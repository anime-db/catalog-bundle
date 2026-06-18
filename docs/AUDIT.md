---
title: Аудит Anime DB Catalog Bundle — сводка
tags: [project/anime-db, doc/audit, security, summary]
created: 2026-06-18
---

# Аудит Anime DB Catalog Bundle — сводный отчёт

> Главный сводный документ аудита. Детали разнесены по: [PROJECT.md](PROJECT.md) (структура) · [TECHNICAL.md](TECHNICAL.md) (техника) · [BUGS.md](BUGS.md) (дефекты) · [RECOMMENDATIONS.md](RECOMMENDATIONS.md) (улучшения).

## Резюме

`anime-db/catalog-bundle` — зрелый, аккуратно структурированный Symfony-бандл эпохи PHP 5.4 / Symfony 2.7, реализующий ядро настольного менеджера коллекции аниме. Архитектура **чистая и идиоматичная**: плагинная расширяемость через DI-теги, разделение на слои, доменная модель с aggregate root `Item`, хорошее unit-покрытие сервисов.

Главная характеристика, определяющая всю оценку: это **локальное однопользовательское приложение на localhost**. Поэтому отсутствие аутентификации — by design, а не дефект. Однако ряд опасных примитивов (shell-exec, мутация composer, скан произвольных путей ФС, удаление по GET) достижимы **неаутентифицированным GET-запросом без CSRF-защиты**, что создаёт реальный вектор **CSRF → RCE/разрушение** через любой веб-контент, открытый пользователем при работающем приложении.

### Оценка по областям

| Область         | Оценка    | Комментарий                                                 |
|-----------------|-----------|-------------------------------------------------------------|
| Архитектура     | 🟢 хорошо | Чистые слои, плагинная система, разумный DI                 |
| Доменная модель | 🟡 средне | Хорошая структура, но рассогласования nullable/cascade      |
| Безопасность    | 🟠 риски  | CSRF на GET-роутах → RCE-вектор; нет escapeshellarg         |
| Качество кода   | 🟡 средне | Идиоматично, но опечатки в API, проглатывание исключений    |
| Тесты           | 🟡 средне | Сервисы покрыты, контроллеры/команды/репозитории — нет      |
| Зависимости     | 🔴 EOL    | PHP 5.4, Symfony 2.7, jQuery 1.x, Twig 1.x — всё EOL        |
| Frontend        | 🟡 средне | Работает, но глобалы, offset-баг, мёртвая сборочная цепочка |

## Модель угроз

- **Развёртывание**: localhost, один пользователь, desktop-обёртка.
- **Доверенная граница**: всё, что приходит по HTTP, считается доверенным (нет auth) — но браузер пользователя может быть обманут сторонним сайтом (CSRF), и любой локальный процесс может постучать на порт.
- **Активы**: коллекция в SQLite, файлы на дисках хранилищ, возможность исполнять команды на машине.
- **Реалистичная атака**: пользователь с открытым приложением заходит на вредоносную страницу → та делает GET на `localhost/.../execute_update.html` или POST с `plugin[install][package]` → исполнение кода.

## Топ находок по безопасности

Подробности и `file:line` — ниже в разделе [Находки по безопасности](#находки-по-безопасности).

| # | Severity (local / network) | Находка                                                               |
|---|----------------------------|-----------------------------------------------------------------------|
| 1 | High / Critical            | Нет auth/authz нигде (мультипликатор всех остальных)                  |
| 2 | High                       | CSRF на деструктивных GET-роутах (delete/scan/execute_update)         |
| 3 | High → RCE                 | Composer add/remove из сырого POST без валидации                      |
| 4 | Medium                     | Скан произвольного пути ФС (storage path не ограничен базовой папкой) |
| 5 | Low (latent)               | `Builder::sort()` конкатенация DQL (безопасно только из-за whitelist) |
| 6 | Low                        | shell-команды через sprintf без `escapeshellarg`                      |

## Топ функциональных багов

| ID                                    | Приоритет | Краткое                                              |
|---------------------------------------|-----------|------------------------------------------------------|
| [B-01](BUGS.md#контроллеры)           | 🔴        | `executeAction` fall-through без `return` (WinXP)    |
| [B-14](BUGS.md#доменная-модель)       | 🔴        | `date_premiere` NOT nullable ≠ миграция DEFAULT NULL |
| [B-08](BUGS.md#поиск)                 | 🟠        | `total` пагинации расходится при genre/label фильтре |
| [B-10](BUGS.md#сканирование--console) | 🟠        | `Progress::__destruct()` пишет `100%` даже при сбое  |
| [B-29](BUGS.md#frontend-js)           | 🟠        | offset скан-лога в JS-длине строки, не байтах        |

Полный список (37 пунктов) — в [BUGS.md](BUGS.md).

## Находки по безопасности

### 1. Полное отсутствие аутентификации/авторизации — High (Critical в сети)
`src/Controller/BaseController.php:18` не добавляет контроля доступа; нет firewall, нет `isGranted`/`@Security`. Все маршруты анонимны, включая деструктивные и shell-исполняющие. Для localhost single-user это намеренная модель (отсюда High, не Critical), но это корневой множитель всех находок ниже.

### 2. CSRF на state-changing GET-роутах — High
Деструктивные действия доступны по **GET без CSRF-токена**:
- `ItemController::deleteAction` — `item_delete` GET удаляет элемент (`ItemController.php:224`)
- `StorageController::deleteAction` — `storage_delete` GET удаляет хранилище (`StorageController.php:132`)
- `StorageController::scanAction` — `storage_scan` GET спавнит фоновый процесс (`StorageController.php:175`)
- `UpdateController::executeAction` — `update_execute` GET запускает `php app/console animedb:update` через shell (`UpdateController.php:122`)

`<img src="http://localhost/.../delete.html">` на любой открытой странице сработает. Формы через Symfony Forms (`storage/item/notice/label/settings`) защищены встроенным CSRF-токеном — но эти raw-GET его обходят.

### 3. Command injection через Update/composer — High → RCE
`src/Controller/UpdateController.php:54-67` берёт сырой POST-массив `plugin` и передаёт `$plugin['delete']`, `$plugin['install']['package']`, `$plugin['install']['version']` напрямую в `removePackage()`/`addPackage()` без whitelist/валидации. В сочетании с отсутствием CSRF (#2) — сильный кандидат на RCE (зависит от экранирования в AppBundle-манипуляторе; контроллер не санитизирует вовсе).

### 4. Скан произвольного пути ФС — Medium
`$storage->getPath()` (свободный текст из формы `entity_storage`) читается командой скана и обходится `Finder`'ом без ограничения базовой директорией (`ScanExecutor.php:72`, `ScanStoragesCommand`). Пользователь (или CSRF-заданное хранилище) может указать `/`, `C:\` и перечислить чувствительные каталоги.

### 5. `Builder::sort()` — латентная DQL-инъекция — Low
`src/Service/Item/Search/Selector/Builder.php:307` конкатенирует column+direction в `orderBy` без экранирования. Безопасно **только** из-за whitelist в `Manager` (`Manager.php:104`). Прямой вызов `Builder::sort()` в обход `Manager` — инъекция. Защита живёт в другом классе.

### 6. shell без `escapeshellarg` — Low
`ScanExecutor::export()` (`ScanExecutor.php:72`) и `executeAction` (`UpdateController.php:131`) собирают команду через `sprintf`/конкатенацию. Интерполируются `$storage->getId()` (int PK) и config-пути — не достижимо инъекцией при нормальных данных, но небезопасно по конструкции.

### Что проверено и чисто
- **SQL-инъекции в LIKE/IN**: параметризованы, id приводятся `(int)` ([TECHNICAL.md](TECHNICAL.md#подсистема-поиска)).
- **XSS**: HTML через Twig (autoescape), JSON через `JsonResponse`/`LogResponse` с `JSON_HEX_*`.
- **SSRF в `Entity\Downloader`**: листенер только перемещает локальные файлы, сетевой fetch — в AppBundle (вне аудита).
- **Path traversal в скан-логах**: имя файла из `\d+`-id и доверенного config-шаблона — обхода нет.

## Состояние зависимостей (критично)

Всё EOL: **PHP 5.4** (с 2015), **Symfony 2.7** (LTS до 2018), **Twig 1.x**, **Doctrine ORM 2.4**, **PHPUnit 4.8**, **jQuery 1.12** (известные XSS CVE). `satooshi/php-coveralls` заброшен, Travis CI (travis-ci.org) и SensioLabs Insight закрыты — вся CI-цепочка мертва, бейджи в README указывают на несуществующие сервисы.

## Рекомендации (топ)

Полный список — в [RECOMMENDATIONS.md](RECOMMENDATIONS.md). Приоритетное:

1. **CSRF-защита** деструктивных GET → перевести на POST + токен (или метод-проверка). [→](RECOMMENDATIONS.md#безопасность)
2. **Валидация composer-пакетов** (whitelist по `vendor/anime-db-*`) перед add/remove.
3. **`escapeshellarg`** на все интерполируемые аргументы команд.
4. **Дублировать sort-whitelist** внутри `Builder::sort()` (defense-in-depth).
5. **Ограничить storage path** базовой директорией / валидировать.
6. Исправить 🔴-баги B-01, B-14.
7. Покрыть тестами контроллеры (особенно scan/install/refill AJAX-потоки).

## Заключение

Для целевого развёртывания (localhost, один пользователь) приложение «приемлемо, но небрежно»: архитектура добротная, но опасные операции открыты по неаутентифицированному GET без CSRF — доминирующий риск это **CSRF-управляемый RCE/разрушение**. При любом выносе за пределы localhost находки #1–#4 становятся Critical. Главный долгосрочный риск — **полный EOL стека зависимостей**.

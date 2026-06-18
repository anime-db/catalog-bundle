---
title: Context — операционный контекст
tags: [memory/repo, context, security, project/anime-db]
created: 2026-06-18
---

# Context — операционный контекст

> Кто вызывает этот код, в какой модели он живёт, какие легаси-решения есть и каков их статус.

## Кто и как это запускает

- **Это бандл, не приложение.** `anime-db/catalog-bundle` (`type: library`) подключается головным пакетом `anime-db/anime-db` и опирается на `anime-db/app-bundle` (планировщик задач, загрузчик файлов, `CommandExecutor`, API-клиент, `CacheTimeKeeper`, `SecretKey`).
- **Запуск — локально, у конечного пользователя.** Desktop-обёртка вокруг Symfony-приложения, слушает localhost. Один пользователь, одна машина.
- **Кто «вызывает сервисы»:** HTTP-запросы из локального браузера пользователя; консольная команда `animedb:scan-storage` (запускается из `ScanExecutor` через self-HTTP и по расписанию `task`-планировщика AppBundle); Doctrine-события (lifecycle-листенеры); composer package-события (`Listener\Package`).
- **БД:** SQLite (миграции написаны под SQLite-идиомы).

## Модель угроз (кратко)

Полный разбор — [../docs/AUDIT.md](../docs/AUDIT.md#модель-угроз). Суть:

- **Граница доверия:** всё пришедшее по HTTP считается доверенным — **аутентификации НЕТ нигде** (by design для localhost single-user). `BaseController` не добавляет контроля доступа, firewall отсутствует.
- **Реальный остаточный риск — CSRF → RCE.** Деструктивные и shell-исполняющие операции открыты по неаутентифицированному **GET без CSRF-токена**:
  - `item_delete`, `storage_delete` — удаление по GET
  - `storage_scan` — спавн фонового процесса по GET
  - `update_execute` — `php app/console animedb:update` через shell по GET
  - `UpdateController::indexAction` — composer add/remove из сырого POST `plugin[]` без валидации (→ RCE-кандидат)
  Вектор: пользователь с открытым приложением заходит на вредоносную страницу → `<img src="http://localhost/.../delete.html">` или авто-POST. Также любой локальный процесс может постучать на порт.
- **Формы через Symfony Forms защищены** встроенным CSRF-токеном (`storage/item/notice/label/settings`). Дыры — именно в raw GET/POST-эндпоинтах в обход форм.
- **Скан читает произвольный путь ФС:** `Storage::$path` не ограничен базовой папкой → можно указать `/`, `C:\` и перечислить ФС.
- **Что проверено и чисто:** SQL-инъекции (параметризовано, id→`(int)`), XSS (Twig autoescape + JSON_HEX), SSRF в `Entity\Downloader` (только перемещает локальные файлы; сетевой fetch — в AppBundle), path-traversal в скан-логах (имя из `\d+`-id).

При любом выносе за localhost находки по auth/CSRF/composer/scan становятся Critical.

## Легаси-решения и их статус

| Решение                                                                                    | Статус / как относиться                                                                                                                                                         |
|--------------------------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| **Весь стек EOL** (PHP 5.4, Symfony 2.7, Twig 1.x, Doctrine 2.4, PHPUnit 4.8, jQuery 1.12) | Осознанный долг. Не добавлять новый код «под старину» без нужды; модернизация — отдельная крупная задача ([RECOMMENDATIONS.md R-22](../docs/RECOMMENDATIONS.md)).               |
| **CI мёртв** (Travis-ci.org, Coveralls, Scrutinizer, SensioLabs Insight закрыты)           | Бейджи в README указывают на несуществующие сервисы. `satooshi/php-coveralls` заброшен. Переезд на GitHub Actions — в рекомендациях.                                            |
| **Закоммиченный JS-dist**                                                                  | Ручной `grunt` + commit после правок JS. Drift-риск. См. [gotchas.md](gotchas.md).                                                                                              |
| **Self-HTTP async через `command_exec`**                                                   | Намеренный способ fire-and-forget на PHP без очередей. Не «странный костыль» — так устроен async скан/обновление.                                                               |
| **`@deprecated Plugin` + наследующие базы**                                                | Депрекейшн неактуализируем (нет замены с дефолтами). Игнорировать как руководство.                                                                                              |
| **Опечатки в публичном API** (`freez`, `getDafeultPlugin`, `data-massage`)                 | Заморожены: исправление = BC-break. Не трогать.                                                                                                                                 |
| **`Progress::__destruct()` пишет 100%**                                                    | Известный баг ([B-10](../docs/BUGS.md)), не фича. Признак завершения — через парсинг лога.                                                                                      |
| **Sample-аниме с тегом `debug: true`**                                                     | `Bakuman/Beck/Gintama/Gto/Hellsing/...` грузятся только в dev-режиме (`InstallItemPass` фильтрует по атрибуту `debug`). Public-набор: FullmetalAlchemist/OnePiece/SpiritedAway. |

## Покрытие тестами (что есть / чего нет)

- **Есть:** `Service`, `Console`, `Entity`, `DependencyInjection`, частично `Event`/`Plugin`.
- **Нет вообще:** `Controller` (11 шт.), `Command`, `Menu`, `Repository`, большая часть `Form`. Миграции исключены намеренно.
- **Следствие:** меняя контроллеры/команды/репозитории — нет сетки безопасности, нужны ручные проверки. `ScanExecutorTest` хрупкий (позиционные `$this->at(N)`).

Карта покрытия — [../docs/TECHNICAL.md](../docs/TECHNICAL.md#тесты-карта-покрытия).

## Ветки

- Активная разработка — `2.x` (текущая). `master` — основная для PR. Snapshot истории см. `git log`.

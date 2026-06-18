---
title: Структура проекта Anime DB Catalog Bundle
tags: [project/anime-db, doc/project, symfony, php]
created: 2026-06-18
---

# Структура проекта — Anime DB Catalog Bundle

> Описание того, как проект устроен внутри: назначение, слои, поток выполнения.
> См. также: [AUDIT.md](AUDIT.md) · [TECHNICAL.md](TECHNICAL.md) · [BUGS.md](BUGS.md) · [RECOMMENDATIONS.md](RECOMMENDATIONS.md)

## Что это такое

`anime-db/catalog-bundle` — Symfony-бандл, образующий **основной UI и доменную логику** настольного приложения для ведения домашней коллекции аниме (Anime DB). Распространяется как Composer-библиотека (`type: library`, GPL-3.0) и подключается головным пакетом `anime-db/anime-db`. Опирается на `anime-db/app-bundle` (инфраструктура: планировщик задач, загрузчик файлов, исполнитель команд, API-клиент).

Приложение **локальное, однопользовательское** — запускается на машине пользователя (обёртка desktop-стиля), слушает localhost. Это объясняет полное отсутствие аутентификации/авторизации и ряд решений, которые в сетевом вебе были бы недопустимы (см. [AUDIT.md](AUDIT.md#модель-угроз)).

- **PHP**: ≥5.4 (фактически EOL)
- **Symfony**: 2.7.x LTS (EOL), Twig 1.33, Doctrine ORM 2.4
- **БД**: SQLite (миграции написаны под SQLite-идиомы)
- **Frontend**: jQuery 1.12 + jquery-form + hinclude, сборка через Grunt
- **Объём**: ~17 800 строк PHP в `src/`

## Карта слоёв

```
HTTP-запрос
  └─ Request listener (locale/install-редирект, priority -500)
       └─ Routing (routing.yml)
            └─ Controller (11 шт., все extends BaseController)
                 ├─ Form (Symfony Forms, entity-bound)
                 ├─ Service (Search, Storage scan, ListControls, TwigExtension)
                 │    └─ Repository (Item / Storage / Label)  →  Doctrine ORM  →  SQLite
                 ├─ Plugin Chains (filler/search/refiller/import/export/item/setting)
                 └─ Event dispatch (Storage scan events, Install events)
                      └─ Listeners (ScanStorage, Install, Package, Entity\Downloader, Entity\Storage)
```

## Доменная модель

Центральная сущность — **`Item`** (единственный настоящий aggregate root). Она *владеет* (composition, `cascade={persist,remove}` + `orphanRemoval`):

- `Name` — альтернативные названия
- `Source` — внешние ссылки-источники
- `Image` — дополнительные изображения

И *ссылается* (ManyToOne / ManyToMany, без orphanRemoval — это справочники):

- `Type` (ManyToOne, строковый PK, i18n через Gedmo)
- `Country` (ManyToOne, строковый PK 2 символа, i18n через personal-translations `country_translation`)
- `Storage` (ManyToOne — хранилище-источник файлов)
- `Studio` (ManyToOne, без перевода)
- `Genre` (ManyToMany, join `items_genres`, i18n через Gedmo)
- `Label` (ManyToMany, join `items_labels`, пользовательские метки)

```
            ┌────── Name (owns)
            ├────── Source (owns)
            ├────── Image (owns)
   Item ────┼── Type ──── ext_translations
            ├── Country ── country_translation
            ├── Storage
            ├── Studio
            ├── Genre[] ── ext_translations
            └── Label[]
```

**DTO-сущности без ORM-маппинга**: `Search` (критерии поиска), `SearchFiller` (URL + резолвинг плагина), `Settings\General` (настройки), `Widget\{Item,Genre,Type}` (view-модели для внешних каталогов).

Подробности полей, связей и lifecycle-callbacks — в [TECHNICAL.md](TECHNICAL.md#доменная-модель-детали).

## Система плагинов

Главный механизм расширяемости. Реализован как **реестр** (паттерн «коллекция плагинов по имени», не chain-of-responsibility). Сторонний бандл регистрирует сервис с DI-тегом, а compiler-pass `PluginPass` на этапе компиляции контейнера собирает все тегированные сервисы в соответствующую цепочку `Chain` через `addPlugin()`.

| Категория   | Тег                 | Сервис Chain                  | Назначение                                                |
|-------------|---------------------|-------------------------------|-----------------------------------------------------------|
| filler      | `anime_db.filler`   | `anime_db.plugin.filler`      | Создать `Item` по URL/форме (скрапинг внешнего источника) |
| search_fill | `anime_db.search`   | `anime_db.plugin.search_fill` | Поиск по имени во внешнем источнике → список результатов  |
| refiller    | `anime_db.refiller` | `anime_db.plugin.refiller`    | Дозаполнение отдельных полей существующего `Item`         |
| import      | `anime_db.import`   | `anime_db.plugin.import`      | Массовый импорт `Item[]` из файла/формата                 |
| export      | `anime_db.export`   | `anime_db.plugin.export`      | Экспорт каталога (маркерный интерфейс)                    |
| item        | `anime_db.item`     | `anime_db.plugin.item`        | Расширения контекстного меню элемента                     |
| setting     | `anime_db.setting`  | `anime_db.plugin.setting`     | Экраны настроек (маркерный интерфейс)                     |

Отдельный механизм — **`InstallItemPass`** + тег `anime_db.install_item` — наполняет цепочку sample-аниме для мастера первой установки (с поддержкой атрибута `debug: true` для скрытия записей вне dev-режима).

Детали контрактов и архитектурные замечания — в [TECHNICAL.md](TECHNICAL.md#система-плагинов-детали).

## Поток сканирования хранилища (async)

Ключевая фоновая функция — сканирование папки с файлами и автодобавление аниме:

1. `StorageController::scanAction` → `ScanExecutor::export($storage)`
2. `ScanExecutor` создаёт лог-файлы `logs/scan_storage/{id}-output.log` и `{id}-progress.log`, затем запускает **фоновую** shell-команду через `CommandExecutor::send()` (которая POST-ит команду на локальный HTTP-эндпоинт `command_exec` и исполняет в отдельном запросе):
   `php app/console animedb:scan-storage --no-ansi --force --export={progress} {id} >{output} 2>&1`
3. `ScanStoragesCommand` обходит файлы (`Finder`, depth 0), и для каждого диспатчит событие:
   - неизвестный файл → `DETECTED_NEW_FILES` (с очищенным через `FilenameCleaner` именем)
   - изменённый существующий → `UPDATE_ITEM_FILES`
   - после цикла, пропавшие → `DELETE_ITEM_FILES`
4. Слушатель `ScanStorage` пытается резолвить аниме через fill-плагины, при успехе диспатчит `ADD_NEW_ITEM` → запись в БД + создание `Notice`.
5. Браузер опрашивает `scan/output.json` (инкрементальный «хвост» лога через `LogResponse`) и `scan/progress.json` (строка `N%`), пока не увидит признак завершения.

Полная диаграмма состояний и разбор Console Output/Progress-декораторов — в [TECHNICAL.md](TECHNICAL.md#подсистема-сканирования).

## Мастер установки (5 шагов)

`InstallController` ведёт пользователя через: (1) выбор локали + поиска по умолчанию → (2) добавление хранилища (предзаполнено домашней папкой) → (3) «что вам нужно» (диспатч `INSTALL_SAMPLES`) → (4) сканирование хранилища → (5) финал (диспатч `INSTALL_APP`, установка `installed=true`, генерация `secret`, очистка кэша). Каждый шаг защищён проверкой параметра `anime_db.catalog.installed`.

Пока локаль не задана, `Request`-listener редиректит **любой** маршрут (кроме `install*`) на мастер установки.

## Конфигурация (DI)

| Файл                     | Содержит                                                       |
|--------------------------|----------------------------------------------------------------|
| `services.yml`           | Twig-расширение, ListControls, Menu\Builder; импорт остальных  |
| `services/plugins.yml`   | 7 сервисов-цепочек плагинов                                    |
| `services/listeners.yml` | event-listener'ы (scan, package, downloader, storage, install) |
| `services/storage.yml`   | `ScanExecutor`, `FilenameCleaner`                              |
| `services/search.yml`    | `Manager`, `Selector`, драйвер `SqlLike`                       |
| `services/install.yml`   | sample-аниме (public + debug)                                  |
| `services/forms.yml`     | формы                                                          |
| `routing.yml`            | все маршруты                                                   |
| `parameters.yml`         | пути лог-файлов скана, `installed`, драйвер поиска             |
| `config.yml`             | imagine-фильтры, assetic, twig-globals                         |

## Frontend

ES5-модули (глобалы на `window`), собираются Grunt'ом (`sass → concat → cssmin → uglify`) в **закоммиченный** `dist/main.js` + `main.min.js`. Ключевые модули: `progress_bar.js`/`progress_log.js` (опрос скана), `popup.js`/`cap.js` (модальные окна), `collection.js` (Symfony collection-type), `refill.js` (дозаполнение через плагины), `image.js`/`local_path.js` (загрузка через jquery-form). SCSS разбит на три яруса: `mixins/base` → `form/*` → `page/*`.

## Тесты

41 файл PHPUnit 4.8, чистые unit-тесты на моках (без загрузки ядра). Хорошо покрыты `Service`, `Console`, `Entity`, `DependencyInjection`. **Не покрыты вообще**: весь `Controller` (11 шт.), `Command`, `Menu`, `Repository`, большая часть `Form`. Миграции исключены намеренно.

Карта покрытия — в [TECHNICAL.md](TECHNICAL.md#тесты-карта-покрытия).

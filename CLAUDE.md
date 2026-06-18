# CLAUDE.md

Этот файл даёт указания Claude Code (claude.ai/code) при работе с кодом в этом репозитории.

## Что это

Symfony-бандл (`anime-db/catalog-bundle`) — основной UI и доменная логика менеджера домашней коллекции аниме. Распространяется как Composer-библиотека, подключается из `anime-db/anime-db`. PHP ≥5.4, Symfony 2.7, Doctrine ORM, SQLite. **Локальное однопользовательское приложение на localhost** (отсюда — отсутствие аутентификации by design).

## Документация (docs/)

Глубокая документация и аудит — в [`docs/`](docs/). Начинать отсюда при незнакомой задаче:

- [docs/PROJECT.md](docs/PROJECT.md) — структура проекта, карта слоёв, доменная модель, потоки
- [docs/TECHNICAL.md](docs/TECHNICAL.md) — техническая глубина: маппинги, контракты, алгоритмы (`file:line`)
- [docs/AUDIT.md](docs/AUDIT.md) — сводный аудит + находки по безопасности
- [docs/BUGS.md](docs/BUGS.md) — 37 найденных багов с приоритетами и фиксами
- [docs/RECOMMENDATIONS.md](docs/RECOMMENDATIONS.md) — улучшения поддерживаемости и безопасности

## Команды

```bash
# PHP-тесты
./vendor/bin/phpunit                              # все тесты
./vendor/bin/phpunit tests/Service/Storage/      # отдельная директория
./vendor/bin/phpunit --filter ScanExecutorTest   # отдельный класс теста

# Frontend-ассеты (результат в src/Resources/public/js/dist/ и src/Resources/public/css/)
npm install
grunt           # sass → concat → cssmin → uglify
```

## Архитектура

### Система плагинов

Основной механизм расширяемости. Плагины регистрируются как DI-сервисы Symfony с тегом, специфичным для типа. Compiler pass `PluginPass` подключает все тегированные сервисы в соответствующий сервис `Chain` через `addPlugin()`.

| Тег                 | Сервис Chain                  | Базовый класс плагина           |
|---------------------|-------------------------------|---------------------------------|
| `anime_db.filler`   | `anime_db.plugin.filler`      | `Plugin\Fill\Filler\Filler`     |
| `anime_db.search`   | `anime_db.plugin.search_fill` | `Plugin\Fill\Search\Search`     |
| `anime_db.refiller` | `anime_db.plugin.refiller`    | `Plugin\Fill\Refiller\Refiller` |
| `anime_db.import`   | `anime_db.plugin.import`      | `Plugin\Import\Import`          |
| `anime_db.export`   | `anime_db.plugin.export`      | `Plugin\Export\Export`          |
| `anime_db.item`     | `anime_db.plugin.item`        | `Plugin\Item\Item`              |
| `anime_db.setting`  | `anime_db.plugin.setting`     | `Plugin\Setting\Setting`        |

`Plugin\Chain` (абстрактный базовый класс): хранит плагины по ключу-имени, отсортированные по алфавиту.

### Сканирование хранилища

`Service\Storage\ScanExecutor::export()` запускает сканирование как **фоновый shell-процесс** через `AppBundle\Service\CommandExecutor::send()`. Прогресс пишется в файл по пути, форматируемому как `sprintf($this->progress, $storage->getId())` (напр. `%s` → `/path/to/{id}.progress`). Frontend опрашивает прогресс через `lib/progress_bar.js`.

### Поиск элементов

`Service\Item\Search\Manager` делегирует работу `DriverInterface`. Единственный встроенный драйвер — `Driver\SqlLike`. `Selector` строит объект запроса; `Selector\Builder` формирует критерии фильтрации из сущности `Search`.

### Поток событий

- **События хранилища** (`Event\Storage\`): `DetectedNewFiles` → `AddNewItem` / `UpdateItemFiles` / `DeleteItemFiles`
- **События установки** (`Event\Install\`): `App` и `Samples` срабатывают при первичной настройке; `Event\Listener\Install\Item\Chain` загружает примеры записей аниме
- **Слушатели сущностей** (`Event\Listener\Entity\`): `Downloader` отвечает за загрузку изображений, `Storage` — за разрешение путей

### Сборка frontend

Конвейер Grunt: SCSS (`src/Resources/public/sass/main.scss`) → развёрнутый CSS → конкатенация с vendor-CSS → минификация. JS-исходники в `src/Resources/public/js/src/` (разделены на `form/` и `lib/`) → конкатенация → минификация в `js/dist/main.min.js`.

## Границы

### ОБЯЗАТЕЛЬНО
- Сохранять совместимость с PHP 5.4 (без короткого синтаксиса массивов в сигнатурах функций, без `::class`, без скалярных type hints).
- Регистрировать новые типы плагинов через DI-теги + `PluginPass` — не подключать плагины вручную.

### НЕЛЬЗЯ
- Пропускать `PluginInterface` — все плагины обязаны его реализовывать (даёт `getName()` / `getTitle()`).
- Писать проценты прогресса в любом формате, кроме `N%` — JS-парсер и `Console\Progress\Export` ожидают именно этот формат.

## Подводные камни (gotchas)

- **`dist/main.js` и `main.min.js` закоммичены** — после правки `src/Resources/public/js/src/**` нужно вручную пересобрать (`grunt`) и закоммитить dist, иначе фронтенд разойдётся с исходником. CSS-dist при этом НЕ коммитится (политика артефактов несогласованна).
- **Опечатки в публичном API менять нельзя** (BC-break): `Item::freez()`, `Search\Chain::getDafeultPlugin()`, JS `data-massage`. Они load-bearing.
- **`Builder::sort()` конкатенирует колонку в DQL** — безопасность держится на whitelist в `Manager::$sort_columns`. Не вызывать `Builder::sort()` в обход `Manager`.
- **`Progress\Export::__destruct()` пишет `100%` безусловно** — прогресс-файл всегда покажет 100% при teardown, даже если скан упал. Признак завершения детектится отдельно по содержимому лога (`StorageController::isEndOfLog`).
- **Деструктивные действия — по GET без CSRF** (`*/delete.html`, `*/scan.html`, `execute_update.html`). Не добавлять новые такие маршруты; см. [docs/AUDIT.md](docs/AUDIT.md#находки-по-безопасности).
- **`date_premiere`**: маппинг говорит NOT nullable, но миграция сделала колонку `DEFAULT NULL` — рассогласование ([B-14](docs/BUGS.md#доменная-модель)).
- **Тесты на моках без загрузки ядра**; контроллеры/команды/репозитории НЕ покрыты. `ScanExecutorTest` использует хрупкие позиционные `$this->at(N)`.
- **Миграции — под SQLite**: изменение колонки = полная пересборка таблицы (temp `_new` → copy → rename → drop). Некоторые `down()` необратимы (напр. `ChangeImagePaths`).

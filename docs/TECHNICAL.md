---
title: Техническая документация Anime DB Catalog Bundle
tags: [project/anime-db, doc/technical, symfony, doctrine, php]
created: 2026-06-18
---

# Техническая документация — Anime DB Catalog Bundle

> Глубокая техническая информация: маппинги, контракты, алгоритмы, ссылки на код `file:line`.
> См. также: [PROJECT.md](PROJECT.md) · [AUDIT.md](AUDIT.md) · [BUGS.md](BUGS.md) · [RECOMMENDATIONS.md](RECOMMENDATIONS.md)

## Доменная модель (детали)

### Item — `src/Entity/Item.php` (aggregate root)

Расширяет `AppBundle\...\BaseEntity`, реализует `ImageInterface` (обложка через `getFilename`/`setFilename`). `@HasLifecycleCallbacks`.

Поля: `id` (auto int), `name` (string 256, NotBlank), `date_premiere` (date, **в маппинге NOT nullable** — `Item.php:66`, см. рассогласование ниже), `date_end` (date nullable), `duration` (int nullable, PHP-дефолт 0), `summary`/`episodes`/`file_info` (text nullable), `path` (string 256 nullable), `episodes_number` (string 5, regex), `rating` (int nullable, дефолт 0), `cover`, `date_add`/`date_update` (datetime NOT null). Транзиентное поле `not_cleared_path` (`Item.php:225`).

Связи:
- `names` OneToMany→`Name` — cascade persist+remove, orphanRemoval (`Item.php:51`)
- `sources` OneToMany→`Source` — cascade persist+remove, orphanRemoval (`Item.php:159`)
- `images` OneToMany→`Image` — cascade persist+remove, orphanRemoval (`Item.php:200`)
- `type` ManyToOne→`Type` — cascade persist (`Item.php:58`)
- `country` ManyToOne→`Country` — cascade persist (`Item.php:98`)
- `storage` ManyToOne→`Storage` — cascade persist (`Item.php:128`)
- `studio` ManyToOne→`Studio` — cascade persist (`Item.php:215`)
- `genres` ManyToMany→`Genre` — owning, join `items_genres`, cascade persist (`Item.php:82`)
- `labels` ManyToMany→`Label` — owning, join `items_labels`, cascade persist (`Item.php:90`)

Lifecycle:
- `doChangeDateUpdate` `@PreUpdate` (`Item.php:896`) — обновляет `date_update`
- `doClearPath` `@PrePersist`+`@PreUpdate` (`Item.php:946`) — срезает префикс пути хранилища из `not_cleared_path` в `path`
- `isPathValid` валидатор (`Item.php:908`)
- `freez()` (`Item.php:922`) — заменяет связанные Type/Genre на managed-ссылки `$em->getReference(...)` (используется при разрешении дубликатов из сессии)

### Справочные сущности

| Сущность             | Файл                            | PK         | i18n                           | Особое                                       |
|----------------------|---------------------------------|------------|--------------------------------|----------------------------------------------|
| `Name`               | `Entity/Name.php`               | int        | —                              | bidirectional `setItem`                      |
| `Source`             | `Entity/Source.php`             | int        | —                              | `url` Url-валидация, индекс `source_url_idx` |
| `Image`              | `Entity/Image.php`              | int        | —                              | `source` = и значение, и имя файла           |
| `Storage`            | `Entity/Storage.php`            | int        | —                              | свой repo + lifecycle, `old_paths` транзиент |
| `Label`              | `Entity/Label.php`              | int        | —                              | свой repo, ManyToMany inverse                |
| `Genre`              | `Entity/Genre.php`              | int        | Gedmo `ext_translations`       | Translatable                                 |
| `Country`            | `Entity/Country.php`            | string(2)  | personal `country_translation` | дефолт id=`0` (рассогласование типа)         |
| `CountryTranslation` | `Entity/CountryTranslation.php` | int        | —                              | extends Gedmo AbstractPersonalTranslation    |
| `Studio`             | `Entity/Studio.php`             | int        | —                              | без перевода                                 |
| `Type`               | `Entity/Type.php`               | string(16) | Gedmo `ext_translations`       | строковый PK без генератора                  |

`Storage` имеет типы FOLDER / EXTERNAL / EXTERNAL_R / VIDEO; `OneToMany→Item` **без cascade** (`Storage.php:118`) — удаление хранилища не каскадирует на элементы.

### Репозитории

- **`Repository\Item`** (`src/Repository/Item.php`): `count()`, `getList($limit,$offset)` (offset-пагинация, ORDER BY id DESC), `findDuplicate(Item)` (две DQL-выборки по именам + слияние с дедупом, O(n²)), `getLastUpdate($id)`.
- **`Repository\Storage`**: `count()`, `getList(array $types)`, `getLastUpdate()`, `getLast()`.
- **`Repository\Label`**: `updateListLabels(ArrayCollection)` — diff-синхронизация набора меток с `flush()` **внутри репозитория** (доменная запись не на своём месте — `Label.php:25`).

## Система плагинов (детали)

### Контракты

- **`PluginInterface`** (`Plugin/PluginInterface.php:15`): `getName()` (ключ), `getTitle()` (метка).
- **`PluginInMenuInterface`** (`Plugin/PluginInMenuInterface.php:17`): + `buildMenu(ItemInterface $item)`.
- **`Plugin`** базовый класс (`Plugin/Plugin.php`) — **помечен `@deprecated`**, но все конкретные базы его наследуют.
- **`Chain`** абстрактный (`Plugin/Chain.php:17`) — два параллельных map'а `$plugins[name]` и `$titles[name]`; `addPlugin()` делает `ksort` на каждой вставке (`Chain.php:36`); `getPlugin($name)` возвращает `null` при отсутствии.

### Регистрация

`PluginPass::process()` (`DependencyInjection/Compiler/PluginPass.php:26`) семь раз вызывает `compilerChain(chain_service, tag)`; для каждого тегированного сервиса добавляет `addPlugin(Reference)` в определение цепочки. Если сервис-цепочка отсутствует — тихий no-op (`:44`).

`InstallItemPass` (`Compiler/InstallItemPass.php:28`): ранний выход если `installed` или нет сервиса; для каждого `anime_db.install_item` читает `$attributes[0]['debug']` и кладёт в `addDebugItem` или `addPublicItem`.

### Особенности категорий

- **filler** (`FillerInterface`): «толстый» интерфейс — `fill(array): Item`, `getForm()`, `setRouter(Router)`, `getLinkForFill`, `fillFromSearchResult`, `isSupportedUrl`. База `Filler` даёт дефолты.
- **search_fill** (`SearchInterface`): `search()`, filler-обвязка, `getCatalogItem()` (автозаполнение при единственном результате, **глотает все исключения** — `Search.php:130`). Цепочка `Search\Chain` принимает `default_search`, метод `getDafeultPlugin()` (опечатка в публичном API — `Chain.php:37`).
- **refiller** (`RefillerInterface extends PluginInterface`, не InMenu): 14 констант-полей, `isCanRefill/refill/isCanSearch/search/refillFromSearchResult`. `Refiller\Chain::getPluginsThatCanFillItem()`.
- **import / export / item / setting**: `export`, `item`, `setting` — фактически маркерные (пустые интерфейсы/цепочки). `ItemInterface::buildMenu($node, $item)` имеет **другую сигнатуру** (2 аргумента), несовместимую с `PluginInMenuInterface`.

Архитектурные слабости (null vs exception, опечатки, framework-coupling, отсутствие приоритетов) — в [BUGS.md](BUGS.md#система-плагинов) и [RECOMMENDATIONS.md](RECOMMENDATIONS.md#система-плагинов).

## Подсистема поиска

Поток: `Manager::search()` (`Service/Item/Search/Manager.php:72`) валидирует сортировку по whitelist'ам и делегирует в `DriverInterface`. Единственная реализация — `Driver/SqlLike` (`SqlLike.php`). Та берёт `Builder` из `Selector::create()`, применяет цепочку предикатов `addX()` и выполняет два запроса: выборку страницы и `COUNT(DISTINCT i)`.

**Построение запроса (`Builder`)**: держит два QueryBuilder'а (`select`, `total`); каждый `addX()` оборачивает предикат в `\Closure` и применяет к обоим (`Builder::add()` — `Builder.php:265`), чтобы запросы были синхронны.

**Whitelist сортировки** (`Manager.php:39`): `name, date_update, rating, date_premiere, date_end`; направление `DESC|ASC`. `getValidSortColumn/Direction` (`Manager.php:104`) делают `in_array` с фолбэком на дефолт.

**Анализ инъекций**:
- LIKE-паттерны — безопасны: `addName()` (`Builder.php:71`) приводит к нижнему регистру, схлопывает `%` и биндит через `setParameter`.
- Genre/label IN() — безопасны: id приводятся `(int)` (`Builder.php:206,229`).
- **Сортировка — `orderBy('i.'.$column, $direction)`** (`Builder.php:307`) — конкатенация column И direction в DQL **без экранирования**. Безопасно **только** потому, что `Manager` всегда передаёт значения из whitelist. Это latent-footgun: безопасность живёт в другом классе. См. [BUGS.md](BUGS.md#поиск) / [RECOMMENDATIONS.md](RECOMMENDATIONS.md#безопасность).
- Баг функциональный: `andHaving(COUNT = N)` для genre/label добавляется только к `select`, не к `total` (`Builder.php:211,236`) → `total` расходится при фильтре по жанрам/меткам.

## Подсистема сканирования

### Жизненный цикл

```
StorageController::scanAction (или InstallController::scanAction)
  → ScanExecutor::export()  [ScanExecutor.php:62]
       создаёт {id}-output.log, {id}-progress.log (='0%')
       CommandExecutor::send("php app/console animedb:scan-storage --no-ansi --force --export=PROG ID >OUT 2>&1")
         → urlencode + POST на локальный route command_exec (fsockopen) → exec в отдельном запросе
            → ScanStoragesCommand [Command/ScanStoragesCommand.php:124]
                 Finder depth 0 по storage->getPath()
                 для каждого файла:
                   isAllowFile? нет → skip
                   существует и mtime новее → dispatch UPDATE_ITEM_FILES
                   неизвестен → FilenameCleaner → dispatch DETECTED_NEW_FILES
                 после: пропавшие → dispatch DELETE_ITEM_FILES
                 progress->advance() на каждом шаге
            → ScanStorage listener [Event/Listener/ScanStorage.php]
                 onDetectedNewFilesTryAdd: fill-плагины резолвят → ADD_NEW_ITEM (stopPropagation)
                 onAddNewItemPersistIt: persist + flush
                 onXSendNotice: рендер Twig → Notice-сущность
браузер:
  опрос scan/output.json (LogResponse инкрементальный хвост)
  опрос scan/progress.json ("N%")
  при isEndOfLog() → ScanExecutor::forceStopScan() пишет "100%"
```

### Console Output / Progress декораторы

- `Console\Output\Decorator` — базовый pass-through `OutputInterface`.
- `Console\Output\LazyWrite` — буферизует `write/writeln` в стек, флашит `writeAll()` (чтобы перерисовка прогресс-бара не мешалась с лог-строками).
- `Console\Output\Export` — зеркалирует вывод в файл, `strip_tags` сообщения (`Output/Export.php:84`); при `$append=false` делает `rewind+ftruncate` перед записью (для progress-файла — всегда одно текущее значение).
- `Console\Progress\Export` — оборачивает `ProgressHelper`, на `advance()` пишет `floor(current/max*100)."%"`; **`__destruct()` безусловно пишет `"100%"`** (`Progress/Export.php:88`) — маскирует аварийное завершение.
- `Console\Progress\PresetOutput` — адаптер, запоминающий `OutputInterface`.

### Признак завершения

`StorageController::isEndOfLog()` (`StorageController.php:216`) ищет в логе `\nTime: \d+ s.`, Windows-приглашение `root>` или `Fatal error:` (через `!= false` вместо `!== false` — `:225`). `LogResponse::logOffset()` (`Scan/LogResponse.php:27`) отдаёт `mb_substr($log, $offset, ...)` + новый offset + флаг `end`, JSON с `JSON_UNESCAPED_UNICODE`.

### FilenameCleaner

`Service/Storage/FilenameCleaner.php` превращает имя медиа-файла в заголовок для поиска: убирает контрол-символы и `_`, вырезает `[...]`/`(...)`, итеративно удаляет release/quality-теги (`DVDRip`, `1080p`, ... — `preg_quote` в конструкторе `:79`), схлопывает пробелы, тримит пунктуацию.

## Сущностные слушатели

- **`Entity\Downloader`** (`Event/Listener/Entity/Downloader.php`) — Doctrine `prePersist`. Не качает из сети (это в AppBundle), а **перемещает** уже скачанный файл: при наличии подстроки `tmp` в имени (`strpos(...,'tmp') !== false` — `:66`, переборчивая эвристика) копирует в дату-структуру `Y/m/d/His/`. SSRF здесь нет.
- **`Entity\Storage`** (`Event/Listener/Entity/Storage.php`) — поддерживает файл-маркер `.storage` с id хранилища: `postPersist` пишет (mode **0666** — `:53`), `postRemove` удаляет если содержимое совпадает, `postUpdate` чистит `old_paths` и переписывает. Дублирует логику `ScanStoragesCommand::checkStorageId()` с другими правами.
- **`Listener\Install`** (`Event/Listener/Install.php`): `onInstallApp` создаёт метки, ставит `installed=true`, генерит `secret` (`:156`), чистит кэш; `onInstallSamples` создаёт метку «Sample» и sample-аниме, копируя обложки из `Resources/private/images/` в `web/media/`.
- **`Listener\Package`** (`Event/Listener/Package.php`): на install/update пакета копирует Twig-шаблоны ошибок из ресурсов бандла (`copyTemplates()` — `:71`).
- **`Listener\Request`** (`Event/Listener/Request.php`): на `kernel.request` при пустой локали редиректит на `install` (кроме самих install-маршрутов).

## TwigExtension

`Service/TwigExtension.php` экспонирует ровно **один** фильтр `dummy` (через legacy `\Twig_Filter_Method`): `dummy($path, $filter)` возвращает `$path` либо плейсхолдер `/bundles/animedbcatalog/images/dummy/{filter}.jpg`. Никаких функций/глобалов. API устаревший (Twig 1.x).

## Frontend (детали)

### JS-модули

`lib/`: `cap.js` (оверлей-синглтон), `popup.js`+`PopupContainer` (модалки с кэшем и abort in-flight XHR), `block_load.js` (observer для переинициализации форм в динамическом DOM), `notice_container.js`, `progress_bar.js`/`progress_log.js` (опрос скана, expire 2 мин), `update_log.js` (опрос self-update), `trans.js`, `confirm_delete.js`, `keep_hover.js`, `toggle.js`.

`form/`: `collection.js` (Symfony collection add/remove по `data-prototype`), `refill.js` (дозаполнение из внешнего источника — POST формы в lazyload-popup), `image.js` (загрузка через jquery-form `ajaxSubmit`), `local_path.js` (браузер папок ФС с `data-root`), `storage.js` (AJAX-проверка required-пути), `notice.js`, `check_all.js` (мастер-чекбокс таблицы).

`main.js` — единственный bootstrap (`$(function(){...})`): инициализирует Cap/PopupContainer, регистрирует `BlockLoadHandler`, инстанцирует контроллеры сканом DOM по маркер-классам/`data-`-атрибутам.

### Сборка

Grunt: `sass(expanded) → concat → cssmin → uglify(drop_console)`. Порядок concat важен (всё на глобалах, `main.js` последним). **`dist/main.js` и `main.min.js` закоммичены** (мин. от 2017-06-15), CSS-dist — нет (несогласованная политика артефактов).

### SCSS

`main.scss` (`@charset UTF-8`) импортирует: `mixins → base → form/* → logo/layout → page/* → прочие top-level`. Три яруса: примитивы → виджеты форм → лейауты страниц.

## Тесты (карта покрытия)

Bootstrap `tests/bootstrap.php` лишь подключает `vendor/autoload.php` (без ядра/фикстур). 41 файл, чистые unit на моках.

| Namespace                   | Покрытие                                                       |
|-----------------------------|----------------------------------------------------------------|
| `Console` (Output/Progress) | ✅ полностью                                                    |
| `DependencyInjection`       | ✅                                                              |
| `Entity` (+Widget/Settings) | ✅ в основном (Item, Storage, Search, Country, Widget...)       |
| `Event`                     | 🟡 частично (8 тестов из ~28 файлов)                           |
| `Service`                   | ✅ (Search Manager/Selector/Builder/SqlLike, ListControls, ...) |
| `Plugin`                    | 🟡 частично (Chain, Filler, Refiller, Search, Import)          |
| `Form`                      | 🔴 только `SearchSimpleTest`                                   |
| `Controller`                | 🔴 **нет вообще** (11 контроллеров)                            |
| `Command`                   | 🔴 нет                                                         |
| `Menu`                      | 🔴 нет                                                         |
| `Repository`                | 🔴 нет                                                         |
| `DoctrineMigrations`        | ⬛ исключены намеренно                                          |

Качество: идиоматичный PHPUnit 4 на моках (`getMock`, `expects($this->once())`, `@dataProvider`). Хрупкое место — `ScanExecutorTest:71-78` использует позиционные `$this->at(2)/at(3)` (ломается при перестановке fs-вызовов; уже вызывало churn в истории).

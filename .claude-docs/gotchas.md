---
title: Gotchas — нетривиальные ловушки
tags: [memory/repo, gotcha, project/anime-db]
created: 2026-06-18
---

# Gotchas — подводные камни

> Только нетривиальное: «выглядит понятно, но ломает» или «выглядит странно, но сделано намеренно».
> То, что считывается за 5 секунд из имени файла/класса, сюда не входит.

## Сборка и артефакты

### `dist/main.js` и `main.min.js` закоммичены — CSS-dist нет
После правки `src/Resources/public/js/src/**` фронтенд **не обновится сам**: нужно вручную `grunt` и закоммитить пересобранный `dist/`. JS-артефакт в git, а CSS-dist — нет (его генерят на деплое). Политика несогласованна → правя JS, легко забыть пересобрать и закоммитить, и dist молча разойдётся с исходником. В истории есть отдельные коммиты «update js dist» — это ручной шаг.

### Порядок concat в Gruntfile критичен
Весь JS — глобалы на `window`, без модулей. `main.js` должен идти **последним** в concat (`Gruntfile.js:46`: jquery → hinclude → jquery-form → vendor → form/* → lib/* → main.js). Переставишь — сломается инициализация. `uglify` с `drop_console:true` вырезает `console.*` из прода.

## Опечатки, ставшие частью API (не «исправлять»)

Эти имена выглядят как баг, но менять = BC-break (используются в шаблонах/данных):
- `Item::freez()` (`src/Entity/Item.php:922`) — не «freeze». Заменяет связанные Type/Genre на managed-ссылки `getReference()`.
- `Search\Chain::getDafeultPlugin()` (`src/Plugin/Fill/Search/Chain.php:37`) — не «Default». Публичный метод.
- JS `data-massage` (не «message») — `src/Resources/public/js/src/lib/confirm_delete.js`, читается в `main.js`. Атрибут load-bearing.

## Скан хранилища

### Прогресс-файл ВСЕГДА показывает `100%` в конце — даже при падении
`Console\Progress\Export::__destruct()` (`src/Console/Progress/Export.php:88`) пишет `100%` **безусловно** при разрушении объекта. Поэтому нельзя судить об успехе скана по progress-файлу. Реальный признак завершения/ошибки детектится отдельно — парсингом содержимого output-лога в `StorageController::isEndOfLog()` (ищет `Time: N s.`, Windows-приглашение или `Fatal error:`). Если правишь логику завершения — правь оба места.

### Скан запускается не напрямую, а через self-HTTP-POST
`ScanExecutor::export()` не делает `exec()` сам. Он отдаёт команду в `CommandExecutor::send()` (AppBundle), который **urlencode-ит команду и POST-ит её на локальный route `command_exec`** через `fsockopen` — приложение просит само себя выполнить команду в отдельном HTTP-запросе (fire-and-forget async). Если ищешь «где же реально запускается скан» — он в обработчике `command_exec`, не в `ScanExecutor`.

### `%N%`-формат прогресса — контракт между PHP и JS
Прогресс пишется строкой вида `42%`. Этот формат парсят И `Console\Progress\Export`, И JS-поллер `lib/progress_bar.js`. Любой другой формат сломает прогресс-бар. Не «улучшать» формат.

### offset в опросе лога считается в JS-длине строки, не в байтах
`lib/progress_log.js:37` шлёт серверу `offset += data.content.length` (UTF-16 code units), а сервер режет лог через `mb_substr` и отдаёт с `JSON_UNESCAPED_UNICODE`. На не-ASCII названиях (а это аниме — их много) offset расходится → дубли/обрезка кусков лога. Выглядит как «случайный глюк лога», на деле — рассинхрон единиц offset. (см. [BUGS.md B-29](../docs/BUGS.md))

## Доменная модель

### `getPath()` ≠ то, что лежит в колонке `path`
В `Item` колонка `path` хранит путь **без** префикса хранилища, а `getPath()` возвращает путь **с** префиксом. Срезание делает lifecycle-callback `doClearPath` (`@PrePersist`+`@PreUpdate`). Есть транзиентное поле `not_cleared_path`: если установить `path` до привязки `storage`, поведение зависит от порядка вызовов сеттеров. То есть «сохранил путь — прочитал другой» здесь нормально, а не баг.

### `date_premiere`: маппинг говорит NOT nullable, колонка — DEFAULT NULL
Рассинхрон маппинга и реальной SQLite-схемы (миграция #9 сделала колонку nullable, маппинг — нет). На свежей схеме сохранение `Item` с null-премьерой может нарушить маппинг. Не считать маппинг источником истины по nullable — сверяться с миграциями. (см. [BUGS.md B-14](../docs/BUGS.md))

### Миграции — под SQLite: изменение колонки = пересборка всей таблицы
Идиома «temp `_new` → INSERT…SELECT → rename → drop» повторяется ~8 раз. Это не over-engineering, а ограничение SQLite (нет полноценного ALTER). Часть `down()` намеренно необратима (напр. `ChangeImagePaths` делает `File::move()` физических файлов и `down()` = no-op). Не жди, что миграции откатываются чисто.

## Плагины

### `getPlugin($name)` возвращает `null` молча
`Plugin\Chain::getPlugin()` (`src/Plugin/Chain.php:45`) при неизвестном имени возвращает `null`, не бросает. А `Filler::getLinkForFill` в похожей ситуации бросает `LogicException`. Контракт ошибок в подсистеме несогласован — не полагайся на единое поведение. Коллизия имён плагинов (`getName()`) тоже молча перетирает (last-writer-wins).

### Порядок плагинов — алфавитный, не по priority
`Chain` сортирует `ksort`-ом по имени. Это НЕ стандартный Symfony-`priority` в тегах. Хочешь повлиять на порядок в меню/цепочке — только переименованием. Авторы плагинов часто ждут `priority` — здесь его нет.

### `ItemInterface::buildMenu()` имеет ДРУГУЮ сигнатуру
У большинства in-menu плагинов `buildMenu($item)` (1 арг, `PluginInMenuInterface`). У `Plugin\Item\ItemInterface::buildMenu($node, $item)` — **2 аргумента**. Поэтому `Item`-плагины не реализуют `PluginInMenuInterface`. Обобщённый код по меню обязан спец-кейсить категорию item.

### Все базовые классы плагинов наследуют `@deprecated Plugin`
`Plugin\Plugin` помечен `@deprecated use PluginInterface`, но `Filler/Search/Import/Export/Item/Setting/Refiller` его наследуют. Совет «реализуй интерфейс напрямую» нерабочий — без базы не получишь дефолтов. Депрекейшн фактически неактуализируем, игнорируй его как руководство.

## Установка

### Пока локаль пуста — ВСЁ редиректит на /install
`Event\Listener\Request` при пустой локали редиректит любой маршрут (кроме `install*`) на мастер установки. Если «приложение упорно открывает установку» — проверь параметр локали, а не роутинг.

### `secret` перегенерируется при каждом прогоне установки
`onInstallApp` (`Event/Listener/Install.php:156`) каждый раз вызывает `SecretKey::generate()`. Повторный запуск установки ротирует Symfony `secret` → инвалидирует CSRF-токены / remember-me. Не «повторно установить для теста» на боевых данных.

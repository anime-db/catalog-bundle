---
title: Найденные баги Anime DB Catalog Bundle
tags: [project/anime-db, doc/bugs, audit]
created: 2026-06-18
---

# Найденные баги и рекомендации по исправлению

> Конкретные дефекты с привязкой `file:line` и предлагаемыми исправлениями. Баги безопасности вынесены в [AUDIT.md](AUDIT.md#находки-по-безопасности); рекомендации по улучшению — в [RECOMMENDATIONS.md](RECOMMENDATIONS.md).
> См. также: [PROJECT.md](PROJECT.md) · [TECHNICAL.md](TECHNICAL.md)

Приоритеты: 🔴 критичный (функция ломается/потеря данных) · 🟠 серьёзный · 🟡 умеренный · ⚪ косметика.

## Контроллеры

### 🔴 B-01. `executeAction` — fall-through без `return` на Windows XP
`src/Controller/UpdateController.php:125-127`. В ветке для Windows XP вызывается `$this->redirect(...)` **без `return`**, выполнение проваливается дальше и обновление запускается всё равно.
**Fix**: `return $this->redirect(...)`.

### 🟠 B-02. `getPathAction` — нет null-проверки + маршрут без требования `id`
`src/Controller/StorageController.php:159-164`. `find('...Storage', $request->get('id'))` может вернуть `null`, далее сразу `$storage->isPathRequired()` → fatal 500. Маршрут `storage_path` (`routing.yml:64`) в отличие от соседних не имеет `requirements: id: \d+`.
**Fix**: проверка `if (!$storage) { return new JsonResponse(...404...); }` + добавить требование `id`.

### 🟡 B-03. Мутация в render-экшене index
`src/Controller/UpdateController.php:54-68`. Composer add/remove (деструктивная операция) выполняется внутри «index/render»-экшена по POST `plugin`. Усложняет рассуждение о CSRF и смешивает ответственности.
**Fix**: вынести в отдельный POST-only экшен с CSRF-токеном.

### 🟡 B-04. Скан запускается до проверки 304
`src/Controller/InstallController.php:178` вызывает `export()` (спавн процесса) **до** раннего возврата `isNotModified` (`:181`). Conditional GET «not modified» всё равно стартует скан.
**Fix**: проверять `isNotModified` до `export()`.

### 🟡 B-05. `isEndOfLog` — `!=` вместо `!==`
`src/Controller/StorageController.php:225`. `strpos(...) != false` — нестрогое сравнение; работает только потому, что совпадение не на offset 0.
**Fix**: `!== false`.

### ⚪ B-06. `mb_strtolower($x, 'UTF8')`
`src/Controller/HomeController.php:136,144,149`. `'UTF8'` — алиас, канонично `'UTF-8'` (как в `LogResponse`). Несогласованность.

### 🟡 B-07. RefillController строит `Item` из сырого запроса без валидации
`src/Controller/RefillController.php:53,80,125`. `createForm('entity_item', new Item())->handleRequest()` затем `->getData()` без проверки `isValid()`; `fillFromSearchAction` передаёт `$request->get('data')` прямо плагину (`:129`). Доверие плагину в санитизации.

## Поиск

### 🟠 B-08. `total` расходится с `select` при фильтре по жанрам/меткам
`src/Service/Item/Search/Selector/Builder.php:211,236`. `andHaving('COUNT(i.id) = N')` добавляется только в `$this->select`, но не в `total`-запрос. Счётчик `total` (для пагинации) завышается при фильтрации по genre/label.
**Fix**: применять having-условие к обоим QueryBuilder'ам (через тот же `add()`-механизм с closure).

### 🟡 B-09. `Builder::sort()` — конкатенация колонки в DQL
`src/Service/Item/Search/Selector/Builder.php:307`. `orderBy('i.'.$column, $direction)` без экранирования. Сейчас безопасно (whitelist в `Manager`), но безопасность вне класса. См. [AUDIT.md](AUDIT.md#находки-по-безопасности).
**Fix**: продублировать whitelist-проверку внутри `sort()`.

## Сканирование / Console

### 🟠 B-10. `Progress\Export::__destruct()` безусловно пишет `100%`
`src/Console/Progress/Export.php:88`. Даже при аварийном (fatal/exception) завершении скана progress-файл при teardown покажет `100%`, маскируя сбой. UI может одновременно показывать «100%» и «Fatal error» в логе.
**Fix**: писать `100%` только при штатном `finish()`, не в деструкторе; или отслеживать флаг успешного завершения.

### 🟡 B-11. `ScanStoragesCommand` — хрупкий парсинг пути
`src/Command/ScanStoragesCommand.php:202`. `list(, $file) = explode('://', $file->getPathname(), 2)` предполагает наличие `://`-префикса; без него `$file` станет `null` (E_NOTICE) → `new SplFileInfo(null)`.
**Fix**: проверять количество элементов / наличие разделителя.

### 🟡 B-12. Несогласованные права файла-маркера `.storage`
`Entity/Storage::postPersist` пишет с mode **0666** (`Event/Listener/Entity/Storage.php:53`), а `ScanStoragesCommand::checkStorageId()` (`:319-326`) — с правами по умолчанию. Один и тот же файл создаётся двумя путями по-разному.
**Fix**: единые права (0644) и единый хелпер.

### 🟡 B-13. `Output\Export` `strip_tags` портит реальные данные
`src/Console/Output/Export.php:84`. `strip_tags` убирает консольные цвет-теги, но заодно вырежет любые `<...>` в настоящем имени/заголовке файла в логе.

## Доменная модель

### 🔴 B-14. Рассогласование nullable у `date_premiere`
`src/Entity/Item.php:66` маппит `date_premiere` как **NOT nullable**, но миграция #9 (`Version20140122122107`) сделала колонку `DEFAULT NULL`, сеттер принимает `null` (`Item.php:271`), геттер возвращает `null` (`:283`), `Search` считает поле nullable. На свежей схеме сохранение `Item` с null-премьерой нарушит маппинг.
**Fix**: добавить `nullable=true` в маппинг `date_premiere`.

### 🟠 B-15. Миграция `RemoveItemPathPrefix` — неопределённая переменная `$images`
`src/DoctrineMigrations/Version20140211104719_RemoveItemPathPrefix.php:37,75`. `skipIf(!($items && $images), ...)` использует неинициализированную `$images` → E_NOTICE. Логика выполняется только в no-data ветке, что обычно маскирует баг.
**Fix**: убрать `$images` из условия или определить.

### 🟠 B-16. `ChangeImagePaths` — необратимая миграция с файловыми операциями
`src/DoctrineMigrations/Version20131015113854_ChangeImagePaths.php`. Делает `File::move()` физических файлов во время миграции БД; `down()` — no-op (`skipIf(true)`) → необратимо. Сбой посреди миграции оставит файлы перемещёнными, а БД — частично обновлённой (нет транзакционности ФС+БД).

### 🟡 B-17. `Item::freez()` — обращение к `getId()` без null-проверки
`src/Entity/Item.php:932`. `$em->getReference(get_class($this->type), $this->type->getId())` при `type === null` (а сеттер допускает null) → NPE.
**Fix**: проверять `if ($this->type)`.

### 🟡 B-18. Дедуп `addName/addSource/addImage` по значению, не по идентичности
`src/Entity/Item.php:459,712,751`. `array_map('strval', ...)` + нестрогий `in_array` — пустая строка при наличии другой пустой молча теряется; нестрогое сравнение даёт ложные совпадения. Несогласованно с `removeName`, который использует `contains`.

### 🟡 B-19. Мутабельные коллекции/даты наружу
`Item.php:486,556,594,739,778` и `Storage::getDateUpdate/getFileModified` (`Storage.php:393,413`) возвращают живой `ArrayCollection`/внутренний `\DateTime` без клонирования — внешний код может мутировать внутреннее состояние в обход bidirectional-методов.

### 🟡 B-20. Отсутствие cascade/ON DELETE для справочников
`Storage::getItems()` (`Storage.php:118`), Country/Type/Studio→Item — без cascade и без DB-level `ON DELETE`. Удаление справочника либо упадёт по FK, либо оставит «висячие» ссылки.

### ⚪ B-21. PHP-дефолты `0` при nullable-колонках
`Item::$duration`, `$rating` имеют PHP-дефолт `0`, хотя колонки nullable — никогда не заданное значение сохранится как `0`, а не `NULL`.

### ⚪ B-22. `Country::$id` дефолт `0`
`src/Entity/Country.php:37`. Целочисленный дефолт на `string(2)` PK с `@Assert\Country`.

### ⚪ B-23. Опечатки в публичном API
`freez()` (`Item.php:922`), `getDafeultPlugin()` (`Plugin/Fill/Search/Chain.php:37`), `data-massage` (`confirm_delete.js`). Уже load-bearing — нельзя исправить без BC-break.

## Система плагинов

### 🟡 B-24. `getPlugin()` возвращает `null` без сигнала
`src/Plugin/Chain.php:45-52`. Нельзя отличить «нет такого плагина» от штатного значения. Несогласованно с `Filler::getLinkForFill`, который бросает `LogicException`. `Search\Chain::getDafeultPlugin()` пробрасывает null без guard'а; при `default_search=null` → `getPlugin(null)`.

### 🟡 B-25. Коллизии имён плагинов молча перетирают
`src/Plugin/Chain.php:34`. Два плагина с одинаковым `getName()` — last-writer-wins, без предупреждения.

### 🟡 B-26. Проглатывание исключений в `Search::getCatalogItem`
`src/Plugin/Fill/Search/Search.php:130-138`. `catch (\Exception $e) {}` отбрасывает все ошибки (сеть/парсинг/БД) без логирования.

### ⚪ B-27. `ksort` на каждой вставке
`src/Plugin/Chain.php:36-37`. Двойной `ksort` при каждом `addPlugin`. Безвредно при компиляции, но расточительно.

### ⚪ B-28. Конкретные базы наследуют `@deprecated Plugin`
`src/Plugin/Plugin.php:13` депрекейтит `Plugin`, но `Filler/Search/Import/Export/Item/Setting/Refiller` его наследуют. Депрекейшн неактуализируем.

## Frontend (JS)

### 🟠 B-29. `progress_log.js` — offset в JS-длине строки, не в байтах
`src/Resources/public/js/src/lib/progress_log.js:37`. `this.offset += data.content.length` использует длину в UTF-16 code units как байтовый/символьный offset для сервера. На мультибайтном контенте (не-ASCII названия аниме) offset расходится с серверным (`mb_substr`), риск дублирования/обрезки кусков лога. Сервер использует `JSON_UNESCAPED_UNICODE`, усиливая экспозицию.

### 🟡 B-30. `image.js` — неверная сигнатура error-обработчика jquery-form
`src/Resources/public/js/src/form/image.js:58-66`. Разыменовывает `data.status`/`data.responseText` как XHR, но error-сигнатура jquery-form — `(xhr, status, error)`. `JSON.parse(data.responseText)` без guard'а от битого тела.

### 🟡 B-31. `update_log.js` — бесконечный ретрай без expire
`src/Resources/public/js/src/lib/update_log.js:22`. В отличие от progress_bar/progress_log (expire 2 мин), при постоянной `error` ретраит вечно каждые 400ms. Завершение — по подстроке `Fatal error: ` (ложно сработает на легитимной лог-строке).

### 🟡 B-32. Утечки глобалов через присваивание-в-условии
`popup.js:63`, `refill.js:63,83`, `image.js:96`, `local_path.js:113,199`, `main.js:72`. `if (popup = ...)` без `var` создаёт неявные глобалы (`popup`, `from`, `value`), общие для всех инстансов. Сейчас безвредно (код синхронный), но footgun.

### 🟡 B-33. `expire` сбрасывается на каждом успешном опросе
`progress_bar.js:35`, `progress_log.js:25`. Окно expire фактически переустанавливается при каждом успешном (200) ответе → залипший-но-живой бэкенд (status не доходит до 100) опрашивается бесконечно.

### ⚪ B-34. `refill.js` — хрупкий regex извлечения query-параметра
`src/Resources/public/js/src/form/refill.js:146`. `decodeURIComponent(href).replace(/^.*(?:\?|&)source=(.*)$/, '$1')` ломается, если `source` не последний параметр или значение содержит `&`.

### ⚪ B-35. `notice_container.js` — parse на 304
`src/Resources/public/js/src/lib/notice_container.js:21`. `$.parseJSON(responseText)` в `complete`, который срабатывает и на `notmodified` (304) с возможно пустым телом.

## Тесты

### 🟡 B-36. Хрупкие позиционные mock-ожидания
`tests/Service/Storage/ScanExecutorTest.php:71-78`. `$this->at(2)/at(3)` жёстко связаны с порядком вызовов `Filesystem`; любая вставка/перестановка ломает тест (уже вызывало churn — см. недавние коммиты).

### ⚪ B-37. Опечатка в баннере Grunt
`Gruntfile.js:67`. «auto-generated by grant» (вместо grunt), проросло в закоммиченный `dist/main.min.js`.

---

## Сводная таблица приоритетов

| ID                                                            | Приоритет | Область    | Краткое                                |
|---------------------------------------------------------------|-----------|------------|----------------------------------------|
| B-01                                                          | 🔴        | Controller | executeAction fall-through (WinXP)     |
| B-14                                                          | 🔴        | Entity     | date_premiere nullable рассогласование |
| B-02                                                          | 🟠        | Controller | getPathAction null + маршрут без id    |
| B-08                                                          | 🟠        | Search     | total ≠ select при genre/label фильтре |
| B-10                                                          | 🟠        | Console    | __destruct() пишет 100% при сбое       |
| B-15                                                          | 🟠        | Migration  | неопределённая $images                 |
| B-16                                                          | 🟠        | Migration  | необратимая ChangeImagePaths           |
| B-29                                                          | 🟠        | Frontend   | offset в JS-длине, не байтах           |
| B-03,04,05,07,09,11,12,13,17,18,19,20,24,25,26,30,31,32,33,36 | 🟡        | разное     | см. выше                               |
| B-06,21,22,23,27,28,34,35,37                                  | ⚪         | разное     | косметика/мелочи                       |

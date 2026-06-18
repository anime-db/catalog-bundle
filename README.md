![Anime DB](http://anime-db.org/bundles/animedboffsite/images/logo.jpg)

[![Latest Stable Version](https://img.shields.io/packagist/v/anime-db/catalog-bundle.svg?maxAge=3600&label=stable)](https://packagist.org/packages/anime-db/catalog-bundle)
[![Latest Unstable Version](https://img.shields.io/packagist/vpre/anime-db/catalog-bundle.svg?maxAge=3600&label=unstable)](https://packagist.org/packages/anime-db/catalog-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/anime-db/catalog-bundle.svg?maxAge=3600)](https://packagist.org/packages/anime-db/catalog-bundle)
[![Build Status](https://img.shields.io/travis/anime-db/catalog-bundle.svg?maxAge=3600)](https://travis-ci.org/anime-db/catalog-bundle)
[![Coverage Status](https://img.shields.io/coveralls/anime-db/catalog-bundle.svg?maxAge=3600)](https://coveralls.io/github/anime-db/catalog-bundle?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/anime-db/catalog-bundle.svg?maxAge=3600)](https://scrutinizer-ci.com/g/anime-db/catalog-bundle/?branch=master)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/f885a5ec-272e-4721-901d-a61f687be89b.svg?maxAge=3600&label=SLInsight)](https://insight.sensiolabs.com/projects/f885a5ec-272e-4721-901d-a61f687be89b)
[![StyleCI](https://styleci.io/repos/15072150/shield?branch=master)](https://styleci.io/repos/15072150)
[![License](https://img.shields.io/packagist/l/anime-db/catalog-bundle.svg?maxAge=3600)](https://github.com/anime-db/catalog-bundle)

# Anime DB #

This is the application for making your home collection anime<br />
The application is for home use only<br />

## О проекте

**Anime DB Catalog Bundle** — это Symfony-бандл, образующий ядро настольного приложения для ведения личной коллекции аниме. Проще говоря: программа, которую вы запускаете на своём компьютере, чтобы каталогизировать аниме, которое у вас есть на дисках.

Что умеет:

- **Каталог** — карточки аниме с названиями (включая альтернативные), жанрами, студиями, странами, типами, рейтингом, датами премьеры/окончания, обложками и ссылками на источники.
- **Сканирование хранилищ** — указываете папку с видеофайлами, приложение обходит её, распознаёт имена и предлагает автоматически добавить найденное аниме в каталог.
- **Заполнение из внешних источников** — система плагинов умеет искать аниме по названию во внешних базах и заполнять карточку (поиск, заполнение, дозаполнение отдельных полей).
- **Импорт/экспорт** — перенос коллекции через плагины.
- **Метки, поиск и фильтрация** — организация коллекции и быстрый поиск по ней.
- **Мастер первичной установки** — пошаговая настройка при первом запуске.

Приложение распространяется как Composer-библиотека `anime-db/catalog-bundle` и подключается головным пакетом `anime-db/anime-db`. Расширяется плагинами без изменения ядра.

> **Важно:** приложение предназначено только для локального домашнего использования (один пользователь, localhost). В нём нет аутентификации — не выставляйте его в сеть.

## Документация для разработчиков

- [CLAUDE.md](CLAUDE.md) — краткая точка входа (команды, архитектура, подводные камни)
- [docs/PROJECT.md](docs/PROJECT.md) — структура проекта и как он устроен
- [docs/TECHNICAL.md](docs/TECHNICAL.md) — глубокая техническая документация
- [docs/AUDIT.md](docs/AUDIT.md) — аудит проекта и безопасности
- [docs/BUGS.md](docs/BUGS.md) — известные баги
- [docs/RECOMMENDATIONS.md](docs/RECOMMENDATIONS.md) — рекомендации по улучшению

## Технологии

PHP ≥5.4 · Symfony 2.7 · Doctrine ORM · SQLite · Twig 1.x · jQuery 1.12 · Grunt

## Лицензия

GPL-3.0. См. [LICENSE](LICENSE).

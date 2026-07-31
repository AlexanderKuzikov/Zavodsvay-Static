<p align="center">
  <a href="https://www.php.net/"><img alt="PHP 8" src="https://img.shields.io/badge/PHP-8.x-777BB4?logo=php&logoColor=white"></a>
  <a href="https://nodejs.org/"><img alt="Node" src="https://img.shields.io/badge/Node-tools-339933?logo=node.js&logoColor=white"></a>
  <a href="LICENSE"><img alt="License" src="https://img.shields.io/badge/License-Apache_2.0-blue.svg"></a>
</p>

<h1 align="center">Zavodsvay-Static</h1>
<p align="center">Производственный сайт завода свайных фундаментов «Гефест» (Пермь)</p>

---

Production-сайт zavodsvay.ru. Первый кейс [WebForge](https://github.com/AlexanderKuzikov/WebForge). PHP file-router для разработки, чистая статика для деплоя. 529 программно сгенерированных страниц объектов, 29 SEO-статей, интерактивная карта на 500+ маркеров.

- **PHP 8.x file-router** — layouts/, pages/, partials/
- **Vanilla CSS/JS** — без фреймворков, Mobile-First
- **Node.js ESM tooling** — медиапайплайн (Sharp → WebP), генерация страниц, sitemap
- **Yandex Maps v3** — интерактивная карта выполненных работ
- **FTP-деплой** — tools/deploy.js (dry-run, full)
- **SEO-first** — Schema.org, OG-теги, sitemap.xml, robots.txt

## Быстрый старт

```bash
git clone https://github.com/AlexanderKuzikov/Zavodsvay-Static.git
cd Zavodsvay-Static
npm run media          # обработка медиа (Sharp → WebP)
npm run ui             # локальный сервер
npm run deploy:dry     # dry-run деплой
```

## Документация

- [`docs/CONTEXT.md`](docs/CONTEXT.md) — состояние проекта
- [`docs/DECISIONS.md`](docs/DECISIONS.md) — архитектурные решения

## Статус

**Production** — zavodsvay.ru работает. 529 объектов, 29 статей, карта.

## Лицензия

[Apache-2.0](LICENSE) © Alexander Kuzikov

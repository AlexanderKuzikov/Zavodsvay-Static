# Zavodsvay-Static — Instructions for AI Agents

## Commands
- media: `npm run media`
- ui: `npm run ui`
- deploy: `npm run deploy`
- deploy:dry: `npm run deploy:dry`
- deploy:full: `npm run deploy:full`
- add-object: `npm run add-object`

## Conventions
- PHP 8.x file-router (index.php, layouts/, pages/, partials/)
- Vanilla CSS (template.css + page-specific)
- Vanilla JS (без фреймворков)
- Node.js ESM (tools/, generate-pages.mjs, generate-sitemap.mjs)
- Sharp для WebP медиапайплайна
- Yandex Maps JS API v3
- Деплой через FTP (tools/deploy.js)
- Mobile-First, SEO-first

## Structure
- `index.php` — file-router
- `layouts/` — макеты страниц
- `pages/` — контент страниц
- `partials/` — переиспользуемые блоки
- `assets/` — CSS, JS, изображения
- `data/` — JSON данные объектов
- `tools/` — Node.js утилиты (media, deploy, server)
- `source/` — исходные медиа

## Do NOT touch
- `node_modules/`
- `.env` — секреты
- `old/` — архив

## Documentation rules
- После работы — обнови docs/CONTEXT.md
- Если принял архитектурное решение — запиши в docs/DECISIONS.md
- НЕ создавай новых файлов документации без разрешения
- Переиспользуемые знания — в D:\GitHub\knowledge/README.md

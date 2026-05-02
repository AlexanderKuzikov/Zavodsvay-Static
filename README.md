# Завод винтовых свай «Гефест» — сайт

[![License](https://img.shields.io/badge/license-Apache%202.0-blue.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Nginx](https://img.shields.io/badge/Nginx-PHP--FPM-009639?logo=nginx&logoColor=white)](https://nginx.org/)
[![Site](https://img.shields.io/badge/site-zavodsvay.ru-brightgreen)](https://zavodsvay.ru)

**Продакшн:** [zavodsvay.ru](https://zavodsvay.ru)  
**Контекст разработки (для AI и деталей):** [CONTEXT.md](CONTEXT.md)

Корпоративный сайт производителя винтовых свай. Реализован как zero-dependency PHP micro-framework с файловым роутингом — без фреймворков, без сборщиков, без npm. Целевое состояние — полностью статический HTML после build-фазы.

Проект является **первым production-кейсом** генератора статических сайтов [WebForge](https://github.com/AlexanderKuzikov/WebForge). Текущая pre-static PHP-версия — рабочая среда разработки и прямой источник контента до реализации полного WebForge-пайплайна.

---

## Архитектура

```
index.php           # Точка входа, файловый роутер
├── pages/          # Страницы (файловый роутинг /slug/ → /pages/slug/index.php)
│   ├── index/      # Главная
│   ├── catalog/    # Каталог свай
│   ├── prices/     # Цены
│   ├── calc/       # Калькулятор
│   ├── montage/    # Монтаж
│   ├── articles/   # Статьи (28 страниц)
│   ├── contacts/   # Контакты
│   ├── map/        # Карта объектов (~500, в разработке)
│   ├── document/   # Документация
│   └── 404/        # Страница ошибки
├── layouts/        # Шаблоны страниц (main, home, wide)
├── partials/       # Переиспользуемые блоки (header, footer, sidebar, splash, components)
├── assets/         # CSS, JS, изображения
│   └── img/        # WebP-наборы с srcset (несколько размеров на изображение)
├── data/           # JSON-данные (закрыты от прямого доступа)
├── video/          # Видеоматериалы
├── sitemap.xml     # 37 URL (9 основных + 28 статей)
├── robots.txt      # Yandex/Googlebot/*, Sitemap-директива
└── .htaccess       # Rewrite rules, gzip, кэширование статики
```

## Принцип работы

Каждый запрос попадает в `index.php`, который резолвит `REQUEST_URI` в путь `pages/{slug}/index.php`. Каждая страница определяет `$title`, `$meta_description`, `$canonical`, рендерит контент через `ob_start()` и подключает нужный layout. Layouts подключают partials — header, footer и опциональные блоки.

```php
// pages/catalog/index.php
$title = "Каталог винтовых свай";
ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
```

## Стек

| Слой | Технология |
|---|---|
| Сервер | Nginx + PHP-FPM 8.3 |
| Роутинг | Самописный PHP file-router |
| Шаблонизация | PHP include / ob_start |
| Стили | Нативный CSS (один файл до build-фазы) |
| Скрипты | Vanilla JS |
| Данные | JSON-файлы в `/data/` |
| Хостинг | ISPmanager, webhost1 |

## SEO-слой

- `sitemap.xml` — 37 URL, генерируется вручную до реализации `build.php`
- `robots.txt` — настроен для Yandex и Googlebot
- Semantic HTML: `<header>`, `<main>`, `<footer>`, `<article>`, `<figure>`
- WebP srcset на все изображения
- Планируется: Open Graph, Twitter Cards, JSON-LD Schema.org, favicon/manifest

## Локальная разработка

Требования: [Laragon](https://laragon.org/) (PHP 8.x, Nginx/Apache).

```bash
# Клонировать репо в папку www
git clone https://github.com/AlexanderKuzikov/Zavodsvay-Static D:\laragon\www\Zavodsvay-Static
```

```powershell
# Или создать символическую ссылку если репо уже клонирован
New-Item -ItemType SymbolicLink -Path "D:\laragon\www\Zavodsvay-Static" -Target "D:\GitHub\Zavodsvay-Static"
```

Сайт доступен по адресу `http://zavodsvay-static.test`

## Деплой

Workflow: локальная правка → проверка на Laragon → коммит в `main` → деплой на сервер.

```
local (Laragon) → GitHub (main) → FTP → zavodsvay.ru
```

## Roadmap

- [ ] Open Graph / Twitter Cards / VK мета-теги + JSON-LD Schema.org
- [ ] Favicon, `site.webmanifest`, `theme-color`
- [ ] Медиасистема: реестр изображений, нарезка Sharp, VLM auto-alt
- [ ] Карта объектов (~500) + страницы каждого объекта (programmatic SEO)
- [ ] GitHub Actions → автодеплой по FTP при пуше в `main`
- [ ] `build.php` — генерация чистого статического HTML в `/dist/` + динамический sitemap.xml
- [ ] Переход на pure static: Nginx без PHP

## Связь с WebForge

Этот проект — **production-кейс** [WebForge](https://github.com/AlexanderKuzikov/WebForge), универсального генератора статических сайтов. По мере зрелости WebForge:
- `webforge.json` станет SSOT для всей структуры, данных и медиа сайта
- `build.php` заменит текущий PHP file-router на pure static HTML
- Медиапайплайн (Node.js + Sharp + VLM) будет общим инструментом
- Schema.org / OG разметка будет генерироваться из данных `webforge.json`

## Автор

**Alexander Kuzikov** — [github.com/AlexanderKuzikov](https://github.com/AlexanderKuzikov)

## Лицензия

Распространяется под лицензией [Apache License 2.0](LICENSE).

---

© 2012 — 2026 Завод винтовых свай «Гефест»

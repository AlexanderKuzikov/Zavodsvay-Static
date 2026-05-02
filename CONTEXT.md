# CONTEXT.md — Контекст разработки

> Этот файл — живой документ для AI-ассистента и разработчика.  
> Обновляется после каждой сессии/задачи. Дополняет README.md деталями, решениями и договорённостями.

---

## Режим работы с AI

- Основной инструмент: Perplexity (Space: Zavodsvay), GitHub MCP
- AI имеет право коммитить напрямую в `main` после явного подтверждения
- Браузером AI не управляет — только GitHub API
- Перед каждой сессией рекомендуется дать AI прочитать этот файл

---

## Связь с WebForge

Этот проект — **первый production-кейс** [WebForge](https://github.com/AlexanderKuzikov/WebForge).

WebForge — универсальный генератор статических сайтов с локальным пайплайном (Node, PHP, Python, Go, VLM/LLM). Zavodsvay-Static сейчас работает как **pre-static PHP-версия** — полноценный сайт на минимальном PHP без зависимостей. Целевое состояние после внедрения WebForge:

| Сейчас (pre-static) | После WebForge |
|---|---|
| PHP file-router, ручной роутинг | `build.php` → pure static HTML |
| Ручной `sitemap.xml` | Динамическая генерация из структуры |
| Изображения вручную | Node.js + Sharp, реестр в `webforge.json` |
| Schema.org/OG вручную | Генерация из данных объектов |
| 500 объектов — не реализовано | Programmatic SEO через шаблон + данные |

---

## Текущее состояние проекта

**Дата последнего обновления:** 2026-05-02

### Что реализовано
- Файловый PHP-роутер, layouts (main, home, wide), partials
- Все основные страницы: index, catalog, prices, calc, montage, articles, contacts, map, document, 404
- 28 страниц статей в `pages/articles/{slug}/`
- Адаптивная верстка на нативном CSS (один файл `assets/css/template.css`)
- Гамбургер-меню, коллапсируемый sidebar на мобиле
- Hero-видео на главной (`video/gefest01.mp4`)
- WebP-изображения с srcset (`assets/img/start/` — 6 размеров)
- Система классов для изображений в тексте (`content-image-wrapper` + BEM-модификаторы)
- Вставка `SvaiGrunt` на главную страницу с адаптивным srcset
- `sitemap.xml` — 37 URL (9 основных + 28 статей)
- `robots.txt` — Yandex/Googlebot/*, Disallow внутренних директорий, Sitemap-директива

### Ближайшие независимые блоки работ

- [ ] **SEO-разметка** — Open Graph, Twitter Cards, JSON-LD Schema.org, geo-теги (см. раздел ниже)
- [ ] **Favicon + manifest** — SVG-иконка, `apple-touch-icon`, `site.webmanifest`, `theme-color`
- [ ] **Медиасистема** — реестр, нарезка, alt-теги (см. раздел ниже)
- [ ] **Карта объектов** — ~500 объектов, страница каждого, интерактивная карта (см. раздел ниже)
- [ ] **GitHub Actions** → автодеплой по FTP
- [ ] **`build.php`** — статическая генерация `/dist/`

---

## CSS-архитектура

### Файл: `assets/css/template.css`

Единственный CSS-файл проекта (~18 КБ). Разбивать нецелесообразно до появления сборщика (`build.php`). Структура секций:
1. Шрифты (`@font-face`)
2. Базовые стили (body, .main-layout-container)
3. Header
4. Navigation + Hamburger
5. Sidebar
6. Content Area
7. Footer
8. Icons
9. Back to Top
10. Video
11. Изображения в тексте
12. Media Queries

### Медиазапросы — договорённости

| Брейкпоинт | Назначение |
|---|---|
| `max-width: 480px` | Мелкие телефоны — шрифты, float сброс |
| `max-width: 768px` | Мобильный layout (основной) — nav, sidebar collapse, footer |
| `max-width: 1024px` | Планшеты — sidebar 200px, изображения 50% |
| Выше 1024px | Десктоп — без медиазапросов, max-width: 1200px на контейнере |

**Решение принято:** FullHD и 4K медиазапросы не добавляем. Контейнер ограничен `max-width: 1200px`, этого достаточно.

### Система классов изображений в тексте

```
.content-image-wrapper          — базовый блок (border-radius, shadow, user-select)
.content-image-wrapper--left    — float left, 45% ширины
.content-image-wrapper--right   — float right, 45% ширины
.content-image-wrapper--full    — полная ширина, clear both
.content-image-wrapper--center  — 60%, auto margin, без float
.content-clearfix               — сброс float после последнего абзаца с обтеканием
```

На `≤768px` все float-модификаторы → `width: 100%`, `float: none`.

---

## SEO-файлы (реализовано)

### sitemap.xml
- 9 основных страниц + 28 статей = **37 URL**
- Статьи: `priority 0.6`, `changefreq yearly`
- `/prices/` — `changefreq weekly` (цены меняются)
- **TODO при добавлении новой статьи:** добавить URL вручную в `sitemap.xml` до реализации `build.php`

### robots.txt
```
User-agent: * / Yandex / Googlebot
Allow: /
Disallow: /data/ /layouts/ /partials/
Crawl-delay: 5.0  (только Yandex)
Sitemap: https://zavodsvay.ru/sitemap.xml
```

---

## SEO-разметка (запланировано)

### Open Graph + Twitter Cards + VK
Добавить в layouts. Требования к OG-изображению: **1200×630px**, ≤1 МБ.

```html
<meta property="og:type" content="website">
<meta property="og:url" content="https://zavodsvay.ru/">
<meta property="og:title" content="...">
<meta property="og:description" content="...">
<meta property="og:image" content="https://zavodsvay.ru/assets/img/og/og-home.jpg">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:locale" content="ru_RU">
<meta property="og:site_name" content="Завод «Гефест»">
<meta name="twitter:card" content="summary_large_image">
<meta property="vk:image" content="https://zavodsvay.ru/assets/img/og/og-home.jpg">
```

### JSON-LD Schema.org
Стратегия: `@graph` в `<head>` на каждой странице, генерируется из данных.

- **Все страницы:** `Organization` + `LocalBusiness` + `WebSite` (базовый слой)
- **Статьи:** дополнительно `Article` с `datePublished`, `ImageObject`
- **Страницы объектов:** `LocalBusiness` с `geo`, `photo[]`, `additionalProperty[]`
- **Изображения:** `ImageObject` с уникальным `name` и `description` — сигнал для Google Images

### Geo-теги (Яндекс)
```html
<meta name="geo.region" content="RU-PER">
<meta name="geo.placename" content="Пермь">
<meta name="geo.position" content="58.0105;56.2502">
<meta name="ICBM" content="58.0105, 56.2502">
```

---

## Favicon и манифест (запланировано)

Минимальный набор файлов (2026):

| Файл | Размер | Назначение |
|---|---|---|
| `favicon.svg` | вектор | Современные браузеры, тёмная тема |
| `favicon.ico` | 32×32 | Fallback все браузеры |
| `apple-touch-icon.png` | 180×180 | iOS |
| `assets/img/icons/icon-192.png` | 192×192 | Android/PWA |
| `assets/img/icons/icon-512.png` | 512×512 | PWA splash, maskable |
| `site.webmanifest` | — | Android/PWA манифест |

`site.webmanifest`: `display: "browser"`, `theme_color: "#1a1a2e"`, `lang: "ru"`, `purpose: "maskable"` на 512px.

**Блокер:** нужен исходный SVG или PNG 512px+ логотипа Гефест.

---

## Медиасистема (архитектурный вектор)

### Структура хранения
```
source/                     ← оригиналы вне веб-корня
├── objects/{slug}/         ← фото объектов (~500)
├── articles/{slug}/
└── catalog/{slug}/
assets/img/
├── objects/{slug}/         ← нарезанные WebP/AVIF наборы
├── articles/{slug}/
├── catalog/{slug}/
├── shared/                 ← логотипы, иконки
└── og/                     ← OG-изображения 1200×630
```

### Именование
`{slug}-{descriptor}-{width}.webp` — пример: `grunt-montazh-640.webp`

### Форматы и размеры
- Форматы: **AVIF + WebP** (fallback JPG), AVIF даёт -30-50% к WebP
- Ширины: 320, 640, 1024, 1600px (+ 2000px для hero)
- Нарезка: **Node.js + Sharp** (локальный скрипт `tools/process-media.js`)

### Реестр изображений
Когда появится `webforge.json` — медиареестр будет его частью. До тех пор — отдельный `data/media.json`:
```json
{
  "grunt-montazh": {
    "alt": "Погружение винтовых свай ВСГ-108 в грунт",
    "title": "Монтаж свай Гефест",
    "widths": [320, 640, 1024, 1600],
    "formats": ["avif", "webp"],
    "path": "objects/obj-001",
    "vlm_reviewed": false
  }
}
```

### VLM auto-alt
Для ~5000 изображений alt-теги генерируются локальной VLM (Qwen) скриптом `tools/generate-alts.js`.
Флаг `vlm_reviewed: false` — к последующей ручной проверке выборки.

---

## Карта объектов (запланировано)

~500 реализованных объектов. Каждый объект — отдельная SEO-страница + точка на интерактивной карте.

### Структура данных объекта
```json
{
  "id": "obj-001",
  "slug": "perm-ul-lenina-15",
  "title": "Фундамент на ул. Ленина 15, Пермь",
  "coords": [58.0105, 56.2502],
  "date": "2024-04",
  "piles": { "type": "ВСГ-108", "count": 24, "depth": 3.5 },
  "structure": "Жилой дом 2 этажа, брус",
  "images": ["obj-001-main", "obj-001-foundation"],
  "description": "..."
}
```

### Стратегия карты (автономность)
**MapLibre GL + PMTiles** — единый бинарный файл карты региона (~10-50 МБ), WebGL-рендеринг, полная автономность. Альтернатива — Leaflet + локальные тайлы (сложнее, больше файлов).

### SEO-стратегия
- `build.php` итерирует `data/objects.json` → 500 статических HTML-страниц
- Каждая страница уникальна: данные + фото + уникальный alt + Schema.org `LocalBusiness` с `geo`
- Programmatic SEO: уникальность за счёт реальных данных, не шаблонного текста

---

## Изображения (текущие файлы)

### assets/img/start/ — hero-изображения
Формат: WebP, 6 размеров: 480, 600, 800, 1200, 1600, 1920px.
Используются в `<picture>` с `srcset`.

### assets/img/SvaiGrunt* — фото погружения свай

| Файл | Ширина |
|---|---|
| `SvaiGruntx320.webp` | 320px |
| `SvaiGruntx640.webp` | 640px |
| `SvaiGruntx1024.webp` | 1024px |
| `SvaiGrunt2000.webp` | 2000px |
| `SvaiGrunt.jpg` | оригинал (845 КБ, fallback) |

Вставлено на главную (`pages/index/content.html`) перед 3-м абзацем, класс `--right`.
`sizes="(max-width: 1024px) 100vw, 48%"` — полная ширина на планшете/мобиле, правая половина на десктопе.

### Соглашение по именованию
Новые наборы: `{slug}-{descriptor}-{width}.webp` (пример: `grunt-montazh-640.webp`).
Старые файлы (`SvaiGruntx{width}.webp`) — исторически, новые делаем единообразно.

---

## Шаблоны и партиалы

### Layouts
- `layouts/main.php` — стандартный (sidebar + content)
- `layouts/home.php` — главная (splash + content)
- `layouts/wide.php` — без sidebar

### Структура страницы
Каждая страница в `pages/{slug}/index.php`:
```php
$title = "...";
$meta_description = "...";
$canonical = "...";
ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
```
Контент хранится в отдельном `content.html` рядом.

---

## Договорённости и решения

| Дата | Решение |
|---|---|
| 2026-04-30 | Медиазапросы: три точки — 480/768/1024px. Выше не нужно. |
| 2026-04-30 | CSS изображений: BEM-модификаторы к `.content-image-wrapper`, не новые классы |
| 2026-04-30 | `<figure>` + `<picture>` + `<figcaption>` — стандарт вставки изображений в текст |
| 2026-04-30 | `content-clearfix` ставится после последнего абзаца, обтекающего изображение |
| 2026-04-30 | FullHD/4K медиазапросы не добавляем — max-width: 1200px достаточно |
| 2026-04-30 | CSS не разбиваем на файлы до появления `build.php` |
| 2026-04-30 | sitemap.xml обновляется вручную при добавлении статей; при `build.php` — генерировать динамически |
| 2026-05-02 | Форматы изображений: AVIF + WebP + JPG fallback. Нарезка через Node.js + Sharp |
| 2026-05-02 | Медиареестр: `data/media.json` до WebForge, затем переезжает в `webforge.json` |
| 2026-05-02 | Карта объектов: MapLibre GL + PMTiles (автономность), не Google Maps |
| 2026-05-02 | OG-изображение: 1200×630px, файл `assets/img/og/og-home.jpg` |
| 2026-05-02 | Schema.org: `@graph` с `Organization + LocalBusiness + WebSite` на всех страницах |
| 2026-05-02 | Favicon: SVG + ICO + 180px + 192px + 512px + `site.webmanifest` (6 файлов) |

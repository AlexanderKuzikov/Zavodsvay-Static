# Zavodsvay-Static

Производственный сайт завода свайных фундаментов **«Гефест»** (Пермь).  
Первый production-кейс [WebForge](https://github.com/AlexanderKuzikov/WebForge).

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=flat-square&logo=php&logoColor=white)
![CSS](https://img.shields.io/badge/CSS-нативный-1572B6?style=flat-square&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JS-Vanilla-F7DF1E?style=flat-square&logo=javascript&logoColor=black)
![Node.js](https://img.shields.io/badge/Node.js-медиапайплайн-5FA04E?style=flat-square&logo=nodedotjs&logoColor=white)
![Sharp](https://img.shields.io/badge/Sharp-WebP-99CC00?style=flat-square&logo=sharp&logoColor=white)
![FTP Deploy](https://img.shields.io/badge/Deploy-FTP_deploy.js-0066CC?style=flat-square&logo=filezilla&logoColor=white)
![Yandex Maps](https://img.shields.io/badge/Яндекс.Карты-JS_API_v3-FF0000?style=flat-square&logo=yandex&logoColor=white)
![WebP](https://img.shields.io/badge/Images-WebP_srcset-2196F3?style=flat-square&logo=webp&logoColor=white)
![SEO](https://img.shields.io/badge/SEO-first-4CAF50?style=flat-square&logo=googlesearchconsole&logoColor=white)
![License](https://img.shields.io/badge/License-Apache_2.0-D22128?style=flat-square&logo=apache&logoColor=white)

> **Статус:** pre-static PHP-версия. Favicon + `site.webmanifest` уже подключены. Целевое состояние — pure static HTML через `build.php` после готовности WebForge-генератора.

---

## Архитектура

**Текущий режим:** pre-static PHP-сайт без фреймворков и зависимостей на хостинге.

```
Zavodsvay-Static/
├── pages/              ← страницы ({slug}/index.php + content.html)
│   ├── articles/       ← 28 статей
│   └── objects/        ← страницы объектов (в разработке)
├── layouts/            ← шаблоны (main, home, wide)
├── partials/           ← переиспользуемые компоненты (image.php, head-favicon.php, head-seo.php, ...)
├── assets/
│   ├── css/template.css   ← монолитный CSS (до build.php)
│   ├── img/               ← нарезанные WebP-наборы + icons/ + og/
│   └── fonts/
├── source/             ← оригиналы изображений (jpg, png, gif, webp) — в git
├── data/
│   ├── media.json      ← SSOT-реестр изображений
│   ├── map.json        ← данные 500+ точек карты выполненных работ
│   └── objects.json    ← реестр объектов (в разработке)
├── video/
├── tools/              ← медиапайплайн + деплой (Node.js, только локально)
│   ├── process-media.js
│   ├── deploy.js       ← FTP-деплой на shared hosting
│   ├── server.js
│   └── ui/index.html
├── index.php           ← файловый роутер
├── favicon.png
├── apple-touch-icon.png
├── site.webmanifest
├── sitemap.xml         ← обновляется вручную до build.php
├── robots.txt
├── .htaccess
├── CONTEXT.md          ← живой документ разработки для AI + разработчика
└── README.md
```

---

## Медиапайплайн

Локальный инструмент для работы с изображениями. На хостинг **не деплоится**.

### Запуск

```bash
cd tools
npm install      # один раз
npm run ui       # Media UI → http://localhost:3010
# или CLI:
node process-media.js
```

### Как работает

1. Бросаешь оригинал в `source/` (jpg/png/gif/webp, включая анимированные GIF)
2. Нажимаешь **«Сканировать»** в UI — файл регистрируется в `data/media.json`
3. Нажимаешь **«Нарезать всё»** — генерируются WebP-варианты в `assets/img/`
4. Заполняешь `alt` и `caption` прямо в интерфейсе
5. При замене файла — чекбокс **«Перегенерировать»** при сохранении
6. Кнопка **«Найти мусор»** — удаляет WebP-файлы без записи в реестре (orphans)

### Ключи в media.json

Ключ = путь относительно `source/` без расширения, слэши → дефисы:
- `source/logo2.png` → `"logo2"`
- `source/objects/obj-001/main.jpg` → `"objects-obj-001-main"`

### Структура записи

```json
{
  "logo2": {
    "file": "source/logo2.png",
    "dir": "",
    "orig_width": 1200,
    "orig_height": 400,
    "widths": [320, 640],
    "alt": "Логотип Завод Гефест",
    "caption": "",
    "generated": true
  }
}
```

**Ключевые поля:**
- `widths` — реально сгенерированные размеры (≤ ширины оригинала)
- `orig_width`/`orig_height` — для `width`/`height` атрибутов и нулевого CLS
- `generated: false` → пересоздать файлы при следующем запуске

### Использование в PHP

```php
require_once __DIR__ . '/partials/image.php';
render_image('logo2');
// генерирует <picture> с srcset, width, height из реестра
```

---

## Деплой

Локальный FTP-деплой через `tools/deploy.js`. Быстрый, управляемый, без внешних CI.

```bash
cd tools
node deploy.js
```

---

## Страницы

| URL | Файл |
|---|---|
| `/` | `pages/index/` |
| `/catalog/` | `pages/catalog/` |
| `/prices/` | `pages/prices/` |
| `/calc/` | `pages/calc/` |
| `/montage/` | `pages/montage/` |
| `/articles/` | `pages/articles/` |
| `/contacts/` | `pages/contacts/` |
| `/map/` | `pages/map/` |
| `/document/` | `pages/document/` |
| `/articles/{slug}/` | `pages/articles/{slug}/` |
| `/objects/{slug}/` | `pages/objects/{slug}/` (в разработке) |

---

## SEO

- `sitemap.xml` — 37 URL (9 основных + 28 статей), ручное обновление до `build.php`
- `robots.txt` — Yandex/Googlebot, Crawl-delay для Yandex
- WebP + `srcset` — Core Web Vitals
- `orig_width`/`orig_height` в реестре → нулевой Layout Shift (CLS)
- **OG-изображения готовы:** `assets/img/og/og-home.jpg` (primary, для краулеров), `assets/img/og/og-home.webp` (дополнительный)
- **`partials/head-seo.php`** — реализован: OG, Twitter Cards, JSON-LD Schema.org `@graph`, geo-теги Яндекса
- Уже реализовано: `favicon.png`, `apple-touch-icon.png`, `site.webmanifest`, `assets/img/icons/icon-192.png`, `assets/img/icons/icon-512.png`, `partials/head-favicon.php`
- В планах: OG + Schema.org для статей (`og_type=article`), страницы объектов (programmatic SEO)

---

## Известный технический долг

| Проблема | Причина | Решение |
|---|---|---|
| `template.css` — монолит | Осознанно до `build.php` | Декомпозиция на компоненты при миграции на WebForge |
| `sitemap.xml` вручную | До генератора | Автогенерация в `build.php` |
| `source/` в git | Пока объём мал | Git LFS при росте объёма (решать до, не после) |
| Нет hash-инвалидации CSS/JS | До `build.php` | `style.{hash8}.css` при сборке |
| Изображения объектов вне репо | Требуют подготовки и нарезки | Image pipeline для объектов (отдельный этап) |

---

## Роадмап

- [x] Favicon + `site.webmanifest`
- [x] Карта выполненных работ (Яндекс.Карты v3 + кластеризация + легенда категорий)
- [x] OG-изображения (`assets/img/og/og-home.jpg` + `.webp`)
- [x] SEO-partial (`partials/head-seo.php` — OG, Schema.org, geo-теги)
- [x] Карта 500+ объектов — данные подготовлены через Qwen3.5 Flash, карта реализована
- [ ] **Object pages** — первые страницы объектов `/objects/{slug}/`, открываются по клику с карты
- [ ] **Image pipeline для объектов** — подготовка, нарезка, частичная автоматизация + generative-модели
- [ ] SEO-разметка статей (og_type=article, Schema.org Article)
- [ ] `build.php` → pure static `/dist/`
- [ ] Портирование медиапайплайна в WebForge

---

## AI в проекте

| Инструмент | Применение |
|---|---|
| Perplexity (Space: Zavodsvay) | Основной AI-ассистент разработки, работа с репо через GitHub MCP |
| Qwen3.5 Flash (облачный) | Подготовка данных карты: восстановление полей, нормализация, категоризация 500+ объектов |
| Generative-модели (планируется) | Подготовка и дополнение изображений объектов в image pipeline |

---

## Стек

| Слой | Технология |
|---|---|
| Сайт | PHP 8.x, нативный CSS, vanilla JS |
| Медиапайплайн | Node.js, Sharp, Express |
| Деплой | FTP через `tools/deploy.js` (shared hosting, локальный deploy tool) |
| Карта | Яндекс.Карты JS API v3 + `@yandex/ymaps3-clusterer` (via jsdelivr CDN) |

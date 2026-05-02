# Zavodsvay-Static

Производственный сайт завода свайных фундаментов **«Гефест»** (Пермь).  
Первый production-кейс [WebForge](https://github.com/AlexanderKuzikov/WebForge).

---

## Архитектура

**Текущий режим:** pre-static PHP-сайт без фреймворков и зависимостей на хостинге.

```
Zavodsvay-Static/
├── pages/              ← страницы ({slug}/index.php + content.html)
│   └── articles/       ← 28 статей
├── layouts/            ← шаблоны (main, home, wide)
├── partials/           ← переиспользуемые компоненты (image.php, ...)
├── assets/
│   ├── css/template.css
│   ├── img/            ← нарезанные WebP-наборы
│   └── fonts/
├── source/             ← оригиналы изображений (jpg, png, gif, webp)
├── data/
│   └── media.json      ← SSOT-реестр изображений
├── video/
├── tools/              ← медиапайплайн (Node.js)
│   ├── process-media.js
│   ├── server.js
│   └── ui/index.html
├── index.php           ← файловый роутер
├── sitemap.xml
├── robots.txt
├── .htaccess
└── CONTEXT.md          ← живой документ разработки для AI + команды
```

---

## Медиапайплайн

Локальный инструмент для работы с изображениями. На хостинг не деплоится.

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

### Ключи в media.json

Ключ = путь относительно `source/` без расширения, слэши → дефисы:
- `source/logo2.png` → `"logo2"`
- `source/objects/obj-001/main.jpg` → `"objects-obj-001-main"`

### Использование в PHP

```php
require_once __DIR__ . '/partials/image.php';
render_image('logo2');
// генерирует <picture> с srcset, width, height из реестра
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

---

## SEO

- `sitemap.xml` — 37 URL (9 основных + 28 статей)
- `robots.txt` — Yandex/Googlebot, Crawl-delay для Yandex
- WebP + `srcset` — Core Web Vitals
- `orig_width`/`orig_height` в реестре → нулевой Layout Shift (CLS)
- **Запланировано:** Open Graph, JSON-LD Schema.org, geo-теги, favicon

---

## Роадмап

- [ ] SEO-разметка (OG, Schema.org, geo)
- [ ] Favicon + `site.webmanifest`
- [ ] Карта ~500 объектов (MapLibre GL + PMTiles)
- [ ] GitHub Actions → автодеплой по FTP
- [ ] `build.php` → pure static `/dist/`

---

## Стек

| Слой | Технология |
|---|---|
| Сайт | PHP 8.x, нативный CSS, vanilla JS |
| Медиапайплайн | Node.js, Sharp, Express |
| Деплой | FTP (shared hosting) |
| Карта (план) | MapLibre GL + PMTiles |

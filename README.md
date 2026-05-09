# Zavodsvay-Static

Производственный сайт завода свайных фундаментов **«Гефест»** (Пермь).  
Первый production-кейс [WebForge](https://github.com/AlexanderKuzikov/WebForge).

> **Статус:** pre-static PHP-версия. Favicon + `site.webmanifest` уже подключены. Целевое состояние — pure static HTML через `build.php` после готовности WebForge-генератора.

---

## Архитектура

**Текущий режим:** pre-static PHP-сайт без фреймворков и зависимостей на хостинге.

```
Zavodsvay-Static/
├── pages/              ← страницы ({slug}/index.php + content.html)
│   └── articles/       ← 28 статей
├── layouts/            ← шаблоны (main, home, wide)
├── partials/           ← переиспользуемые компоненты (image.php, head-favicon.php, ...)
├── assets/
│   ├── css/template.css   ← монолитный CSS (до build.php)
│   ├── img/               ← нарезанные WebP-наборы + icons/
│   └── fonts/
├── source/             ← оригиналы изображений (jpg, png, gif, webp) — в git
├── data/
│   ├── media.json      ← SSOT-реестр изображений
│   └── map.json        ← данные точек карты выполненных работ
├── video/
├── tools/              ← медиапайплайн (Node.js, только локально)
│   ├── process-media.js
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

- `sitemap.xml` — 37 URL (9 основных + 28 статей), ручное обновление до `build.php`
- `robots.txt` — Yandex/Googlebot, Crawl-delay для Yandex
- WebP + `srcset` — Core Web Vitals
- `orig_width`/`orig_height` в реестре → нулевой Layout Shift (CLS)
- Уже реализовано: `favicon.png`, `apple-touch-icon.png`, `site.webmanifest`, `assets/img/icons/icon-192.png`, `assets/img/icons/icon-512.png`, `partials/head-favicon.php`
- В планах: Open Graph, JSON-LD Schema.org, geo-теги

---

## Известный технический долг

| Проблема | Причина | Решение |
|---|---|---|
| `template.css` — монолит | Осознанно до `build.php` | Декомпозиция на компоненты при миграции на WebForge |
| `sitemap.xml` вручную | До генератора | Автогенерация в `build.php` |
| `source/` в git | Пока объём мал | Git LFS при росте объёма (решать до, не после) |
| Нет CI/CD | Не приоритет | GitHub Actions → FTP |
| Нет hash-инвалидации CSS/JS | До `build.php` | `style.{hash8}.css` при сборке |

---

## Роадмап

- [ ] SEO-разметка (OG, Schema.org, geo-теги)
- [x] Favicon + `site.webmanifest`
- [x] Карта выполненных работ (Яндекс.Карты v3 + кластеризация + легенда категорий)
- [ ] Карта ~500 объектов (данные + страницы объектов)
- [ ] GitHub Actions → автодеплой по FTP
- [ ] `build.php` → pure static `/dist/`
- [ ] Портирование медиапайплайна в WebForge

---

## Стек

| Слой | Технология |
|---|---|
| Сайт | PHP 8.x, нативный CSS, vanilla JS |
| Медиапайплайн | Node.js, Sharp, Express |
| Деплой | FTP (shared hosting, пока вручной) |
| Карта | Яндекс.Карты JS API v3 + `@yandex/ymaps3-clusterer` (via jsdelivr CDN) |

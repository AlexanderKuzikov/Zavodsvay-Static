# CONTEXT.md — Контекст разработки

> Этот файл — живой документ для AI-ассистента и разработчика.  
> Обновляется после каждой сессии/задачи. Дополняет README.md деталями, решениями и договорённостями.

---

## Режим работы с AI

- Основной инструмент: Perplexity (Space: Zavodsvay), GitHub MCP
- AI может коммитить напрямую в `main`, если пользователь явно просит «действуй / сделай сам / коммить»
- Если push по MCP по какой-то причине не доведён до результата, AI должен сохранить готовые файлы в чат для скачивания, а не зависать в ожидании
- Браузером AI не управляет — только GitHub API
- Перед каждой сессией рекомендуется дать AI прочитать этот файл
- **Важно:** AI не должен молчаливо соглашаться с архитектурными решениями — открытые вопросы (см. ниже) требуют явного обсуждения, не автоматического принятия

---

## Данные клиента (Завод «Гефест»)

> Используются для Schema.org, OG-разметки, geo-тегов, favicon, site.webmanifest.

```
Название:        Завод винтовых свай «Гефест»
Юр. название:   ООО "Завод Винтовых Свай "Гефест""
Сайт:            https://zavodsvay.ru/
Телефон:         +7 (342) 20-99-800  →  +73422099800
Email:           info@zavodsvay.ru
Адрес:           г. Пермь, ул. Монастырская, 14, офис 502
Город:           Пермь
Регион:          RU-PER (Пермский край)
Индекс:          614000

Координаты:
  Decimal:       58.014746, 56.228500
  DMS:           58°0′53″N, 56°13′43″E
  Яндекс.Карты:  https://yandex.ru/maps/-/CPWQYF-0

Режим работы:
  Пн–Пт:         09:00–18:00
  Сб–Вс:         Выходной

Соцсети:
  VK:            https://vk.com/club236711949

На рынке с:      2012 года
Гарантия:        до 50 лет
Сфера:           Производство и монтаж винтовых свай, фундаменты
```

### Для Schema.org `openingHoursSpecification`
```json
[
  {
    "@type": "OpeningHoursSpecification",
    "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday"],
    "opens": "09:00",
    "closes": "18:00"
  }
]
```

### Для geo-тегов
```html
<meta name="geo.region" content="RU-PER">
<meta name="geo.placename" content="Пермь">
<meta name="geo.position" content="58.014746;56.228500">
<meta name="ICBM" content="58.014746, 56.228500">
```

### OG-изображения (готовы)
- `assets/img/og/og-home.jpg` — **primary**, используется в `og:image` для краулеров (Telegram, VK, WhatsApp, Twitter, LinkedIn, iMessage и др.)
- `assets/img/og/og-home.webp` — дополнительный asset, для использования на сайте при необходимости
- Размер: 1200×630px

### Блокеры (нужно от клиента)
- [x] OG-изображение 1200×630px — **готово** (`og-home.jpg` + `og-home.webp`)
- [ ] Favicon SVG — только если нужен именно векторный вариант; текущий PNG-комплект уже рабочий

---

## Связь с WebForge

Этот проект — **первый production-кейс** [WebForge](https://github.com/AlexanderKuzikov/WebForge).

WebForge — универсальный генератор статических сайтов с локальным пайплайном (Node, PHP, Python, Go, VLM/LLM). Zavodsvay-Static сейчас работает как **pre-static PHP-версия** — полноценный сайт на минимальном PHP без зависимостей. Целевое состояние после внедрения WebForge:

| Сейчас (pre-static) | После WebForge |
|---|---|
| PHP file-router, ручной роутинг | `build.php` → pure static HTML |
| Ручной `sitemap.xml` | Динамическая генерация из структуры |
| Медиапайплайн через `tools/` | Пайплайн становится частью WebForge |
| Schema.org/OG вручную | Генерация из данных объектов |
| Object pages вручную (PHP) | Programmatic SEO через шаблон + данные |

### Граница Zavodsvay ↔ WebForge

| Функция | Статус |
|---|---|
| Медиапайплайн (process-media.js) | Прототип в Zavodsvay → портируется в WebForge |
| Schema.org / OG генерация | Архитектура в WebForge, применение здесь |
| Карта объектов | **Zavodsvay-специфика** с потенциалом стать компонентом WebForge |
| CSS-система компонентов | Решается в WebForge, переносится при миграции |
| `sitemap.xml` генерация | В WebForge при build, здесь вручную до миграции |
| Object pages / programmatic SEO | **Zavodsvay-специфика**, паттерн войдёт в WebForge как data-driven pages |
| Image pipeline для объектов | Прототип здесь → инструмент в WebForge |

---

## Текущее состояние проекта

**Дата последнего обновления:** 2026-05-14

### Что реализовано
- Файловый PHP-роутер, layouts (main, home, wide), partials
- Все основные страницы: index, catalog, prices, calc, montage, articles, contacts, map, document, 404
- 28 страниц статей в `pages/articles/{slug}/`
- Адаптивная верстка на нативном CSS (один файл `assets/css/template.css`)
- Гамбургер-меню, коллапсируемый sidebar на мобиле
- Hero-видео на главной (`video/gefest01.mp4`)
- WebP-изображения с srcset (`assets/img/start/` — 6 размеров)
- Система классов для изображений в тексте (`content-image-wrapper` + BEM-модификаторы)
- `sitemap.xml` — 37 URL (9 основных + 28 статей)
- `robots.txt` — Yandex/Googlebot/*, Disallow внутренних директорий, Sitemap-директива
- **Медиапайплайн** — `data/media.json` + `tools/process-media.js` + `tools/server.js` + `tools/ui/`
- **Деплой** — `tools/deploy.js` (локальный FTP-деплой на shared hosting)
- **Favicon/manifest подключены** — `favicon.png`, `apple-touch-icon.png`, `site.webmanifest`, `assets/img/icons/icon-192.png`, `assets/img/icons/icon-512.png`
- `partials/head-favicon.php` подключён в `layouts/main.php`, `layouts/home.php`, `layouts/wide.php`
- **`partials/head-seo.php`** — реализован и подключён во все layouts: OG, Twitter Cards, JSON-LD Schema.org `@graph` (Organization + LocalBusiness + WebSite + WebPage), geo-теги Яндекса
- **OG-изображения** — `assets/img/og/og-home.jpg` (primary) + `assets/img/og/og-home.webp`
- **Карта выполненных работ** — `/map/`, Яндекс.Карты JS API v3, маркеры + кластеризация
- **Легенда карты** — блок под картой с цветовыми маркерами категорий, CSS Grid с равными колонками
- **Карта 500+ объектов** — данные подготовлены, карта реализована (маркеры кликабельны, `url` в данных)
- **Данные объектов** — восстановлены и нормализованы через **Qwen3.5 Flash** (облачный): поля восстановлены, категории распределены, данные очищены

### Ближайшие независимые блоки работ

- [x] **Favicon + manifest** — базовый комплект готов и подключён
- [x] **Карта выполненных работ** — реализована с кластеризацией
- [x] **Легенда карты** — цветовые категории под картой
- [x] **OG-изображения** — `og-home.jpg` + `og-home.webp` готовы
- [x] **SEO-partial** — `head-seo.php` реализован и подключён во все layouts
- [x] **Деплой** — `tools/deploy.js` реализован и используется
- [x] **Карта 500+ объектов** — данные и карта готовы
- [ ] **Object pages (первый этап)** — пилотные страницы `/objects/{slug}/`, открываются по клику с карты
- [ ] **Image pipeline для объектов** — подготовка изображений: нарезка, автоматизация, generative-модели
- [ ] **SEO статей** — `og_type=article`, Schema.org Article, `article:published_time`
- [ ] **`build.php`** — статическая генерация `/dist/`

---

## Открытые архитектурные вопросы

> Эти вопросы **не закрыты**. AI не должен молчаливо принимать решения по ним — требуется явное обсуждение с разработчиком.

1. **CSS naming convention для новых секций.** Текущий `template.css` использует плоские классы (`.sidebar`, `.header`). При миграции на WebForge и scoped `.c-{name}` префиксы потребуется рефакторинг. Нужно ли уже сейчас писать новые секции в финальном стиле?

2. **Git LFS для `source/`.** Порог принятия решения — какой объём оригиналов считается критичным? Решать до, не после роста.

3. **Контракт данных объекта.** `data/objects.json` существует, но структура для object pages требует явной фиксации: slug, canonical, title, excerpt, category, coords, media_keys, status (published/draft), SEO-поля. Зафиксировать до старта разработки страниц.

4. **`build.php` в Zavodsvay vs в WebForge.** Писать `build.php` здесь как прототип (и потом портировать), или дождаться WebForge-генератора? Влияет на приоритет задач.

5. **hash-инвалидация CSS/JS.** При деплое на shared hosting без CDN браузеры кэшируют старые версии. Нужно ли реализовывать `style.{hash8}.css` до или после `build.php`?

6. **Image pipeline для объектов.** Изображения хранятся вне репо. Нужно определить: структуру хранения, критерии автоматизации vs ручной обработки, роль generative-моделей (дополнение / восстановление / генерация превью), связку с `data/media.json`.

---

## AI в проекте

### Инструменты и применение

| Инструмент | Применение |
|---|---|
| Perplexity (Space: Zavodsvay) | Основной AI-ассистент разработки; архитектурные решения, коммиты через GitHub MCP |
| Qwen3.5 Flash (облачный) | Подготовка данных карты: восстановление полей, нормализация адресов, распределение по категориям, очистка 500+ записей |
| Generative-модели (планируется) | Image pipeline объектов: дополнение, восстановление, генерация превью где нет фото |

### Опыт применения Qwen3.5 Flash
Облачная модель Qwen3.5 Flash использовалась для batch-обработки исходных данных объектов:
- Восстановление неполных и повреждённых полей
- Нормализация адресов и названий
- Автоматическое распределение по 9 категориям (CAT_COLORS)
- Итог: production-ready `data/map.json` с 500+ объектами без ручной правки каждой записи

---

## Медиапайплайн (реализовано)

### Структура
```
source/                     ← оригиналы (jpg, png, gif, webp) — хранятся в git
assets/img/                 ← нарезанные WebP-наборы
data/media.json             ← SSOT-реестр всех изображений
tools/
├── process-media.js        ← CLI: сканирует source/, нарезает WebP
├── deploy.js               ← FTP-деплой на shared hosting
├── server.js               ← Express UI (порт 3010)
├── ui/index.html           ← медиабиблиотека: сетка, редактирование, удаление
└── package.json            ← зависимости: sharp, express
partials/image.php          ← render_image() для шаблонов
```

### Запуск UI
```bash
cd tools
npm install      # один раз
npm run ui       # http://localhost:3010
```

### CLI (без UI)
```bash
cd tools
node process-media.js
```

### Схема ключей в media.json
Ключ = относительный путь от `source/` без расширения, слэши → дефисы.
- `source/logo2.png` → `"logo2"`
- `source/objects/obj-001/main.jpg` → `"objects-obj-001-main"`

### Структура записи в media.json
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
- `orig_width` / `orig_height` — размер оригинала, используется для `width`/`height` атрибутов и `aspect-ratio`
- `generated: false` → пересоздать файлы при следующем запуске / кнопке «Перегенерировать» в UI

### Поддерживаемые форматы
`.jpg`, `.jpeg`, `.png`, `.webp`, `.gif` (включая анимированные — конвертируются в анимированный WebP)

### API сервера (tools/server.js)
| Метод | URL | Действие |
|---|---|---|
| GET | `/api/media` | Весь реестр |
| GET | `/api/scan` | Сканировать source/, добавить новые |
| POST | `/api/media/:key` | Сохранить alt/caption; `regenerate:true` → пересоздать WebP |
| DELETE | `/api/media/:key` | Удалить запись; `?files=1` → удалить и WebP-файлы |
| GET | `/api/orphans` | WebP-файлы без записи в реестре |
| DELETE | `/api/orphans` | Удалить все orphan-файлы |
| POST | `/api/process` | Нарезать все с `generated:false` |
| GET | `/api/thumb/:key` | Превью 200px из оригинала |

### PHP-хелпер
`partials/image.php` — функция `render_image($key)`. Читает `data/media.json` (кэш в `static $registry`), генерирует `<picture>` с корректными `srcset`, `width`, `height`.

---

## SEO-файлы

### Реализовано
- `favicon.png` в корне
- `apple-touch-icon.png` в корне
- `site.webmanifest` в корне
- `assets/img/icons/icon-192.png`
- `assets/img/icons/icon-512.png`
- `partials/head-favicon.php` — подключён во все layouts
- `assets/img/og/og-home.jpg` — OG-изображение primary (1200×630px)
- `assets/img/og/og-home.webp` — OG-изображение WebP-версия
- **`partials/head-seo.php`** — реализован и подключён во все layouts
  - Open Graph (og:type, og:url, og:title, og:description, og:image, og:locale)
  - Twitter Cards (summary_large_image)
  - Geo-теги Яндекса (geo.region, geo.placename, geo.position, ICBM)
  - JSON-LD Schema.org `@graph`: Organization + LocalBusiness + WebSite + WebPage
  - Page-level переменные: `$og_image`, `$og_type`, `$schema_type`, `$article_published`, `$article_modified`

### Следующий слой SEO
- `og_type=article` + Schema.org Article + `article:published_time` для страниц статей
- Schema.org для object pages (тип `Place` или `LocalBusiness` + `GeoCoordinates`)
- Sitemap расширяется при появлении object pages

---

## Карта выполненных работ (реализовано)

Страница `/map/` — интерактивная карта с маркерами 500+ выполненных объектов и кластеризацией.

### Стек
- **Яндекс.Карты JS API v3** (`api-maps.yandex.ru/v3/`)
- **`@yandex/ymaps3-clusterer@0.0.1`** — подключается через `ymaps3.import.registerCdn` с jsdelivr

### Данные
`data/map.json` — массив объектов:
```json
[
  {
    "id": 1,
    "coords": [longitude, latitude],
    "title": "Описание объекта",
    "category": "house",
    "url": "/objects/slug/"
  }
]
```
> ⚠️ Координаты в формате `[longitude, latitude]` (как в GeoJSON/Яндекс v3), не `[lat, lng]`.
> Поле `url` — ссылка на страницу объекта; `null` если страница ещё не создана.

### Категории маркеров (CAT_COLORS)
| Ключ | Цвет | Название |
|---|---|---|
| `house` | `#2563eb` | Жилой дом |
| `banya` | `#16a34a` | Баня |
| `fence` | `#9333ea` | Забор |
| `commercial` | `#ea580c` | Коммерция |
| `industrial` | `#dc2626` | Промышленные |
| `water` | `#0891b2` | Водные объекты |
| `social` | `#ca8a04` | Социальные |
| `agro` | `#65a30d` | Сельхоз |
| `other` | `#6b7280` | Прочее |

### Легенда карты
Блок `.map-legend` под картой:
- CSS Grid `repeat(auto-fill, minmax(140px, 1fr))` — равные колонки, адаптивный wrap
- Каждый элемент: цветная точка (копия маркера) + подпись категории
- Стили изолированы в `pages/map/content.html`

### Ключевые решения
- `data/map.json` лежит в `data/`, а не в `pages/map/` — роутер блокирует `pages/` с 403
- `fetch('/data/map.json')` — абсолютный путь от корня
- `ymaps3.import.registerCdn('https://cdn.jsdelivr.net/npm/{package}', '@yandex/ymaps3-clusterer@0.0.1')` — **строка**, не массив; явная версия обязательна
- `clusterByGrid({ gridSize: 64 })` — метод кластеризации
- Сигнатура `cluster`: `(coordinates, features) => YMapMarker` — координаты первым аргументом
- `behaviors: ['drag', 'pinchZoom', 'scrollZoom', 'dblClick']` — все 4 режима обязательны

---

## Object pages (в разработке)

~500 объектов. Каждый объект — отдельная SEO-страница + кликабельная точка на карте.

### Текущий статус
- Данные 500+ объектов подготовлены через Qwen3.5 Flash: поля восстановлены, категории распределены
- Карта реализована, маркеры кликабельны (поле `url` в `data/map.json`)
- Страницы объектов — **следующий этап работ**

### Контракт данных объекта (требует явной фиксации — открытый вопрос №3)
Предлагаемые поля `data/objects.json`:
```json
{
  "slug": "perm-dom-pervomayskaya-12",
  "title": "Жилой дом, ул. Первомайская 12",
  "excerpt": "Фундамент на винтовых сваях под двухэтажный жилой дом",
  "category": "house",
  "coords": [56.1234, 58.0123],
  "address": "Пермь, ул. Первомайская, 12",
  "year": 2021,
  "media_keys": ["objects-perm-dom-pervomayskaya-12-main", "objects-perm-dom-pervomayskaya-12-2"],
  "status": "published",
  "seo_title": "Фундамент на сваях — жилой дом Первомайская 12 | Гефест",
  "seo_description": "..."
}
```

### SEO-стратегия object pages
- URL: `/objects/{slug}/`
- Уникальность за счёт реальных данных: адрес, категория, год, фото
- Schema.org: `Place` + `GeoCoordinates` + ссылка на `LocalBusiness`
- Связь с картой: `url` в `data/map.json` → `/objects/{slug}/`
- При `build.php`: итерация `data/objects.json` → 500 статических HTML

### Первый пилотный этап
- 3–5 страниц вручную для проверки шаблона, SEO-разметки и image pipeline
- После валидации — programmatic генерация остальных

---

## Image pipeline для объектов (планируется)

Изображения объектов пока хранятся **вне репо**, требуют подготовки перед публикацией.

### Этапы
1. **Инвентаризация** — сопоставить изображения с объектами из `data/objects.json`
2. **Обработка** — нарезка через существующий медиапайплайн (`process-media.js`), ключи по схеме `objects-{slug}-{n}`
3. **Автоматизация** — batch-обработка через CLI или расширение UI медиапайплайна
4. **Generative-модели** — там где фото нет или некачественные: дополнение, восстановление, генерация превью
5. **Публикация** — изображения попадают в `data/media.json` → доступны через `render_image()`

### Принципы
- Нет фото → placeholder или AI-генерация, явно помечается в данных
- Оригиналы хранятся вне git (или Git LFS при большом объёме)
- Ключи медиа: `objects-{slug}-main`, `objects-{slug}-2`, ...

---

## Шаблоны и партиалы

### Layouts
- `layouts/main.php` — стандартный (sidebar + content)
- `layouts/home.php` — главная
- `layouts/wide.php` — без sidebar

### Partials
- `partials/image.php` — хелпер картинок
- `partials/head-favicon.php` — favicon/manifest/meta theme-color
- `partials/head-seo.php` — OG/Twitter Cards/Schema.org/geo (реализован)

### Структура страницы
```php
$title = "...";
$meta_description = "...";
$canonical = "...";
ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
```

---

## Договорённости и решения

| Дата | Решение |
|---|---|
| 2026-04-30 | Медиазапросы: три точки — 480/768/1024px. Выше не нужно. |
| 2026-04-30 | CSS изображений: BEM-модификаторы к `.content-image-wrapper` |
| 2026-04-30 | `<figure>` + `<picture>` + `<figcaption>` — стандарт вставки изображений в текст |
| 2026-04-30 | FullHD/4K медиазапросы не добавляем — max-width: 1200px достаточно |
| 2026-04-30 | CSS не разбиваем на файлы до появления `build.php` |
| 2026-04-30 | sitemap.xml обновляется вручную до `build.php` |
| 2026-05-02 | OG-изображение: 1200×630px |
| 2026-05-02 | Schema.org: `@graph` с `Organization + LocalBusiness + WebSite` |
| 2026-05-03 | `source/` хранится в git. При росте объёма → Git LFS. |
| 2026-05-03 | Ключ media.json = путь от source/ без расширения, слэши → дефисы |
| 2026-05-03 | После нарезки `widths` в JSON перезаписывается реально сгенерированными размерами |
| 2026-05-03 | `orig_width`/`orig_height` хранятся в реестре для `width`/`height` атрибутов и `aspect-ratio` |
| 2026-05-03 | GIF (включая анимированные) → анимированный WebP через `sharp({animated:true})` |
| 2026-05-03 | Удаление: «только запись» или «запись + WebP файлы» — выбор в UI |
| 2026-05-03 | Orphan-файлы (WebP без записи в реестре) удаляются через кнопку «Найти мусор» в UI |
| 2026-05-03 | UI-сервер: порт 3010 (3000 занят) |
| 2026-05-03 | Данные клиента зафиксированы: адрес, geo, часы, VK, индекс 614000, legalName |
| 2026-05-03 | Реализован favicon-комплект и подключение через `partials/head-favicon.php` |
| 2026-05-03 | Деплой пока ручной; автодеплой выносится в отдельный следующий блок |
| 2026-05-08 | Карта: Яндекс.Карты JS API v3 (не MapLibre) — решение принято по факту реализации |
| 2026-05-08 | data/map.json — данные карты вне pages/ (роутер блокирует pages/ с 403) |
| 2026-05-08 | registerCdn: второй аргумент — строка с явной версией '@yandex/ymaps3-clusterer@0.0.1' |
| 2026-05-08 | clusterByGrid({ gridSize: 64 }) — рабочий метод кластеризации |
| 2026-05-09 | behaviors карты: ['drag', 'pinchZoom', 'scrollZoom', 'dblClick'] — все 4 обязательны |
| 2026-05-09 | Легенда карты: CSS Grid repeat(auto-fill, minmax(140px, 1fr)), изолирована в content.html |
| 2026-05-09 | CAT_COLORS — 9 категорий маркеров, зафиксированы в разделе «Карта выполненных работ» |
| 2026-05-14 | GitHub Actions → FTP признан неоптимальным; каноничный деплой — tools/deploy.js |
| 2026-05-14 | OG assets готовы: assets/img/og/og-home.jpg (primary), assets/img/og/og-home.webp (доп.) |
| 2026-05-14 | head-seo.php реализован: OG + Twitter Cards + geo-теги + JSON-LD @graph, подключён во все layouts |
| 2026-05-14 | Qwen3.5 Flash использован для batch-подготовки данных 500+ объектов карты |
| 2026-05-14 | Следующий этап: object pages (пилот 3–5 страниц) + image pipeline для объектов |
| 2026-05-14 | Изображения объектов хранятся вне репо; ключи медиа: objects-{slug}-main, objects-{slug}-N |
| 2026-05-14 | Object page URL: /objects/{slug}/; контракт данных objects.json требует финальной фиксации |
| 2026-05-15 | Изображения каталога свай VSG-1..4 подключаются вручную через `<picture>` и прямые пути `/assets/img/...`, без регистрации в `data/media.json`; реестр нужен для `render_image()`, статичные product-фото можно подключать напрямую. |

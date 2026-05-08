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

### Блокеры (нужно от клиента)
- [ ] OG-изображение 1200×630px — если нет, генерировать из логотипа
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
| 500 объектов — не реализовано | Programmatic SEO через шаблон + данные |

### Граница Zavodsvay ↔ WebForge

| Функция | Статус |
|---|---|
| Медиапайплайн (process-media.js) | Прототип в Zavodsvay → портируется в WebForge |
| Schema.org / OG генерация | Архитектура в WebForge, применение здесь |
| Карта объектов | **Zavodsvay-специфика** с потенциалом стать компонентом WebForge |
| CSS-система компонентов | Решается в WebForge, переносится при миграции |
| `sitemap.xml` генерация | В WebForge при build, здесь вручную до миграции |
| 500 объектов / programmatic SEO | **Zavodsvay-специфика**, но паттерн войдёт в WebForge как data-driven pages |

---

## Текущее состояние проекта

**Дата последнего обновления:** 2026-05-08

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
- **Favicon/manifest подключены** — `favicon.png`, `apple-touch-icon.png`, `site.webmanifest`, `assets/img/icons/icon-192.png`, `assets/img/icons/icon-512.png`
- `partials/head-favicon.php` подключён в `layouts/main.php`, `layouts/home.php`, `layouts/wide.php`
- Ручной деплой на хостинг после обновления favicon уже выполнен
- **Карта выполненных работ** — `/map/`, Яндекс.Карты JS API v3, маркеры + кластеризация

### Ближайшие независимые блоки работ

- [ ] **SEO-разметка** — Open Graph, Twitter Cards, JSON-LD Schema.org, geo-теги
- [x] **Favicon + manifest** — базовый комплект готов и подключён
- [x] **Карта выполненных работ** — реализована с кластеризацией
- [ ] **Карта ~500 объектов** — данные `data/objects.json` + страницы объектов
- [ ] **GitHub Actions** → автодеплой по FTP
- [ ] **`build.php`** — статическая генерация `/dist/`

---

## Открытые архитектурные вопросы

> Эти вопросы **не закрыты**. AI не должен молчаливо принимать решения по ним — требуется явное обсуждение с разработчиком.

1. **CSS naming convention для новых секций.** Текущий `template.css` использует плоские классы (`.sidebar`, `.header`). При миграции на WebForge и scoped `.c-{name}` префиксы потребуется рефакторинг. Нужно ли уже сейчас писать новые секции в финальном стиле?

2. **Git LFS для `source/`.** Порог принятия решения — какой объём оригиналов считается критичным? Решать до, не после роста.

3. **Структура данных объектов.** `data/objects.json` ещё не существует. Формат ключей, структура записи, связь с медиареестром — нужно зафиксировать до начала работы над картой объектов.

4. **`build.php` в Zavodsvay vs в WebForge.** Писать `build.php` здесь как прототип (и потом портировать), или дождаться WebForge-генератора? Влияет на приоритет задач.

5. **hash-инвалидация CSS/JS.** При деплое на shared hosting без CDN браузеры кэшируют старые версии. Нужно ли реализовывать `style.{hash8}.css` до или после `build.php`?

---

## Медиапайплайн (реализовано)

### Структура
```
source/                     ← оригиналы (jpg, png, gif, webp) — хранятся в git
assets/img/                 ← нарезанные WebP-наборы
data/media.json             ← SSOT-реестр всех изображений
tools/
├── process-media.js        ← CLI: сканирует source/, нарезает WebP
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
- `partials/head-favicon.php` для повторного подключения иконок в layout
- include этого partial во всех layout: `main`, `home`, `wide`

### Следующий слой SEO
- Open Graph + Twitter Cards + VK
- JSON-LD Schema.org (`@graph` в `<head>`)
- geo-теги Яндекса
- og:image 1200×630

---

## Карта выполненных работ (реализовано)

Страница `/map/` — интерактивная карта с маркерами выполненных объектов и кластеризацией.

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
    "url": null
  }
]
```
> ⚠️ Координаты в формате `[longitude, latitude]` (как в GeoJSON/Яндекс v3), не `[lat, lng]`.

### Ключевые решения
- `data/map.json` лежит в `data/`, а не в `pages/map/` — роутер блокирует `pages/` с 403
- `fetch('/data/map.json')` — абсолютный путь от корня
- `ymaps3.import.registerCdn('https://cdn.jsdelivr.net/npm/{package}', '@yandex/ymaps3-clusterer@0.0.1')` — **строка**, не массив; явная версия обязательна
- `clusterByGrid({ gridSize: 64 })` — метод кластеризации
- Сигнатура `cluster`: `(coordinates, features) => YMapMarker` — координаты первым аргументом

### Планируемое расширение
При добавлении ~500 объектов: `data/map.json` расширяется теми же полями. Страницы объектов — отдельный блок работ (programmatic SEO).

---

## Карта объектов (запланировано)

~500 реализованных объектов. Каждый объект — отдельная SEO-страница + точка на интерактивной карте.

### SEO-стратегия
- `build.php` итерирует `data/objects.json` → 500 статических HTML-страниц
- Programmatic SEO: уникальность за счёт реальных данных, не шаблонного текста
- Структура `data/objects.json` — **открытый вопрос**, требует проектирования до реализации

---

## Шаблоны и партиалы

### Layouts
- `layouts/main.php` — стандартный (sidebar + content)
- `layouts/home.php` — главная
- `layouts/wide.php` — без sidebar

### Partials
- `partials/image.php` — хелпер картинок
- `partials/head-favicon.php` — favicon/manifest/meta theme-color

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

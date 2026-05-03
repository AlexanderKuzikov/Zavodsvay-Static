# CONTEXT.md — Контекст разработки

> Этот файл — живой документ для AI-ассистента и разработчика.  
> Обновляется после каждой сессии/задачи. Дополняет README.md деталями, решениями и договорённостями.

---

## Режим работы с AI

- Основной инструмент: Perplexity (Space: Zavodsvay), GitHub MCP
- AI имеет право коммитить напрямую в `main` после явного подтверждения
- Браузером AI не управляет — только GitHub API
- Перед каждой сессией рекомендуется дать AI прочитать этот файл
- **Важно:** AI не должен молчаливо соглашаться с архитектурными решениями — открытые вопросы (см. ниже) требуют явного обсуждения, не автоматического принятия

---

## Данные клиента (Завод «Гефест»)

> Используются для Schema.org, OG-разметки, geo-тегов, favicon, site.webmanifest.

```
Название:        Завод винтовых свай «Гефест»
Юр. название:   (уточнить при необходимости)
Сайт:            https://zavodsvay.ru/
Телефон:         +7 (342) 20-99-800  →  +73422099800
Email:           info@zavodsvay.ru
Адрес:           г. Пермь, ул. Монастырская, 14, офис 502
Город:           Пермь
Регион:          RU-PER (Пермский край)
Индекс:          (уточнить)

Координаты:
  Decimal:       58.014746, 56.228500
  DMS:           58°0′53″N, 56°13′43″E
  Яндекс.Карты:  https://yandex.ru/maps/-/CPWQYF-0

Режим работы:
  Пн–Пт:         09:00–18:00
  Сб–Вс:         Выходной

Соцсети:
  VK:            https://vk.com/club236711949
  Telegram:      https://t.me/zavodsvay

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
- [ ] Логотип SVG или PNG 512px+ — для favicon и og:image
- [ ] Юридическое название (ООО/ИП?) — для Schema.org `legalName`
- [ ] Почтовый индекс — для `postalCode` в Schema.org
- [ ] OG-изображение 1200×630px — если нет, генерировать из логотипа

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
| Карта объектов (MapLibre + PMTiles) | **Zavodsvay-специфика** с потенциалом стать компонентом WebForge |
| CSS-система компонентов | Решается в WebForge, переносится при миграции |
| `sitemap.xml` генерация | В WebForge при build, здесь вручную до миграции |
| 500 объектов / programmatic SEO | **Zavodsvay-специфика**, но паттерн войдёт в WebForge как data-driven pages |

---

## Текущее состояние проекта

**Дата последнего обновления:** 2026-05-03

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

### Ближайшие независимые блоки работ

- [ ] **SEO-разметка** — Open Graph, Twitter Cards, JSON-LD Schema.org, geo-теги
- [ ] **Favicon + manifest** — SVG-иконка, `apple-touch-icon`, `site.webmanifest`, `theme-color`
- [ ] **Карта объектов** — ~500 объектов, страница каждого, интерактивная карта
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

### Система классов изображений в тексте

```
.content-image-wrapper          — базовый блок (border-radius, shadow, user-select)
.content-image-wrapper--left    — float left, 45% ширины
.content-image-wrapper--right   — float right, 45% ширины
.content-image-wrapper--full    — полная ширина, clear both
.content-image-wrapper--center  — 60%, auto margin, без float
.content-clearfix               — сброс float после последнего абзаца с обтеканием
```

---

## SEO-файлы (реализовано)

### sitemap.xml
- 9 основных страниц + 28 статей = **37 URL**
- **TODO при добавлении новой статьи:** добавить URL вручную до реализации `build.php`

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
Требования к OG-изображению: **1200×630px**, ≤1 МБ, файл `assets/img/og/og-home.jpg`.

### JSON-LD Schema.org
Стратегия: `@graph` в `<head>` на каждой странице.
- **Все страницы:** `Organization + LocalBusiness + WebSite`
- **Статьи:** дополнительно `Article` с `datePublished`, `ImageObject`
- **Страницы объектов:** `LocalBusiness` с `geo`, `photo[]`, `additionalProperty[]`

### Geo-теги (Яндекс)
```html
<meta name="geo.region" content="RU-PER">
<meta name="geo.placename" content="Пермь">
<meta name="geo.position" content="58.014746;56.228500">
<meta name="ICBM" content="58.014746, 56.228500">
```

---

## Favicon и манифест (запланировано)

| Файл | Размер | Назначение |
|---|---|---|
| `favicon.svg` | вектор | Современные браузеры |
| `favicon.ico` | 32×32 | Fallback |
| `apple-touch-icon.png` | 180×180 | iOS |
| `assets/img/icons/icon-192.png` | 192×192 | Android/PWA |
| `assets/img/icons/icon-512.png` | 512×512 | PWA splash |
| `site.webmanifest` | — | Android/PWA |

**Блокер:** нужен исходный SVG или PNG 512px+ логотипа Гефест.

---

## Карта объектов (запланировано)

~500 реализованных объектов. Каждый объект — отдельная SEO-страница + точка на интерактивной карте.

### Стратегия карты
**MapLibre GL + PMTiles** — единый бинарный файл карты региона, WebGL-рендеринг, полная автономность.

### SEO-стратегия
- `build.php` итерирует `data/objects.json` → 500 статических HTML-страниц
- Programmatic SEO: уникальность за счёт реальных данных, не шаблонного текста
- Структура `data/objects.json` — **открытый вопрос**, требует проектирования до реализации

---

## Шаблоны и партиалы

### Layouts
- `layouts/main.php` — стандартный (sidebar + content)
- `layouts/home.php` — главная (splash + content)
- `layouts/wide.php` — без sidebar

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
| 2026-05-02 | Карта объектов: MapLibre GL + PMTiles (автономность) |
| 2026-05-02 | OG-изображение: 1200×630px |
| 2026-05-02 | Schema.org: `@graph` с `Organization + LocalBusiness + WebSite` |
| 2026-05-02 | Favicon: SVG + ICO + 180px + 192px + 512px + `site.webmanifest` |
| 2026-05-03 | `source/` хранится в git. При росте объёма → Git LFS. |
| 2026-05-03 | Ключ media.json = путь от source/ без расширения, слэши → дефисы |
| 2026-05-03 | После нарезки `widths` в JSON перезаписывается реально сгенерированными размерами |
| 2026-05-03 | `orig_width`/`orig_height` хранятся в реестре для `width`/`height` атрибутов и `aspect-ratio` |
| 2026-05-03 | GIF (включая анимированные) → анимированный WebP через `sharp({animated:true})` |
| 2026-05-03 | Удаление: «только запись» или «запись + WebP файлы» — выбор в UI |
| 2026-05-03 | Orphan-файлы (WebP без записи в реестре) удаляются через кнопку «Найти мусор» в UI |
| 2026-05-03 | UI-сервер: порт 3010 (3000 занят) |
| 2026-05-03 | Данные клиента (контакты, geo, часы работы) зафиксированы в CONTEXT.md |

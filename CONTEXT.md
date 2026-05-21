# CONTEXT.md — Контекст разработки

> Этот файл — живой документ для AI-ассистента и разработчика.  
> Обновляется после каждой сессии/задачи. Дополняет README.md деталями, решениями и договорённостями.

---

## Режим работы с AI

- Основной инструмент: Perplexity (Space: Zavodsvay), GitHub MCP
- AI может коммитить напрямую в `main`, если пользователь явно просит «действуй / сделай сам / коммить»
- Если push по MCP не доведён до результата — AI сохраняет файлы в чат для скачивания
- Браузером AI не управляет — только GitHub API
- Перед каждой сессией рекомендуется дать AI прочитать этот файл
- **Важно:** AI не должен молчаливо соглашаться с архитектурными решениями — открытые вопросы требуют явного обсуждения

---

## Данные клиента (Завод «Гефест»)

> Используются для Schema.org, OG-разметки, geo-тегов, favicon, site.webmanifest.

```
Название:        Завод винтовых свай «Гефест»
Юр. название:   ООО "Завод Винтовых Свай \"Гефест\""
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

### Для geo-тегов
```html
<meta name="geo.region" content="RU-PER">
<meta name="geo.placename" content="Пермь">
<meta name="geo.position" content="58.014746;56.228500">
<meta name="ICBM" content="58.014746, 56.228500">
```

### OG-изображения (готовы)
- `assets/img/og/og-home.jpg` — primary
- `assets/img/og/og-home.webp` — дополнительный
- Размер: 1200×630px

---

## Связь с WebForge

Этот проект — **первый production-кейс** [WebForge](https://github.com/AlexanderKuzikov/WebForge).

| Сейчас (pre-static) | После WebForge |
|---|---|
| PHP file-router, ручной роутинг | `build.php` → pure static HTML |
| Ручной `sitemap.xml` | Динамическая генерация из структуры |
| Медиапайплайн через `tools/` | Пайплайн становится частью WebForge |
| Schema.org/OG вручную | Генерация из данных объектов |
| Object pages через `_template.php` | Programmatic SEO через шаблон + данные |

---

## Текущее состояние проекта

**Дата последнего обновления:** 2026-05-21

### Что реализовано
- Файловый PHP-роутер, layouts (main, home, wide), partials
- Все основные страницы: index, catalog, prices, calc, montage, articles, contacts, map, document, 404
- 31 страница статей в `pages/articles/{slug}/`
- Адаптивная верстка на нативном CSS (один файл `assets/css/template.css`)
- Hero-видео на главной, WebP-изображения с srcset
- `sitemap.xml`, `robots.txt`, `.htaccess`
- Медиапайплайн — `data/media.json` + `tools/process-media.js` + `tools/server.js` + UI
- Деплой — `tools/deploy.js` (локальный FTP)
- Favicon/manifest комплект, `partials/head-favicon.php`
- `partials/head-seo.php` — OG, Twitter Cards, JSON-LD Schema.org `@graph`, geo-теги
- OG-изображения: `assets/img/og/og-home.jpg` + `og-home.webp`
- **Карта выполненных работ** — `/map/`, Яндекс.Карты JS API v3, 500+ маркеров, кластеризация, легенда
- **Фильтр по категориям** — интерактивная легенда с solo-click, toggle, reset, счётчик; реализована на `/map/` **и** на странице объекта
- **Поиск по карте** — текстовый поиск по `title` + `techDescription` (debounce 300ms), dropdown до 15 результатов со ссылками, совместная работа с фильтром категорий через единую `applyFilters()`
- **Object pages** — `pages/objects/_template.php` + 529 programmatically сгенерированных страниц объектов
- **Карта на странице объекта** — центрирование на объекте zoom 13, пульсирующий маркер текущего объекта, навигация по остальным, легенда-фильтр идентична `/map/`
- Корневые npm-скрипты — `npm run ui/media/deploy` из корня
- Локальные шрифты `Open Sans Local` и `Roboto Slab Local` подключены через `@font-face`
- **SEO статей** — все 29 `pages/articles/*/index.php` переведены на `og_type=article`, `schema_type=Article`, реалистичные `article_published` / `article_modified` по тематике (2022–2026)
- **Страница каталога** `/catalog/` — карточки свай серии ВСГ, адаптивные таблицы, accordion FAQ, CTA-блок с калькулятором
- **Страница цен** `/prices/` — прайс-лист по 7 группам (диаметр/лопасть/грунт), таблицы с весом и надбавками
- **Страница монтажа** `/montage/` — этапы монтажа, техника (`.catalog-features`), таблица стоимости (`.catalog-table--responsive`), FAQ (`.catalog-faq`), CTA
- **Главная страница** — вариант `/preview-d/` согласован с заказчиком и **выполнен** (2026-05-20)

### Ближайшие задачи

- [x] Object pages (529 страниц) — **готово**
- [x] Карта на странице объекта — **готово**
- [x] Фильтрация по категориям — общая карта и страница объекта — **готово**
- [x] SEO статей — `og_type=article`, Schema.org Article, реалистичные даты по тематике — **готово (2026-05-21)**
- [x] Страница каталога (`/catalog/`) — карточки, таблицы, FAQ, CTA — **готово**
- [x] Страница цен (`/prices/`) — прайс-лист, аудит, баг DOCTYPE исправлен — **готово**
- [x] **Поиск на карте** — `title` + `techDescription`, debounced dropdown, 15 результатов, совместная работа с фильтром — **готово**
- [x] **Страница монтажа** (`/montage/`) — доработка оформления, FAQ, таблица стоимости — **готово**
- [x] **Главная страница** — вариант `/preview-d/` согласован с заказчиком, **выполнено** (2026-05-20)
- [ ] **Синхронизация номенклатуры** — диаметры в прайсе vs каталоге (ждём ответа заказчика)
- [ ] **Image pipeline для объектов** — нарезка изображений, batch-автоматизация, generative-модели
- [ ] `build.php` — статическая генерация `/dist/`

---

## SEO статей — даты публикации (реализовано, 2026-05-21)

### Статус
Все 29 статей получили реалистичные даты `article_published` / `article_modified` вместо заглушки `2026-01-01`.  
Коммит: `1493012399923367b463605da2c7c68a5a5d36fb`

### Логика дат

| Группа | Примеры slugов | published | modified |
|---|---|---|---|
| Evergreen-база | `vidy`, `tehnologiy`, `preimushestva` | 2022 | 2024 |
| История | `history` | 2022-11 | 2023-10 |
| Технические | `grunt`, `raschet`, `stroitelstvo-fundamenta` | 2023 | 2025 |
| Материалы домов | `brevno`, `brus`, `karkasny`, `penobeton`, `beton` | 2023 | 2024 |
| Монтаж (весна) | `ruchnoy`, `mashin`, `rostverk`, `razmeshenie-svay` | апр–июнь 2024 | 2025 |
| Сезонные | `zima` | декабрь 2024 | ноябрь 2025 |
| Объекты/применения | `angar`, `bania`, `podpornaystena` | авг–июль 2024 | 2025 |
| Диагностика/испытания | `ispytaniy`, `probnoe`, `oshibki` | окт–нояб 2024 | 2025 |
| Коммерческие (цены) | `stoimost-svay`, `dostavka-svay`, `arenda-yamobura` | 2025 | 2026 |
| Свежие | `otzyvy`, `nakon` | 2025–2026 | 2026 |

### Правило
- `published < modified` всегда
- `modified` ≤ дата последнего коммита
- Сезонный slug (`zima`) → published в декабре, `arenda-yamobura` → апрель

---

## Структура данных объектов

### Текущий SSOT: `data/map.json`

Массив объектов для карты и object pages:
```json
{
  "id": 3,
  "coords": [56.5178955, 57.9237226],
  "category": "house",
  "title": "...",
  "techDescription": "...",
  "images": ["3_1.webp"],
  "url": "/objects/3/"
}
```

> ⚠️ **Координаты в `data/map.json`** хранятся как `[latitude, longitude]`.  
> ymaps3 JS API также принимает их в формате `[latitude, longitude]` — **перевод НЕ нужен**.  
> Не делать swap `[coords[1], coords[0]]` — проверено эмпирически.

### `data/objects.json` — **не создан** (открытый архитектурный вопрос)

`data/objects.json` в репозитории **отсутствует**. В `data/` находятся:
- `data/map.json` — SSOT карты и object pages (209 KB, 500+ объектов)
- `data/media.json` — реестр медиафайлов
- `data/components/` — компоненты данных
- PDF-документация (ГОСТ, СП и др.)

Решение о создании `data/objects.json` как расширенного SEO-реестра (с `metaTitle`, `metaDescription`, `schema` и пр.) **отложено** до проработки контракта данных. Текущий `data/map.json` является единственным источником истины для объектов. Вопрос открыт — см. «Открытые архитектурные вопросы» п. 3.

---

## Главная страница (выполнено, 2026-05-20)

### Статус
- Вариант `/preview-d/` разработан, согласован с заказчиком и **принят** как финальная главная
- Дата завершения: **2026-05-20**

### Архитектура
- `layouts/preview-d.php` — layout с собственными стилями (`.hd-*` namespace), не наследует styles из `main.php`
- CSS инлайн в layout, изолирован через `.hd-` prefix
- `pages/index/content.html` — старый SEO-контент; при деплое заменяется финальной версией

### Структура страницы (все секции реализованы)
| Секция | CSS-класс | Примечание |
|---|---|---|
| Hero + видео | `.hd-hero` | autoplay loop muted video |
| Для кого | `.hd-audience__grid` | 4 карточки аудитории |
| Почему Гефест | `.hd-advantages__grid` | 6 преимуществ |
| Услуги | `.hd-services__grid` | 4 карточки услуг |
| Сваи vs лента/плита | `.hd-why` | сравнительная секция с фото |
| Числа / статистика | `.hd-section--orange` | цифры компании |
| Примеры работ | `.hd-cases__grid--6` | 6 объектов с картой |
| CTA | *(завершающий блок)* | |

---

## Страница монтажа `/montage/` (реализовано)

### Архитектура
- `pages/montage/index.php` — подключает `catalog.css` через `$extra_css` паттерн (так же как `/catalog/`)
- `pages/montage/content.html` — весь контент
- CSS — переиспользует `assets/css/catalog.css` (классы `.catalog-faq`, `.catalog-table`, `.catalog-features`, `.catalog-cta`)

### Структура страницы
1. Вводный параграф
2. Этапы монтажа — нумерованный `<ol>` (6 шагов)
3. Наша техника — нумерованные карточки `.catalog-features` (4 единицы)
4. Стоимость работ — таблица `.catalog-table catalog-table--responsive` в `.catalog-table-wrap`, `data-label` на каждой `<td>`, пояснения в `<small>`
5. Частые вопросы — `.catalog-faq` / `<details class="catalog-faq__item">` / `<summary class="catalog-faq__q">` / `<div class="catalog-faq__a"><p>` (6 вопросов)
6. CTA-блок `.catalog-cta` с кнопкой на калькулятор

### Ключевые решения
| Дата | Решение |
|---|---|
| 2026-05-19 | `$extra_css = catalog.css` добавлен в `pages/montage/index.php` — без него `.catalog-faq` рендерился как голый `<details>` со стрелкой `▶` |
| 2026-05-19 | Таблица стоимости: `.catalog-table--responsive` + `data-label` на `<td>` + `<small>` для пояснений |
| 2026-05-19 | Секция техники переведена с `<ul>` на `.catalog-features` — визуально единообразно с каталогом |
| 2026-05-19 | FAQ: разметка идентична `/catalog/` — 2-space indent, `<details>` без лишних переносов |

---

## Страница цен `/prices/` (реализовано, на уточнении)

### Структура
- `pages/prices/content.html` — автономный фрагмент (без DOCTYPE-обёртки)
- Inline `<style>` в content.html — изолированные стили прайса (`.price-group`, `.price-group-header`, `.table-wrap`)
- 7 групп: Ø60/5,0 → Ø73/5,5-250 → Ø73/5,5-300 (слаб.) → Ø89/6,5-300 → Ø89/6,5-350 (слаб.) → Ø102/6,5 → Ø114/7,0
- Каждая группа: заголовок с нагрузкой на сжатие и стоимостью усиления лопасти + таблица (длина/ВСГ-1/ВСГ-2/ВСГ-3/вес)

### Известные проблемы (ждём ответа заказчика)

| # | Проблема | Статус |
|---|---|---|
| 1 | Диаметры в прайсе (60, 73, 102, 114 мм) ≠ диаметрам в каталоге (57, 76, 89, 108... мм) | ⏳ Ждём ответа |
| 2 | Ø114 / 4000 мм: ВСГ-3 = 6 260 ₽ (должно быть 6 160 ₽ по паттерну +100 к ВСГ-2) | ⏳ Ждём ответа |
| 3 | Ø73 лопасть 300 мм (слабые грунты) даёт нагрузку 10 000 кг > 9 500 кг лопасти 250 мм | ⏳ Ждём ответа |
| 4 | Ø102 и Ø114 — одинаковая нагрузка 13 000 кг при разных диаметрах и цене | ⏳ Ждём ответа |

### Исправленные баги
- `2026-05-18` — `pages/prices/content.html` содержал полный `<!DOCTYPE html>` с `<html>/<head>/<body>` → убрано, оставлен только контент-фрагмент

---

## Страница каталога `/catalog/` (реализовано)

### Архитектура
- `pages/catalog/index.php` — подключает `catalog.css` через `$extra_css` паттерн
- `pages/catalog/content.html` — весь контент: карточки, таблицы, FAQ, CTA
- `assets/css/catalog.css` — изолированные стили каталога
- `layouts/main.php` поддерживает `$extra_css` — page-specific CSS без глобального загрязнения

### Паттерн page-specific CSS
```php
// pages/catalog/index.php  (и pages/montage/index.php)
$extra_css = '<link rel="stylesheet" href="/assets/css/catalog.css">';
```
```php
// layouts/main.php (в <head>)
<?php if (!empty($extra_css)) echo $extra_css; ?>
```

### Responsive таблицы
- **Таблица сравнения** (`catalog-table--responsive`): на mobile (`≤768px`) скрывает `<thead>`, каждый `<td>` рендерится как `display: grid; grid-template-columns: 130px 1fr`, лейбл берётся из `data-label` через `::before`
- **Таблица технических характеристик**: обычная, `overflow-x: auto` — горизонтальный скролл на мобайле

### Ключевые решения
| Дата | Решение |
|---|---|
| 2026-05-18 | `$extra_css` паттерн в layouts/main.php — page-specific стили без глобального засорения |
| 2026-05-18 | Таблица сравнения responsive: `display: grid; grid-template-columns: 130px 1fr` вместо `flex + space-between` |
| 2026-05-18 | Таблица теххарактеристик (2 колонки): НЕ responsive, стандартный горизонтальный скролл |
| 2026-05-18 | `.catalog-cta__title`: `color: #fff !important` — перебивает наследование цвета h2 из template.css |
| 2026-05-18 | Изображения свай: `width: 270px` (×1.5 от исходных 180px), `max-height: 390px` |

---

## Локальные шрифты

### Принцип именования
- Используются локальные имена семейств: `Open Sans Local` и `Roboto Slab Local`
- Суффикс `Local` нужен, чтобы исключить конфликт с системно установленными шрифтами
- В `body` используется `font-family: 'Open Sans Local', sans-serif;`
- В заголовках используется `font-family: 'Roboto Slab Local', serif;`

### Правильное подключение кириллицы
- Для сабсетов с кириллицей и латиницей нельзя полагаться на «первый файл в src» без `unicode-range`
- Правильная схема: **два отдельных `@font-face` блока** на одно family, каждый со своим `unicode-range`
- Для проверки файлов шрифтов использовать [wakamaifondue.com](https://wakamaifondue.com)
- Fallback у заголовков — просто `serif`, без `Georgia`

---

## Открытые архитектурные вопросы

> Эти вопросы **не закрыты**. AI не должен молчаливо принимать решения по ним.

1. **CSS naming convention для новых секций.** При миграции на WebForge потребуется рефакторинг под `.c-{name}` префиксы.

2. **Git LFS для `source/`.** Порог принятия решения — какой объём оригиналов считается критичным?

3. **Контракт данных объекта.** `data/map.json` — текущий SSOT. Файл `data/objects.json` **не создан** — в `data/` он отсутствует. При масштабировании потребуется отдельный реестр с расширенными SEO-полями (`metaTitle`, `metaDescription`, Schema.org-специфика). До принятия решения по контракту данных — не создавать.

4. **`build.php` в Zavodsvay vs в WebForge.** Писать здесь как прототип или дождаться WebForge?

5. **hash-инвалидация CSS/JS.** Нужна ли до `build.php`?

6. **Image pipeline для объектов.** Структура хранения, роль generative-моделей, связка с `data/media.json`.

7. **Хранение изображений объектов.** Сейчас в `pages/map/img/{id}_N.webp`. При масштабировании — вынести в `assets/img/objects/`.

8. **Переключатель типов карты.** Схема/спутник/гибрид в ymaps3 официально недоступны. Обсудить с заказчиком.

9. ~~**Главная страница.**~~ — **Закрыто.** Вариант `/preview-d/` согласован и выполнен (2026-05-20).

10. **Номенклатура диаметров свай.** Диаметры в прайсе (60, 73, 102, 114 мм) не совпадают с диаметрами в каталоге (57, 76, 89, 108... мм). Ждём ответа заказчика.

---

## AI в проекте

| Инструмент | Применение |
|---|---|
| Perplexity (Space: Zavodsvay) | Основной AI-ассистент разработки; архитектурные решения, коммиты через GitHub MCP |
| Qwen3.5 Flash (облачный) | Подготовка данных карты: восстановление полей, нормализация, категоризация 500+ объектов |
| Generative-модели (планируется) | Image pipeline объектов: дополнение, восстановление, генерация превью |

---

## Медиапайплайн (реализовано)

### Структура
```
source/                     ← оригиналы — в git
assets/img/                 ← нарезанные WebP-наборы
data/media.json             ← SSOT-реестр
tools/
├── process-media.js
├── deploy.js
├── server.js
├── ui/index.html
└── package.json
package.json                ← корень: прокси-скрипты
partials/image.php          ← render_image()
```

### Когда регистрировать в media.json

| Случай | Подход |
|---|---|
| Переиспользуется / управляется через UI | В реестр, `render_image()` |
| Статичное product-фото на одной странице | `<picture srcset>` напрямую |

---

## SEO-файлы (реализовано)

- `favicon.png`, `apple-touch-icon.png`, `site.webmanifest`, `assets/img/icons/icon-192/512.png`
- `partials/head-favicon.php` → подключён во все layouts
- `assets/img/og/og-home.jpg` (primary, 1200×630) + `og-home.webp`
- `partials/head-seo.php` → OG + Twitter Cards + JSON-LD `@graph` + geo-теги
- Article pages: `og_type=article`, Schema.org `Article`, `article:published_time`, `article:modified_time` — **реализовано** для всех 29 статей, реалистичные даты 2022–2026 по тематике (2026-05-21)

---

## Карта выполненных работ (реализовано)

### Стек
- Яндекс.Карты JS API v3, `@yandex/ymaps3-clusterer@0.0.1` via jsdelivr

### Данные
`data/map.json` — массив объектов:
```json
{
  "id": 3,
  "coords": [56.5178955, 57.9237226],
  "category": "house",
  "title": "...",
  "techDescription": "...",
  "images": ["3_1.webp"],
  "url": "/objects/3/"
}
```

> ⚠️ **Координаты в `data/map.json`** хранятся как `[latitude, longitude]`.  
> ymaps3 JS API также принимает их в формате `[latitude, longitude]` — **перевод НЕ нужен**.  
> Не делать swap `[coords[1], coords[0]]` — проверено эмпирически.

### Ключевые решения и известные грабли ymaps3

| Проблема | Решение |
|---|---|
| Клик на маркер не работает | `onClick(event, mapEvent) { mapEvent.stopPropagation() }` в props маркера — не DOM-событие! |
| `__DIR__` в require-файле | Указывает на директорию шаблона, не вызывающего. Передавать `$object_dir = __DIR__` из index.php |
| `registerCdn` второй аргумент | Строка, не массив: `'@yandex/ymaps3-clusterer@0.0.1'` — явная версия обязательна |
| Порядок слоёв в дереве карты | Маркеры/кластеры добавлять на `map`, а не на слой |
| `behaviors` карты | `['drag', 'pinchZoom', 'scrollZoom', 'dblClick']` — все 4 обязательны |

### Спутниковый слой и переключатель типов карты

> ⛔ **В ymaps3 (JS API v3) официального спутникового слоя нет.**  
> Варианты для заказчика: остаться на схеме, тема light/dark, миграция на ymaps2, сторонние тайлы.

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

---

## Object Pages (реализовано)

### Архитектура
- `pages/objects/_template.php` — единый шаблон
- `pages/objects/{id}/index.php` — двустрочник:
```php
<?php
$object_id  = 3;
$object_dir = __DIR__;
require __DIR__ . '/../_template.php';
```

### Добавление нового объекта
1. Создать `pages/objects/{id}/index.php`
2. Убедиться что объект в `data/map.json` с `"url": "/objects/{id}/"`
3. Добавить изображения: `pages/map/img/{id}_1.webp`, `{id}_2.webp`, ...
4. Обновить `sitemap.xml`

---

## Шаблоны и партиалы

### Layouts
- `layouts/main.php` — стандартный (sidebar + content), поддерживает `$extra_css`
- `layouts/home.php` — главная (старая)
- `layouts/wide.php` — без sidebar
- `layouts/preview-d.php` — **финальная главная страница** (`.hd-*` namespace, выполнено 2026-05-20)

### Partials
- `partials/image.php` — `render_image()`
- `partials/head-favicon.php` — favicon/manifest
- `partials/head-seo.php` — OG/Twitter Cards/Schema.org/geo

---

## Договорённости и решения

| Дата | Решение |
|---|---|
| 2026-04-30 | Медиазапросы: 480/768/1024px |
| 2026-04-30 | CSS изображений: BEM-модификаторы к `.content-image-wrapper` |
| 2026-04-30 | `<figure>` + `<picture>` + `<figcaption>` — стандарт вставки изображений |
| 2026-04-30 | CSS не разбиваем на файлы до `build.php` |
| 2026-04-30 | sitemap.xml обновляется вручную до `build.php` |
| 2026-05-02 | OG-изображение: 1200×630px |
| 2026-05-02 | Schema.org: `@graph` с `Organization + LocalBusiness + WebSite` |
| 2026-05-03 | `source/` в git. При росте → Git LFS |
| 2026-05-03 | Ключ media.json = путь от source/ без расширения, слэши → дефисы |
| 2026-05-03 | UI-сервер: порт 3010 |
| 2026-05-03 | Деплой — tools/deploy.js (FTP, локальный) |
| 2026-05-08 | Карта: Яндекс.Карты JS API v3 |
| 2026-05-08 | data/map.json — вне pages/ (роутер блокирует pages/ с 403) |
| 2026-05-08 | registerCdn: второй аргумент — строка с явной версией |
| 2026-05-09 | behaviors: ['drag', 'pinchZoom', 'scrollZoom', 'dblClick'] — все 4 |
| 2026-05-09 | Легенда: CSS Grid repeat(auto-fill, minmax(140px, 1fr)) |
| 2026-05-14 | GitHub Actions → FTP признан неоптимальным; деплой — tools/deploy.js |
| 2026-05-14 | head-seo.php реализован, подключён во все layouts |
| 2026-05-14 | Qwen3.5 Flash — batch-подготовка данных 500+ объектов |
| 2026-05-15 | Статичные product-фото подключаются напрямую через `<picture>`, без реестра |
| 2026-05-15 | Корневой package.json с прокси-скриптами; `npm install` только в `tools/` |
| 2026-05-15 | Object pages реализованы: `_template.php` + 529 страниц |
| 2026-05-15 | `$object_dir = __DIR__` передаётся из index.php в шаблон |
| 2026-05-15 | ymaps3 onClick: `mapEvent.stopPropagation()` в props маркера |
| 2026-05-15 | Координаты в map.json: `[lat, lng]` — подставляются напрямую без перевода |
| 2026-05-15 | Карта на странице объекта: zoom 13, текущий маркер `#f97316` + `marker-pulse`, zIndex 100 |
| 2026-05-16 | ymaps3: спутниковый слой официально отсутствует в API v3 |
| 2026-05-16 | Карта `/map/`: фильтр по категориям реализован |
| 2026-05-16 | Легенда-фильтр добавлена на страницу объекта — идентична `/map/` |
| 2026-05-17 | Локальные шрифты приведены к единому неймингу; fallback = `serif` без Georgia |
| 2026-05-17 | SEO статей завершено: все 31 статья, `og_type=article`, даты `2026-01-01` |
| 2026-05-18 | Каталог: `$extra_css` паттерн — page-specific стили |
| 2026-05-18 | Страница цен: баг DOCTYPE исправлен; аудит прайса — ждём ответа заказчика |
| 2026-05-18 | Поиск по карте: `applyFilters()` — единая точка фильтрации |
| 2026-05-19 | Монтаж: `$extra_css = catalog.css` — корневая причина сломанного FAQ |
| 2026-05-19 | Страница монтажа завершена |
| 2026-05-20 | **Главная страница завершена.** Вариант `/preview-d/` (`layouts/preview-d.php`, `.hd-*`) согласован и принят заказчиком |
| 2026-05-21 | **SEO статей: даты исправлены.** 29 `pages/articles/*/index.php` — реалистичные `published/modified` по тематике, коммит `14930123` |
| 2026-05-21 | `data/objects.json` отсутствует в репо; SSOT объектов — `data/map.json`; создание отложено до проработки контракта данных |

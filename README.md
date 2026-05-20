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

> **Статус:** pre-static PHP-версия. Object pages — 529 страниц programmatically сгенерированы. Фильтрация по категориям на карте реализована (общая карта + страница объекта). Текстовый поиск по карте реализован (title + techDescription, debounced, dropdown с результатами). Локальные шрифты приведены к единому неймингу и подключены через раздельные latin/cyrillic `@font-face`. SEO-разметка всех 31 статьи полностью реализована. Страница каталога свай ВСГ, страница цен, страница монтажа — реализованы. **Главная страница: вариант `/preview-d/` согласован с заказчиком и находится в доработке.** Целевое состояние — pure static HTML через `build.php` после готовности WebForge-генератора.

---

## Архитектура

**Текущий режим:** pre-static PHP-сайт без фреймворков и зависимостей на хостинге.

```
Zavodsvay-Static/
├── pages/              ← страницы ({slug}/index.php + content.html)
│   ├── catalog/        ← каталог свай ВСГ (content.html + catalog.css)
│   ├── prices/         ← прайс-лист
│   ├── montage/        ← монтаж свай (переиспользует catalog.css)
│   ├── articles/       ← 31 статья
│   ├── map/            ← карта выполненных работ (content.php)
│   └── objects/        ← страницы объектов
│       ├── _template.php   ← единый шаблон для всех объектов
│       ├── 1/index.php     ← двустрочник: $object_id + require _template
│       └── ... (529 страниц)
├── layouts/            ← шаблоны (main, home, wide, preview-d)
├── partials/           ← переиспользуемые компоненты
├── assets/
│   ├── css/
│   │   ├── template.css   ← глобальные стили
│   │   └── catalog.css    ← page-specific стили (catalog + montage)
│   ├── img/               ← нарезанные WebP-наборы + icons/ + og/
│   └── fonts/             ← локальные woff2-шрифты
├── source/             ← оригиналы изображений — в git
├── data/
│   ├── media.json      ← SSOT-реестр изображений
│   ├── map.json        ← данные 529 точек карты выполненных работ
│   └── objects.json    ← реестр объектов (в разработке)
├── video/
├── tools/              ← медиапайплайн + деплой (Node.js, только локально)
├── package.json        ← корневые npm-скрипты (прокси к tools/)
├── index.php           ← файловый роутер
├── sitemap.xml
├── robots.txt
├── .htaccess
├── CONTEXT.md          ← живой документ разработки для AI + разработчика
└── README.md
```

---

## Медиапайплайн

Локальный инструмент для работы с изображениями. На хостинг **не деплоится**.

### Первый запуск

```bash
cd tools && npm install   # установить зависимости — один раз
```

### Команды (из корня репозитория)

```bash
npm run ui       # Media UI → http://localhost:3010
npm run media    # CLI-нарезка без UI
npm run deploy   # FTP-деплой на хостинг
npm run deploy:dry   # деплой — dry run (ничего не отправляет)
npm run deploy:full  # принудительный полный деплой
```

> `npm install` нужен только один раз в `tools/`. Корневой `package.json` не имеет своих зависимостей — только прокси-скрипты.

### Как работает

1. Бросаешь оригинал в `source/`
2. **«Сканировать»** в UI — регистрируется в `data/media.json`
3. **«Нарезать всё»** — генерируются WebP-варианты
4. Заполняешь `alt` и `caption` в интерфейсе
5. При замене файла — чекбокс **«Перегенерировать»**
6. **«Найти мусор»** — удаляет orphan WebP-файлы без записи в реестре

### Использование в PHP

```php
require_once __DIR__ . '/partials/image.php';
render_image('logo2');
// генерирует <picture> с srcset, width, height из реестра
```

> Статичные product-фото можно подключать напрямую через `<picture>` без регистрации в реестре.

---

## Page-specific CSS

Layouts поддерживают `$extra_css` для подключения изолированных стилей страницы:

```php
// pages/catalog/index.php  (и pages/montage/index.php)
$extra_css = '<link rel="stylesheet" href="/assets/css/catalog.css">';
```

`catalog.css` содержит компоненты `.catalog-faq`, `.catalog-table`, `.catalog-features`, `.catalog-cta` — переиспользуется страницами каталога и монтажа. Создавай отдельный CSS-файл для страниц с нетривиальным UI (калькулятор и т.д.).

---

## Шрифты

Используются локальные семейства `Open Sans Local` и `Roboto Slab Local` из `assets/fonts/`.

- Подключение идёт через раздельные `@font-face` для `latin` и `cyrillic` сабсетов с `unicode-range`
- Такой режим обязателен: в cyrillic-файле могут отсутствовать цифры, знаки и латиница, даже если кириллица есть
- Для проверки покрытия glyphs и unicode ranges перед интеграцией используется [wakamaifondue.com](https://wakamaifondue.com)
- Fallback для заголовков — `serif`, без `Georgia`

---

## Деплой

Локальный FTP-деплой через `tools/deploy.js`.

```bash
npm run deploy
```

---

## Страницы

| URL                 | Файл                                                             |
| ------------------- | ---------------------------------------------------------------- |
| `/`                 | `pages/index/`                                                   |
| `/catalog/`         | `pages/catalog/`                                                 |
| `/prices/`          | `pages/prices/`                                                  |
| `/calc/`            | `pages/calc/`                                                    |
| `/montage/`         | `pages/montage/`                                                 |
| `/articles/`        | `pages/articles/`                                                |
| `/contacts/`        | `pages/contacts/`                                                |
| `/map/`             | `pages/map/`                                                     |
| `/document/`        | `pages/document/`                                                |
| `/preview-d/`       | `layouts/preview-d.php` (экспериментальная главная, в доработке) |
| `/articles/{slug}/` | `pages/articles/{slug}/`                                         |
| `/objects/{id}/`    | `pages/objects/{id}/index.php` → `pages/objects/_template.php`   |

---

## Карта выполненных работ

- Яндекс.Карты JS API v3 + `@yandex/ymaps3-clusterer` (jsdelivr CDN)
- 529 объектов из `data/map.json`
- Маркеры кластеризуются (`clusterByGrid({ gridSize: 64 })`)
- Клик на маркер с `url` → страница объекта (через ymaps3 `onClick` prop + `mapEvent.stopPropagation()`)
- Интерактивная легенда-фильтр под картой: toggle категорий, `solo-click` при состоянии «все включены», reset, счётчик
- **Object page map:** на странице каждого объекта — карта с центрированием на объекте (zoom 13), текущий объект выделен оранжевым маркером с пульсацией, остальные объекты — те же маркеры с навигацией; **легенда-фильтр идентична главной карте** (solo-click, toggle, reset, счётчик, текущий объект из фильтра исключён)

### Координаты

> `data/map.json` хранит `coords` в формате `[latitude, longitude]` — так же как в интерфейсе Яндекс.Карт.  
> ymaps3 JS API принимает их в том же порядке — **перевод не нужен**, `obj.coords` подставляется напрямую.  
> Это относится и к общей карте `/map/`, и к карте на странице объекта (`_template.php`).  
> ⚠️ Не делать swap `[coords[1], coords[0]]` — проверено эмпирически, объекты встают точно.

### Фильтрация

> Фильтрация реализована в легенде под картой через `clusterer.update({ features })`.  
> Клик при состоянии «все включены» → оставляет только выбранную категорию (`solo-click`).  
> Повторный клик по единственной активной категории → показать все.  
> В остальных случаях — обычный toggle.  
> Кнопка `Показать все` и счётчик `показано N из M` отображаются только при активном фильтре.  
> **Работает как на `/map/`, так и на странице объекта.** На странице объекта текущий объект в фильтрацию не включён и всегда виден.

### Поиск

> Текстовый поиск реализован над легендой-фильтром.  
> Поиск ведётся по полям `title` + `techDescription` через `String.includes()` (регистронезависимо).  
> `debounce(300ms)` — кластеризатор не дёргается на каждый keydown.  
> Под инпутом появляется dropdown с результатами (max 15 строк): цветная точка категории, название, техописание.  
> Объекты с `url` — кликабельные ссылки; без `url` — некликабельные строки.  
> При результатах > 15 — footer «Ещё N объектов — уточните запрос».  
> Поиск и фильтр по категориям работают совместно — `applyFilters()` является единой точкой.  
> Смена категории при активном поиске — дропдаун перефильтровывается.  
> Закрытие дропдауна: `Escape`, клик вне блока, очистка инпута кнопкой `×`.

### Спутниковый режим

> ⛔ В ymaps3 (JS API v3) официального спутникового слоя нет — не поддерживается публичным API.  
> Вопрос переключателя типов карты — на обсуждении с заказчиком.  
> Варианты и детали — в `CONTEXT.md` (раздел «Спутниковый слой и переключатель типов карты»).

---

## Object Pages

Страницы объектов `/objects/{id}/` — SEO-страницы с галереей, навигацией prev/next и мини-картой с легендой.

### Архитектура

- Каждый `index.php` — двустрочник: задаёт `$object_id` + `$object_dir = __DIR__` и подключает `pages/objects/_template.php`
- `_template.php` читает данные из `data/map.json` по `$object_id`, рендерит контент + карту
- `$object_dir` передаётся явно — `__DIR__` внутри `require`-файла указывает на директорию шаблона, а не вызывающего файла
- Карта объекта: текущий объект — отдельный маркер с `zIndex: 100` и CSS-анимацией пульсации (`#f97316`), все остальные — через кластеризатор; координаты используются напрямую без swap
- Легенда-фильтр под картой объекта идентична легенде на `/map/` по разметке, CSS и логике

### Добавление нового объекта

1. Создать `pages/objects/{id}/index.php`:

```php
<?php
$object_id  = {id};
$object_dir = __DIR__;
require __DIR__ . '/../_template.php';
```

1. Убедиться что объект есть в `data/map.json` с полем `"url": "/objects/{id}/"`
2. Добавить изображения в `pages/map/img/{id}_1.webp`, `{id}_2.webp`, ...

---

## SEO

- `sitemap.xml` — ручное обновление до `build.php`; содержит все статьи и все 529 объектов
- `robots.txt` — Yandex/Googlebot, Crawl-delay
- WebP + `srcset` — Core Web Vitals / CLS = 0
- `**partials/head-seo.php**` — OG, Twitter Cards, JSON-LD Schema.org `@graph`, geo-теги Яндекса
- Object pages: Schema.org `CreativeWork` + `GeoCoordinates` уже в шаблоне
- Article pages: `og_type=article` + Schema.org `Article` + `article:published_time`/`article:modified_time` (`2026-01-01`) — полностью реализованы во всех 31 статьях

---

## Известный технический долг

| Проблема                                | Причина                                 | Решение                                                           |
| --------------------------------------- | --------------------------------------- | ----------------------------------------------------------------- |
| `template.css` — монолит                | Осознанно до `build.php`                | Декомпозиция при миграции на WebForge                             |
| `sitemap.xml` вручную                   | До генератора                           | Автогенерация в `build.php`                                       |
| `source/` в git                         | Пока объём мал                          | Git LFS при росте                                                 |
| Нет hash-инвалидации CSS/JS             | До `build.php`                          | `style.{hash8}.css` при сборке                                    |
| Изображения объектов в `pages/map/img/` | Исторически                             | При масштабировании — вынести в `assets/img/objects/`             |
| `data/objects.json` в разработке        | Контракт данных ещё не зафиксирован     | Выделить SSOT расширенных SEO-полей перед programmatic генерацией |
| Нет переключателя типов карты           | ymaps3 не поддерживает спутник          | Обсуждение с заказчиком                                           |
| Диаметры в прайсе и каталоге расходятся | Данные не синхронизированы с заказчиком | Ждём ответа заказчика, обновить каталог или прайс                 |

---

## Роадмап

- [x] Favicon + `site.webmanifest`
- [x] Карта выполненных работ (Яндекс.Карты v3 + кластеризация + легенда)
- [x] Фильтрация по категориям на карте (общая карта + страница объекта)
- [x] OG-изображения
- [x] SEO-partial (`partials/head-seo.php`)
- [x] Данные 500+ объектов карты (Qwen3.5 Flash)
- [x] Корневые npm-скрипты
- [x] **Object pages** — шаблон + 529 страниц programmatically, карта + легенда-фильтр на странице объекта
- [x] Локальные шрифты — единый нейминг + раздельные latin/cyrillic сабсеты через `unicode-range`
- [x] **SEO статей** — `og_type=article` + Schema.org `Article` + даты публикации `2026-01-01` — все 31 статья
- [x] **Каталог свай ВСГ** — карточки, таблицы сравнения/теххарактеристик, FAQ, CTA, page-specific CSS
- [x] **Страница цен** — прайс-лист по диаметрам/типам, аудит выполнен, баг DOCTYPE исправлен
- [x] **Поиск на карте** — текстовый поиск по `title`+`techDescription`, debounced dropdown, совместная работа с фильтром категорий
- [x] **Страница монтажа** — этапы, техника, таблица стоимости, FAQ, CTA; CSS переиспользует `catalog.css`
- [ ] **Главная страница** — вариант `/preview-d/` согласован с заказчиком, в доработке
- [ ] **Image pipeline для объектов** — нарезка, автоматизация, generative-модели
- [ ] `build.php` → pure static `/dist/`
- [ ] Портирование медиапайплайна в WebForge
- [ ] Синхронизация номенклатуры диаметров: прайс ↔ каталог (ждём подтверждения заказчика)

---

## AI в проекте

| Инструмент                      | Применение                                                                               |
| ------------------------------- | ---------------------------------------------------------------------------------------- |
| Perplexity (Space: Zavodsvay)   | Основной AI-ассистент разработки, работа с репо через GitHub MCP                         |
| Qwen3.5 Flash (облачный)        | Подготовка данных карты: восстановление полей, нормализация, категоризация 500+ объектов |
| Generative-модели (планируется) | Подготовка и дополнение изображений объектов                                             |

---

## Стек

| Слой          | Технология                                                                                   |
| ------------- | -------------------------------------------------------------------------------------------- |
| Сайт          | PHP 8.x, нативный CSS, vanilla JS                                                            |
| Медиапайплайн | Node.js, Sharp, Express                                                                      |
| Деплой        | FTP через `tools/deploy.js` (shared hosting)                                                 |
| Карта         | Яндекс.Карты JS API v3 + `@yandex/ymaps3-clusterer` (jsdelivr CDN)                           |
| Шрифты        | Open Sans Local, Roboto Slab Local (локальные woff2, latin + cyrillic через `unicode-range`) |

---

## Лицензия

Copyright 2024–2026 Alexander Kuzikov  
Licensed under the [Apache License, Version 2.0](./LICENSE).

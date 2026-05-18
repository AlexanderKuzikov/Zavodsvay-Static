# Zavodsvay Static

Сайт завода винтовых свай «Гефест» (г. Пермь). PHP file-router, нативный CSS, WebP-изображения, Яндекс.Карты v3, programmatic SEO.

**Живой сайт:** https://zavodsvay.ru/

---

## Стек

| Уровень | Технология |
|---|---|
| Backend | PHP 8+, file-router, layouts, partials |
| Frontend | Нативный CSS (один файл + page-specific), vanilla JS |
| Карта | Яндекс.Карты JS API v3 + ymaps3-clusterer |
| Медиапайплайн | Node.js, sharp, WebP srcset |
| Деплой | FTP (tools/deploy.js) |
| Документация | CONTEXT.md (живой документ сессий) |

---

## Структура проекта

```
/
├── assets/
│   ├── css/
│   │   ├── template.css        # глобальные стили
│   │   └── catalog.css         # стили каталога (page-specific)
│   ├── img/                    # WebP-изображения
│   ├── js/
│   └── fonts/
├── data/
│   ├── map.json                # 500+ объектов (SSOT)
│   └── media.json              # реестр медиа
├── layouts/
│   ├── main.php                # sidebar + content (поддерживает $extra_css)
│   ├── home.php                # главная
│   └── wide.php                # без sidebar
├── pages/
│   ├── catalog/                # /catalog/ — каталог свай ВСГ ✅
│   ├── articles/{slug}/        # 31 статья
│   ├── objects/{id}/           # 529 страниц объектов
│   ├── map/                    # /map/ — карта выполненных работ
│   └── ...                     # prices, calc, montage, contacts, 404
├── partials/
│   ├── head-seo.php
│   ├── head-favicon.php
│   ├── header.php
│   ├── sidebar.php
│   ├── footer.php
│   └── image.php
├── tools/                      # медиапайплайн + деплой
├── source/                     # оригиналы изображений
├── CONTEXT.md                  # ← живой документ для AI
├── index.php                   # главная страница
├── sitemap.xml
├── robots.txt
└── .htaccess
```

---

## Статус реализации

| Страница / фича | Статус |
|---|---|
| Роутер, layouts, partials | ✅ |
| Главная страница (`/`) | 🔄 в работе |
| Каталог (`/catalog/`) | ✅ |
| Цены (`/prices/`) | ✅ |
| Калькулятор (`/calc/`) | ✅ |
| Монтаж (`/montage/`) | ✅ |
| Статьи (31 шт.) | ✅ |
| Контакты (`/contacts/`) | ✅ |
| Карта работ (`/map/`) | ✅ |
| Объекты (529 шт.) | ✅ |
| SEO: Schema.org, OG, geo | ✅ |
| SEO статей (Article schema) | ✅ |
| Медиапайплайн | ✅ |
| Деплой (FTP) | ✅ |
| `build.php` (static gen) | ⬜ |

---

## Быстрый старт

```bash
git clone https://github.com/AlexanderKuzikov/Zavodsvay-Static.git
cd Zavodsvay-Static
cd tools && npm install
cd ..
npm run ui      # медиапайплайн UI
npm run deploy  # деплой на FTP
```

Для локального запуска нужен PHP 8+ (например, `php -S localhost:8000`).

---

## Page-specific CSS

Layouts поддерживают `$extra_css` для подключения изолированных стилей страницы:

```php
// pages/catalog/index.php
$extra_css = '<link rel="stylesheet" href="/assets/css/catalog.css">';
```

Создавай отдельный CSS-файл для страниц с нетривиальным UI (каталог, калькулятор и т.д.).

---

## Документация

Подробный контекст разработки, архитектурные решения и договорённости — в **[CONTEXT.md](./CONTEXT.md)**.

CONTEXT.md обновляется после каждого этапа и является основным источником правды для AI-ассистента.

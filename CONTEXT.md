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

## Текущее состояние проекта

**Дата последнего обновления:** 2026-04-30

### Что реализовано
- Файловый PHP-роутер, layouts (main, home, wide), partials
- Все основные страницы: index, catalog, prices, calc, montage, articles, contacts, map, document, 404
- Адаптивная верстка на нативном CSS (один файл `assets/css/template.css`)
- Гамбургер-меню, коллапсируемый sidebar на мобиле
- Hero-видео на главной (`video/gefest01.mp4`)
- WebP-изображения с srcset (`assets/img/start/` — 6 размеров)
- Система классов для изображений в тексте (`content-image-wrapper` + BEM-модификаторы)
- Вставка `SvaiGrunt` на главную страницу с адаптивным srcset

### В работе / ближайшие задачи
- [ ] Наполнение каталога продукции с изображениями
- [ ] GitHub Actions → автодеплой по FTP
- [ ] `build.php` — статическая генерация `/dist/`

---

## CSS-архитектура

### Файл: `assets/css/template.css`

Единственный CSS-файл проекта. Структура секций:
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
11. **Изображения в тексте** (добавлено 2026-04-30)
12. **Media Queries** (добавлено/расширено 2026-04-30)

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
.content-clearfix               — сброс float после блока с обтеканием
```

На `≤768px` все float-модификаторы → `width: 100%`, `float: none`.

---

## Изображения

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
Новые наборы изображений: `{Name}x{width}.webp` (пример: `SvaiGruntx640.webp`).
Максимальный размер без суффикса: `{Name}{width}.webp` (пример: `SvaiGrunt2000.webp`) — исторически сложилось, новые лучше делать единообразно.

---

## Шаблоны и партиалы

### Layouts
- `layouts/main.php` — стандартный (sidebar + content)
- `layouts/home.php` — главная (splash + content)
- `layouts/wide.php` — без sidebar

### Структура страницы
Каждая страница в `pages/{slug}/index.php` определяет переменные и подключает layout:
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

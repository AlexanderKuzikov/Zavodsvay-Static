# Завод винтовых свай «Гефест» — сайт

[![License](https://img.shields.io/badge/license-Apache%202.0-blue.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Nginx](https://img.shields.io/badge/Nginx-PHP--FPM-009639?logo=nginx&logoColor=white)](https://nginx.org/)
[![Site](https://img.shields.io/badge/site-zavodsvay.ru-brightgreen)](https://zavodsvay.ru)

**Продакшн:** [zavodsvay.ru](https://zavodsvay.ru)

Корпоративный сайт производителя винтовых свай. Реализован как zero-dependency PHP micro-framework с файловым роутингом — без фреймворков, без сборщиков, без npm. Целевое состояние — полностью статический HTML после build-фазы.

---

## Архитектура

```
index.php           # Точка входа, файловый роутер
├── pages/          # Страницы (файловый роутинг /slug/ → /pages/slug/index.php)
│   ├── index/      # Главная
│   ├── catalog/    # Каталог свай
│   ├── prices/     # Цены
│   ├── calc/       # Калькулятор
│   ├── montage/    # Монтаж
│   ├── articles/   # Статьи
│   ├── contacts/   # Контакты
│   ├── map/        # Карта дилеров
│   ├── document/   # Документация
│   └── 404/        # Страница ошибки
├── layouts/        # Шаблоны страниц (main, home, wide)
├── partials/       # Переиспользуемые блоки (header, footer, sidebar, splash, components)
├── assets/         # CSS, JS, изображения
├── data/           # JSON-данные (закрыты от прямого доступа)
├── video/          # Видеоматериалы
└── .htaccess       # Rewrite rules, gzip, кэширование статики
```

## Принцип работы

Каждый запрос попадает в `index.php`, который резолвит `REQUEST_URI` в путь `pages/{slug}/index.php`. Каждая страница определяет `$title`, `$meta_description`, `$canonical`, рендерит контент через `ob_start()` и подключает нужный layout. Layouts подключают partials — header, footer и опциональные блоки.

```php
// pages/catalog/index.php
$title = "Каталог винтовых свай";
ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';
```

## Стек

| Слой | Технология |
|---|---|
| Сервер | Nginx + PHP-FPM 8.3 |
| Роутинг | Самописный PHP file-router |
| Шаблонизация | PHP include / ob_start |
| Стили | Нативный CSS |
| Скрипты | Vanilla JS |
| Данные | JSON-файлы в `/data/` |
| Хостинг | ISPmanager, webhost1 |

## Локальная разработка

Требования: [Laragon](https://laragon.org/) (PHP 8.x, Nginx/Apache).

```bash
# Клонировать репо в папку www
git clone https://github.com/AlexanderKuzikov/Zavodsvay-Static D:\laragon\www\Zavodsvay-Static
```

```powershell
# Или создать символическую ссылку если репо уже клонирован
New-Item -ItemType SymbolicLink -Path "D:\laragon\www\Zavodsvay-Static" -Target "D:\GitHub\Zavodsvay-Static"
```

Сайт доступен по адресу `http://zavodsvay-static.test`

## Деплой

Workflow: локальная правка → проверка на Laragon → коммит в `main` → деплой на сервер.

```
local (Laragon) → GitHub (main) → FTP → zavodsvay.ru
```

## Roadmap

- [ ] GitHub Actions → автодеплой по FTP при пуше в `main`
- [ ] Вынос динамики (форма, калькулятор) в отдельный API endpoint
- [ ] `build.php` — генерация чистого статического HTML в `/dist/`
- [ ] Переход на pure static: Nginx без PHP

## Автор

**Alexander Kuzikov** — [github.com/AlexanderKuzikov](https://github.com/AlexanderKuzikov)

## Лицензия

Распространяется под лицензией [Apache License 2.0](LICENSE).

---

© 2024 — Завод винтовых свай «Гефест»

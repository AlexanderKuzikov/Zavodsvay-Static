# Zavodsvay-Static — DECISIONS

<!-- Append-only. Формат фиксирован. -->

## 2026-05-22: PHP file-router вместо SSG

**Контекст:** Нужен production-сайт без серверной логики на хостинге.

**Решение:** PHP для разработки (include-based), статика для деплоя.

**Альтернативы:** Next.js, Hugo, чистый HTML.

**Trade-off:** Нет hot-reload, но zero-dependency на продакшене.

## 2026-05-22: Yandex Maps v3 + JSON данные

**Контекст:** 500+ объектов на карте выполненных работ.

**Решение:** Yandex Maps JS API v3, данные в JSON (data/objects/).

**Альтернативы:** MapLibre + PMTiles (в WebForge), Google Maps.

**Trade-off:** Зависимость от Yandex API, но бесплатно и быстро для РФ.

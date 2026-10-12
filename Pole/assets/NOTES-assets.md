# assets — картинки и что не скачалось

## Спасено

- `logo-brand.svg` — фирменный знак «СваяПроект», дословно из `LOGO_SVG` в коде (5 полигонов, цвета `#005B3F/#017E54/#000000/#ffffff/#E2E2E2`, viewBox `150 205 700 620`). В проекте хранится как data-URI `BRAND_LOGO` и подставляется в штамп чертежа; есть кнопки «Загрузить логотип (PNG, SVG)» / «Знак СваяПроект».
- `../snapshot-2026-10-12.yml` — визуальная структура листа (все текстовые элементы чертежа с координатами-боксами отсутствуют в yml, но есть тексты: заголовки, размеры, 21 номер сваи, таблицы).

## Не скачалось (сайт лёг) — прямые URL, докачать при доступе

- https://sssfr.ru/favicon.svg (иконка, грузилась ×2)
- https://sssfr.ru/og.png (OG 1200×630)
- https://sssfr.ru/icon-180.png, https://sssfr.ru/site.webmanifest
- Шрифты Google (нам не нужны — у нас локальные `Open Sans Local` / `Roboto Slab Local`): `Golos Text`, `PT Sans Narrow`, `IBM Plex Mono` — https://fonts.googleapis.com/css2?family=Golos+Text:wght@400;500;600;700&family=PT+Sans+Narrow:wght@400;700&family=IBM+Plex+Mono:wght@400;500&display=swap
- Счётчики: `https://mc.yandex.ru/metrika/tag.js?id=113043827`, `tag_phono.js`, `watch/113043827` (нам не нужны).

## Отдельно

Сам план чертежа как картинки не существует — он генерируется `DRAW.svgDocument(st, piles, spec, {width})` на лету (PNG 2600 / SVG 1600 / печать 100%). Скриншот снять не удалось (браузер закрыт, сайт недоступен); при повторном доступе: `PNG/SVG/Печать` кнопками + скрин всей страницы.

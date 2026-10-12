# 00-meta — шапка, SEO, окружение страницы

Источник: `document.head.outerHTML` (первые 4000 символов) + сетевые запросы + `document.scripts/styles`.

## Документ

- URL: https://sssfr.ru/konstruktor/ (canonical на себя)
- Title: `СваяПроект — конструктор свайных полей`
- Description: `Интерактивный конструктор свайных полей: схема с осями и сваями, лист чертежа со штампом, спецификация, расчёт стоимости и материалов фундамента на винтовых и ЖБ сваях.`
- OG: `og:site_name=СваяПроект`, `og:locale=ru_RU`, `og:title` = title, `og:description` = `Схема свайного поля, чертёж со штампом, спецификация и стоимость фундамента — за пару минут прямо в браузере.`, `og:image=https://sssfr.ru/og.png` (1200×630), twitter `summary_large_image`.
- `theme-color #15724c`, `format-detection telephone=no`.
- Иконки: `/favicon.svg` (svg), `/icon-180.png`, `/site.webmanifest`.
- Шрифты (единственный внешний CSS): `Golos Text 400/500/600/700` (UI) + `PT Sans Narrow 400/700` (чертёж) + `IBM Plex Mono 400/500` (цифры). Preconnect к `fonts.googleapis.com` / `fonts.gstatic.com`.
- Стили: два `<style>` — ~22 995 (интерфейс) и ~3 389 (печать) байт. CSS-переменные `--bg/--panel/--ink/--accent #15724c/--mod #d9480f`, тёмная тема через `prefers-color-scheme` + `data-theme`.
- Скрипты: `metrika/tag_phono.js` + `metrika/tag.js?id=113043827`, далее inline ~153 449 и ~621 байт. Других JS-файлов НЕТ.
- Сеть после загрузки: шрифты, `site.webmanifest`, `favicon.svg` ×2, Метрика (`tag.js`, `advert.gif`, `watch/113043827`, `tag_phono.js`, `match.html`, `hdrc/mdd.yandex.net`). Своей API нет.
- Верхняя навигация: `← СССФР · сообщество строителей свайных фундаментов` → `/`, разделы `/svai/`, `/regiony/`, `/zavody/`.

## Шапка конструктора

- Бренд: `СваяПроект — конструктор свайных полей`.
- Поля объекта: `Объект = Жилой дом 8,0 × 6,0 м` (placeholder `Жилой дом 8,0 × 6,0 м`), `Адрес = Московская обл., г. Пушкино, СНТ «Рассвет», уч. 14`.
- Кнопки: `↶ ↷` (undo/redo, история 80 шагов), `Новый / Открыть / Сохранить (.json) / PNG / SVG`, `⋯`, `Печать / PDF`, `Предложить` (форма → WhatsApp +7 909 000-07-10, текст копируется).

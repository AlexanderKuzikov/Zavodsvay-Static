# formulas/model-axes — модель осей и свай (MODEL)

Источник: выдержки `MODEL`: `ax/positions/labels/blank/build/resolve/setOverride/preset/snapshot/undo/tieLength/inset/outer/setOuter`.

## Оси

- Ось: `{id, gap, main}` (`gap` — пролёт ДО оси от предыдущей, мм; первая — база 0). `positions()` — накопленные координаты; `span()` — габарит.
- Метки: основные — `1,2,3 / А,Б,В…`; промежуточные — `1/1, 1/2…` (`labels()`).
- `main=true` = «стена» (сваи по всей длине); `main=false` = «ряд» (сваи только на пересечениях с основными). Середина `ряд×ряд` — сваи не ставятся.
- `resplit(st)`: перерасчёт рядов-авто по `maxStep` (кнопка «Разбить по шагу»); авто-оси помечены `auto:true`.
- `dimMode`: `axes` (размеры между осями) / `outer` (от наружной грани; `inset` — заход оси внутрь, дефолт 75: «75 мм — ось под серединой бруса 150, 100 — под серединой швеллера 200, 0 — по грани»). `outer() = span + 2*inset`; `setOuter()` масштабирует пролёты пропорционально (мин. 300 мм, добор округлений в последнюю ось).
- Drag: оси таскаются мышью на чертеже (`snap` 50 мм из `opts`), `Alt` — сохранить общий габарит.

## Сваи

- Ключ узловой: `N:{vid}:{hid}`; внесетевые: `X:{id}` (`extra[]: {id,x,y,props}`).
- `resolve(key,x,y)`: `kind/size/len/cap/capOn` = override → иначе defaults (с backlash: смена kind сбрасывает size/len/cap на типовые: ЖБ 200/4000/standart, винт 108/2500/200×200); `prod = pile(kind,size,len)`; `moved/dx/dy`, `modified` (любой тронутый ключ кроме note), `deleted`.
- `build()`: все пересечения (кроме ряд×ряд) + extra; сортировка `(y desc, x asc)`; нумерация `no = i+1`; ось `axis = Буква/Цифра`.
- Правки: `setOverride(key,patch)` (null стирает поле), `resetPile`, `addPile(x,y,props)`, `removePile` (узловые — флагом `deleted:true`, внесетевые — удалением). Подсветка изменённых — оранжевым (`--mod #d9480f`).
- `tieLength()`: `bx.length*h + by.length*w` по основным осям. `area() = outerV*outerH/1e6`.

## История и пресеты

- История: `snapshot()` (JSON строки, стек 80) / `undo()` / `redo()` / `canUndo/canRedo`. Снапшот перед каждым мутating-действием.
- `blank()`: дефолтный проект (см. `data/example-project.json` — это он с подменой id осей). `preset(name, keepMeta)`: собирает `axV/axH` из `PRESETS` + `resplit`; `migrate()` — апгрейд старых сохранений.

## Модули-ключи (для повторного съёма)

- `MODEL`: LETTERS, BRAND_LOGO, uid, clone, ax, labels, positions, span, blank, migrate, resplit, maxGap, build, resolve, ovOf, setOverride, resetPile, addPile, removePile, tieLength, area, inset, outer, setOuter, preset, PRESETS, snapshot, undo, redo, canUndo, canRedo.
- `DRAW`: render, svgDocument, SHEETS, SCALES, esc, fmt, wrap, ruDate. Лист в мм листа, план — вложенной группой 1:N, Y вверх; рамка ГОСТ; штамп 185×55 мм.
- `SPEC`: build, money, toCSV, toOffer, ruDate. CSV: `Артикул;Наименование;Ед.;Кол-во;Цена, ₽;Сумма, ₽` + строка `ИТОГО`.
- `PRINT`: build. Печать: лист чертежа 1:1 + лист спецификации (таблица №/Наименование/Ед/Кол/Цена/Сумма, итоги, скидка, НДС, метрики шт/м²/₽/свая/₽/м²).
- Экспорты: `save` (.json всего `st`), `png` (SVG→Image→canvas 2600px, белый фон), `svg` (1600px), `print` (PRINT.build + `window.print()` через 150 мс), `fit/zoomIn/zoomOut` (×1.25).

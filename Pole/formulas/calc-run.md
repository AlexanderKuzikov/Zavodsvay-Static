# formulas/calc-run — сводный расчёт нагрузок (CALC.run)

Источник: дословная выдержка `function run(st, piles)` из inline-JS (2026-10-12).

## Вход

`st.calc` (дефолты): `{house:'каркасный', floors:1, wallH:3000, roof:'металлочерепица', floorType:'деревянное', snow:180, soil:'суглинок', freeze:1400}`.
Геометрия: `X = positions(axV)`, `Y = positions(axH)` (мм → м: `W`, `H`, `A = W*H`, `perim = 2*(W+H)`).
Внутренние несущие: суммарная длина `innerLen` по осям `bearing` (не крайним).

## Формулы (дословно)

```
wallKg = WALL[house].w            # кг/м² стены, см. data/loads.json
hM = wallH/1000; nFl = max(1, floors)
wWalls  = perim*hM*nFl*0.85*wallKg + innerLen*hM*nFl*0.5*wallKg
selfFl  = FLOORS[floorType]       # 150/500/0
wFloors = A*(selfFl+150)*nFl + A*(selfFl*0.6+100)
wRoof   = A*1.15*ROOF[roof]
wSnow   = A*snow
sumKg   = wWalls+wFloors+wRoof+wSnow
design  = sumKg*1.2               # коэфф. надёжности 1.2
perPile = piles.length ? design/piles.length : design
```

## Подбор типоразмера

- Без геологии (`geo.off` или ЖБ): `capT = loadPerPile(kind, size, soil)`; `need = ceil(design/capT)`.
  `loadPerPile = base * k_grunt`, где `base` = `SCREW[size].load` (или `ZHB_LOAD[size]`), `k` из `GRUNT` (суглинок 1.0, песок 1.15, торф 0.6…).
- С геологией (только винтовые): `pickDepth(st, size, perPile)` — опускает лопасть шагами, пока `Fd/γk` не перекроет нагрузку (лимит — ствол 6 м); `cap = round(NkG)`.
- `have = piles.length`; `current` — опция текущего `defaults.size`; `fit` — первая опция с `need <= have`.
- `recLen`: с гео `min(6000, max(2000, ceil((depth+plinth+200)/500)*500))`, без гео `min(6000, max(2000, ceil((freeze+800+plinth)/500)*500))`.
- `recStep`: ЖБ 3000; винт ≥133 → 3000; ≥108 → 2500; иначе 2000. `reserve = cap*have/design`; `ok = have >= need`.

## Возврат

`{W,H,area,perim, parts:[Стены/Перекрытия/Кровля/Снег], sumKg, design, perPile:cap, need, have, reserve, ok, options, fit, byGeo, recLen, recStep, soil}`.

## Что сверить у себя

Коэффициенты 0.85/0.5/1.15/1.2 и `+150/+100` — эмпирика СССФР, не норматив. Наши формулы в `pages/calc/content.html:397-406` — другие; при объединении выбрать одну методику и зафиксировать тестом.

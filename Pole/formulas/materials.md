# formulas/materials — масса, полости, заполнение (CALC.materials)

Дословно из `function materials(st, piles)`.

## Вход

`fin`: `{fill:'ПГС', fillRatio:100, bagKg:0, tieOn, tieType, ...}`; сваи `piles[]` (`kind/size/len/capOn`).

## Алгоритм

```
ratio = clamp(fillRatio,0,100)/100; loss = 1.1   # уплотнение и потери
для каждой сваи p:
    pm = pileMass(kind,size,len); pileMass += pm
    если capOn: capMass += capMass(kind,size,cap); caps++
    если zhb: zhb++, zhbMass += pm
    иначе: screw++, screwMass += pm
        v = cavityVolume('screw', size, len, ratio)   # по ВНУТРЕННЕМУ диаметру (Ø − 2×стенка)
        cavity += v; группировка byPile['Ø{size}×{len}'] += {n, v, m}
tieLen  = tieOn ? tieLength(st)/1000 : 0              # м, по основным осям: bx*h + by*w
tieMass = tieLen * TIE_MASS[tieType]
brace   = braces(st, piles); braceMass = brace.mass   # укосины по пролётам контура, где цоколь выше допустимого
fillVolume = cavity * loss
fillMass   = fillVolume * FILL[name].density
bagKg = fin.bagKg > 0 ? fin.bagKg : FILL[name].bag
bags  = bagKg ? ceil(fillMass/bagKg) : 0              # бетон миксером: bag=0 → bags=0
perPileLitres = screw ? cavity/screw*1000 : 0
total = pileMass + capMass + tieMass + braceMass + fillMass
```

## Контрольные цифры демо

`cavity`: 20 л/сваю → 0.421 м³ → ×1.1 = 0.463 м³; ПГС 1700 кг/м³ → 787 кг → 20 мешков × 40 кг.
Сталь: сваи 567 + оголовки 71 = 638 кг; всего с заполнением 1 425 кг → `Газель, 1 рейс`.

## mass-функции (CATALOG, смысл)

`pileMass(kind,size,len)`, `capMass(kind,size,cap)`, `cavityVolume(...)` — тела не снимали (вне фрагментов); при повторном доступе снять и сверить с нашим прайсом (у нас есть вес в `pages/prices/`).

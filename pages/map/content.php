<?php
// $published и $map_data доступны из index.php

$CAT_LABELS = [
    'house'      => 'Жилой дом',
    'banya'      => 'Баня',
    'fence'      => 'Забор',
    'commercial' => 'Коммерция',
    'industrial' => 'Промышленные',
    'water'      => 'Водные объекты',
    'social'     => 'Социальные',
    'agro'       => 'Сельхоз',
    'other'      => 'Прочее',
];

$CAT_COLORS = [
    'house'      => '#2563eb',
    'banya'      => '#16a34a',
    'fence'      => '#9333ea',
    'commercial' => '#ea580c',
    'industrial' => '#dc2626',
    'water'      => '#0891b2',
    'social'     => '#ca8a04',
    'agro'       => '#65a30d',
    'other'      => '#6b7280',
];
?>
<h1>Карта выполненных работ</h1>

<style>
    #map-works {
        width: 100%;
        height: 500px;
        background-color: #f0f0f0;
        position: relative;
    }

    .map-loader {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 10;
    }

    .custom-marker {
        border: 2px solid #fff;
        border-radius: 50%;
        width: 16px;
        height: 16px;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
        transition: transform 0.15s;
    }

    .custom-marker:hover {
        transform: scale(1.3);
    }

    .custom-marker--published {
        width: 20px;
        height: 20px;
        border: 3px solid #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
    }

    .cluster-icon {
        background: #1e3a5f;
        color: #fff;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: bold;
        border: 2px solid #fff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.35);
        cursor: pointer;
    }

    /* === Поиск === */
    .map-search {
        position: relative;
        margin-top: 18px;
    }

    .map-search__input-wrap {
        display: flex;
        align-items: center;
        gap: 0;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #fff;
        transition: border-color 0.15s, box-shadow 0.15s;
        overflow: hidden;
    }

    .map-search__input-wrap:focus-within {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .map-search__icon {
        padding: 0 10px 0 12px;
        color: #9ca3af;
        display: flex;
        align-items: center;
        flex-shrink: 0;
        pointer-events: none;
    }

    .map-search__input {
        flex: 1;
        border: none;
        outline: none;
        font-size: 14px;
        padding: 10px 4px;
        background: transparent;
        color: #111;
        min-width: 0;
    }

    .map-search__input::placeholder {
        color: #9ca3af;
    }

    .map-search__clear {
        display: none;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0 12px;
        color: #9ca3af;
        font-size: 18px;
        line-height: 1;
        flex-shrink: 0;
    }

    .map-search__clear:hover {
        color: #374151;
    }

    .map-search__results {
        display: none;
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        z-index: 200;
        max-height: 320px;
        overflow-y: auto;
    }

    .map-search__results--open {
        display: block;
    }

    .map-search__result-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 14px;
        cursor: pointer;
        border-bottom: 1px solid #f3f4f6;
        text-decoration: none;
        color: inherit;
        transition: background 0.1s;
    }

    .map-search__result-item:last-child {
        border-bottom: none;
    }

    .map-search__result-item:hover {
        background: #f0f6ff;
    }

    .map-search__result-item--no-link {
        cursor: default;
    }

    .map-search__result-item--no-link:hover {
        background: transparent;
    }

    .map-search__result-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .map-search__result-title {
        font-size: 13px;
        font-weight: 500;
        color: #111827;
        line-height: 1.35;
        flex: 1;
        min-width: 0;
    }

    .map-search__result-desc {
        font-size: 11px;
        color: #6b7280;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
        flex-shrink: 0;
    }

    .map-search__result-more {
        padding: 8px 14px;
        font-size: 12px;
        color: #6b7280;
        text-align: center;
        border-top: 1px solid #f3f4f6;
    }

    .map-search__no-results {
        padding: 16px 14px;
        font-size: 13px;
        color: #6b7280;
        text-align: center;
    }

    /* === Легенда-фильтр === */
    .map-legend {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 10px 0;
        margin-top: 12px;
        padding: 16px 20px;
        background: #f8f8f8;
        border: 1px solid #e4e4e4;
        border-radius: 10px;
    }

    .map-legend-footer {
        grid-column: 1 / -1;
        display: flex;
        align-items: center;
        gap: 12px;
        padding-top: 10px;
        border-top: 1px solid #e4e4e4;
        margin-top: 4px;
    }

    .map-legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #333;
        line-height: 1;
        cursor: pointer;
        user-select: none;
        padding: 3px 6px 3px 0;
        border-radius: 4px;
        transition: opacity 0.15s;
    }

    .map-legend-item:hover {
        opacity: 0.8;
    }

    .map-legend-item--inactive {
        opacity: 0.35;
    }

    .map-legend-item--inactive .map-legend-dot {
        background: #ccc !important;
        box-shadow: none;
    }

    .map-legend-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.35);
        flex-shrink: 0;
        transition: background 0.15s;
    }

    .map-legend-reset {
        display: none;
        font-size: 12px;
        color: #2563eb;
        background: none;
        border: 1px solid #2563eb;
        border-radius: 4px;
        padding: 3px 10px;
        cursor: pointer;
        line-height: 1.5;
        transition: background 0.15s, color 0.15s;
    }

    .map-legend-reset:hover {
        background: #2563eb;
        color: #fff;
    }

    .map-legend-counter {
        font-size: 12px;
        color: #888;
    }

    /* === Список опубликованных объектов === */
    .objects-section {
        margin-top: 36px;
    }

    .objects-section__header {
        display: flex;
        align-items: baseline;
        gap: 12px;
        margin-bottom: 20px;
        border-bottom: 2px solid #0a2342;
        padding-bottom: 10px;
    }

    .objects-section__title {
        font-size: 1.3em;
        font-weight: 700;
        color: #0a2342;
        margin: 0;
    }

    .objects-section__count {
        font-size: 0.85em;
        color: #888;
    }

    .objects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 16px;
    }

    .object-card {
        display: flex;
        flex-direction: column;
        border: 1px solid #e4e4e4;
        border-radius: 8px;
        overflow: hidden;
        text-decoration: none;
        color: inherit;
        background: #fff;
        box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }

    .object-card:hover {
        box-shadow: 0 4px 16px rgba(10, 35, 66, 0.15);
        transform: translateY(-2px);
    }

    .object-card__thumb {
        width: 100%;
        aspect-ratio: 4 / 3;
        object-fit: cover;
        background: #f0f0f0;
        display: block;
        flex-shrink: 0;
    }

    .object-card__thumb--placeholder {
        aspect-ratio: 4 / 3;
        background: linear-gradient(135deg, #e8eef5 0%, #d0daea 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .object-card__thumb--placeholder svg {
        width: 40px;
        height: 40px;
        opacity: 0.3;
    }

    .object-card__body {
        padding: 12px 14px;
        display: flex;
        flex-direction: column;
        gap: 6px;
        flex-grow: 1;
    }

    .object-card__category {
        font-size: 0.75em;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #fff;
        padding: 2px 8px;
        border-radius: 20px;
        align-self: flex-start;
        line-height: 1.6;
    }

    .object-card__title {
        font-size: 0.9em;
        font-weight: 600;
        color: #0a2342;
        line-height: 1.35;
        margin: 0;
    }

    .object-card__desc {
        font-size: 0.8em;
        color: #666;
        line-height: 1.4;
        margin: 0;
    }

    .object-card__link {
        font-size: 0.8em;
        color: #2563eb;
        margin-top: auto;
        padding-top: 8px;
        font-weight: 400;
    }

    @media (max-width: 768px) {
        .objects-grid {
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 12px;
        }
    }

    @media (max-width: 480px) {
        .objects-grid {
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
    }
</style>

<script src="https://api-maps.yandex.ru/v3/?apikey=c0204985-1515-44c3-8e4e-1a57f540fd94&lang=ru_RU"></script>

<div id="map-works">
    <div class="map-loader">Загрузка карты...</div>
</div>

<div class="map-search" id="map-search">
    <div class="map-search__input-wrap">
        <span class="map-search__icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
        </span>
        <input
            type="search"
            id="map-search-input"
            class="map-search__input"
            placeholder="Поиск по названию объекта, адресу, описанию..."
            autocomplete="off"
            aria-label="Поиск по объектам"
        >
        <button class="map-search__clear" id="map-search-clear" type="button" aria-label="Очистить поиск">&times;</button>
    </div>
    <div class="map-search__results" id="map-search-results" role="listbox"></div>
</div>

<div class="map-legend" id="map-legend" aria-label="Фильтр по категориям">
    <div class="map-legend-item" data-cat="house">
        <span class="map-legend-dot" style="background:#2563eb"></span>
        <span>Жилой дом</span>
    </div>
    <div class="map-legend-item" data-cat="banya">
        <span class="map-legend-dot" style="background:#16a34a"></span>
        <span>Баня</span>
    </div>
    <div class="map-legend-item" data-cat="fence">
        <span class="map-legend-dot" style="background:#9333ea"></span>
        <span>Забор</span>
    </div>
    <div class="map-legend-item" data-cat="commercial">
        <span class="map-legend-dot" style="background:#ea580c"></span>
        <span>Коммерция</span>
    </div>
    <div class="map-legend-item" data-cat="industrial">
        <span class="map-legend-dot" style="background:#dc2626"></span>
        <span>Промышленные</span>
    </div>
    <div class="map-legend-item" data-cat="water">
        <span class="map-legend-dot" style="background:#0891b2"></span>
        <span>Водные объекты</span>
    </div>
    <div class="map-legend-item" data-cat="social">
        <span class="map-legend-dot" style="background:#ca8a04"></span>
        <span>Социальные</span>
    </div>
    <div class="map-legend-item" data-cat="agro">
        <span class="map-legend-dot" style="background:#65a30d"></span>
        <span>Сельхоз</span>
    </div>
    <div class="map-legend-item" data-cat="other">
        <span class="map-legend-dot" style="background:#6b7280"></span>
        <span>Прочее</span>
    </div>
    <div class="map-legend-footer">
        <button class="map-legend-reset" id="map-legend-reset" type="button">Показать все</button>
        <span class="map-legend-counter" id="map-legend-counter"></span>
    </div>
</div>

<section class="objects-section">
    <div class="objects-section__header">
        <h2 class="objects-section__title" id="objects-section-title">Подробные описания объектов</h2>
        <span class="objects-section__count" id="objects-section-count"></span>
    </div>
    <div class="objects-grid" id="objects-grid">
        <?php 
        /** @var array[] $published */
        foreach ($published as $obj):
            $color     = $CAT_COLORS[$obj['category']] ?? $CAT_COLORS['other'];
            $cat_label = $CAT_LABELS[$obj['category']] ?? 'Прочее';
            $thumb     = !empty($obj['images']) ? '/pages/map/img/' . $obj['images'][0] : null;
        ?>
        <a class="object-card" href="<?= htmlspecialchars($obj['url']) ?>" data-id="<?= $obj['id'] ?>">
            <?php if ($thumb): ?>
            <img class="object-card__thumb"
                 src="<?= htmlspecialchars($thumb) ?>"
                 alt="<?= htmlspecialchars($obj['title']) ?>"
                 loading="lazy"
                 width="440" height="330">
            <?php else: ?>
            <div class="object-card__thumb--placeholder">
                <svg viewBox="0 0 24 24" fill="none" stroke="#0a2342" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <path d="M3 9l4-4 4 4 4-4 4 4"/>
                    <path d="M3 15h18"/>
                </svg>
            </div>
            <?php endif; ?>
            <div class="object-card__body">
                <span class="object-card__category" style="background:<?= $color ?>"><?= htmlspecialchars($cat_label) ?></span>
                <p class="object-card__title"><?= htmlspecialchars($obj['title']) ?></p>
                <?php if (!empty($obj['techDescription'])): ?>
                <p class="object-card__desc"><?= htmlspecialchars($obj['techDescription']) ?></p>
                <?php endif; ?>
                <span class="object-card__link">Подробнее &rarr;</span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<script>
    const CAT_COLORS = {
        house: '#2563eb', banya: '#16a34a', fence: '#9333ea',
        commercial: '#ea580c', industrial: '#dc2626', water: '#0891b2',
        social: '#ca8a04', agro: '#65a30d', other: '#6b7280'
    };
    const CAT_LABELS = {
        house: 'Жилой дом', banya: 'Баня', fence: 'Забор',
        commercial: 'Коммерция', industrial: 'Промышленные', water: 'Водные объекты',
        social: 'Социальные', agro: 'Сельхоз', other: 'Прочее'
    };
    const CAT_ORDER = ['house', 'banya', 'fence', 'commercial', 'industrial', 'water', 'social', 'agro', 'other'];

    const MAP_CUSTOMIZATION = [
        {
            tags: { any: ['food', 'restaurant', 'cafe', 'bar', 'shop', 'supermarket', 'convenience'] },
            elements: 'geometry',
            stylers: [{ visibility: 'off' }]
        },
        {
            tags: { any: ['poi'] },
            elements: 'label.icon',
            stylers: [{ visibility: 'off' }]
        }
    ];

    const ALL_CATS = Object.keys(CAT_COLORS);
    const SEARCH_RESULT_LIMIT = 15;

    function getDefaultCards(published) {
        const result = [];
        for (const cat of CAT_ORDER) {
            const candidates = published.filter(o => o.category === cat);
            if (!candidates.length) continue;
            const withPhoto = candidates.find(o => o.images && o.images.length);
            result.push(withPhoto || candidates[0]);
        }
        return result;
    }

    function renderCards(objects) {
        const grid  = document.getElementById('objects-grid');
        const count = document.getElementById('objects-section-count');

        grid.innerHTML = objects.map(obj => {
            const color = CAT_COLORS[obj.category] || CAT_COLORS.other;
            const label = CAT_LABELS[obj.category] || 'Прочее';
            const thumb = obj.images && obj.images.length
                ? `/pages/map/img/${obj.images[0]}`
                : null;
            const thumbHtml = thumb
                ? `<img class="object-card__thumb" src="${thumb}" alt="${escHtml(obj.title)}" loading="lazy" width="440" height="330">`
                : `<div class="object-card__thumb--placeholder"><svg viewBox="0 0 24 24" fill="none" stroke="#0a2342" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9l4-4 4 4 4-4 4 4"/><path d="M3 15h18"/></svg></div>`;
            return `<a class="object-card" href="${escHtml(obj.url)}" data-id="${obj.id}">
                ${thumbHtml}
                <div class="object-card__body">
                    <span class="object-card__category" style="background:${color}">${label}</span>
                    <p class="object-card__title">${escHtml(obj.title)}</p>
                    ${obj.techDescription ? `<p class="object-card__desc">${escHtml(obj.techDescription)}</p>` : ''}
                    <span class="object-card__link">Подробнее &rarr;</span>
                </div>
            </a>`;
        }).join('');

        count.textContent = `${objects.length} объект${pluralize(objects.length)}`;
    }

    function pluralize(n) {
        if (n % 10 === 1 && n % 100 !== 11) return '';
        if (n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 10 || n % 100 >= 20)) return 'а';
        return 'ов';
    }

    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function debounce(fn, ms) {
        let timer;
        return (...args) => { clearTimeout(timer); timer = setTimeout(() => fn(...args), ms); };
    }

    function matchesQuery(obj, query) {
        if (!query) return true;
        const haystack = ((obj.title || '') + ' ' + (obj.techDescription || '')).toLowerCase();
        return haystack.includes(query);
    }

    async function initMap() {
        try {
            await ymaps3.ready;

            const { YMap, YMapDefaultSchemeLayer, YMapDefaultFeaturesLayer, YMapMarker } = ymaps3;

            const map = new YMap(
                document.getElementById('map-works'),
                {
                    location: { center: [56.2, 58.0], zoom: 9 },
                    behaviors: ['drag', 'pinchZoom', 'scrollZoom', 'dblClick']
                }
            );

            map.addChild(new YMapDefaultSchemeLayer({ customization: MAP_CUSTOMIZATION }));
            map.addChild(new YMapDefaultFeaturesLayer());

            ymaps3.import.registerCdn(
                'https://cdn.jsdelivr.net/npm/{package}',
                '@yandex/ymaps3-clusterer@0.0.1'
            );
            const { YMapClusterer, clusterByGrid } = await ymaps3.import('@yandex/ymaps3-clusterer');

            let allObjects = [];
            try {
                const res = await fetch('/data/map.json');
                if (!res.ok) throw new Error('HTTP ' + res.status);
                allObjects = await res.json();
            } catch (e) {
                console.error('[map] fetch error:', e);
            }

            const allPublished = allObjects.filter(o => o.url);

            renderCards(getDefaultCards(allPublished));

            const toFeature = obj => ({
                type: 'Feature',
                id: String(obj.id),
                geometry: { type: 'Point', coordinates: obj.coords },
                properties: { obj }
            });

            const marker = (feature) => {
                const obj = feature.properties.obj;
                const el = document.createElement('div');
                el.className = 'custom-marker' + (obj.url ? ' custom-marker--published' : '');
                el.title = obj.title || '';
                el.style.backgroundColor = CAT_COLORS[obj.category] ?? CAT_COLORS.other;
                return new YMapMarker(
                    {
                        coordinates: feature.geometry.coordinates,
                        zIndex: 10,
                        onClick(event, mapEvent) {
                            mapEvent.stopPropagation();
                            if (obj.url) window.location.href = obj.url;
                        }
                    },
                    el
                );
            };

            const cluster = (coordinates, features) => {
                const el = document.createElement('div');
                el.className = 'cluster-icon';
                el.innerText = features.length;
                return new YMapMarker({ coordinates }, el);
            };

            const clusterer = new YMapClusterer({
                method: clusterByGrid({ gridSize: 64 }),
                features: allObjects.map(toFeature),
                marker,
                cluster
            });
            map.addChild(clusterer);

            // === Состояние фильтров ===
            const activeCategories = new Set(ALL_CATS);
            let searchQuery = '';

            const resetBtn    = document.getElementById('map-legend-reset');
            const counter     = document.getElementById('map-legend-counter');
            const items       = document.querySelectorAll('.map-legend-item[data-cat]');
            const searchInput = document.getElementById('map-search-input');
            const searchClear = document.getElementById('map-search-clear');
            const searchDrop  = document.getElementById('map-search-results');

            // === Единая функция применения фильтров ===
            function applyFilters() {
                const filtered = allObjects.filter(o =>
                    activeCategories.has(o.category) && matchesQuery(o, searchQuery)
                );

                clusterer.update({ features: filtered.map(toFeature) });

                // Счётчик легенды
                const catFiltered = activeCategories.size < ALL_CATS.length;
                items.forEach(item => {
                    item.classList.toggle('map-legend-item--inactive', !activeCategories.has(item.dataset.cat));
                });
                resetBtn.style.display = catFiltered ? 'inline-block' : 'none';
                counter.textContent = (catFiltered || searchQuery)
                    ? `показано ${filtered.length} из ${allObjects.length}`
                    : '';

                // Карточки под картой
                if (!searchQuery) {
                    if (!catFiltered) {
                        renderCards(getDefaultCards(allPublished));
                    } else if (activeCategories.size === 1) {
                        const cat = [...activeCategories][0];
                        renderCards(allPublished.filter(o => o.category === cat));
                    }
                    // мульти-выбор без поиска — не трогаем карточки
                } else {
                    // При поиске — показываем все найденные опубликованные
                    renderCards(filtered.filter(o => o.url));
                }
            }

            // === Дропдаун поиска ===
            function renderDropdown(results, total) {
                if (!results.length) {
                    searchDrop.innerHTML = '<div class="map-search__no-results">Ничего не найдено</div>';
                } else {
                    const items = results.slice(0, SEARCH_RESULT_LIMIT).map(obj => {
                        const color = CAT_COLORS[obj.category] || CAT_COLORS.other;
                        const desc  = obj.techDescription
                            ? `<span class="map-search__result-desc">${escHtml(obj.techDescription)}</span>`
                            : '';
                        if (obj.url) {
                            return `<a class="map-search__result-item" href="${escHtml(obj.url)}" role="option">
                                <span class="map-search__result-dot" style="background:${color}"></span>
                                <span class="map-search__result-title">${escHtml(obj.title)}</span>
                                ${desc}
                            </a>`;
                        }
                        return `<div class="map-search__result-item map-search__result-item--no-link" role="option">
                            <span class="map-search__result-dot" style="background:${color}"></span>
                            <span class="map-search__result-title">${escHtml(obj.title)}</span>
                            ${desc}
                        </div>`;
                    }).join('');

                    const more = total > SEARCH_RESULT_LIMIT
                        ? `<div class="map-search__result-more">Ещё ${total - SEARCH_RESULT_LIMIT} объект${pluralize(total - SEARCH_RESULT_LIMIT)} — уточните запрос</div>`
                        : '';

                    searchDrop.innerHTML = items + more;
                }
                searchDrop.classList.add('map-search__results--open');
            }

            function closeDropdown() {
                searchDrop.classList.remove('map-search__results--open');
                searchDrop.innerHTML = '';
            }

            // === Обработчики поиска ===
            const handleInput = debounce(() => {
                searchQuery = searchInput.value.trim().toLowerCase();
                searchClear.style.display = searchQuery ? 'block' : 'none';

                if (!searchQuery) {
                    closeDropdown();
                    applyFilters();
                    return;
                }

                const results = allObjects.filter(o =>
                    activeCategories.has(o.category) && matchesQuery(o, searchQuery)
                );
                renderDropdown(results, results.length);
                applyFilters();
            }, 300);

            searchInput.addEventListener('input', handleInput);

            searchClear.addEventListener('click', () => {
                searchInput.value = '';
                searchQuery = '';
                searchClear.style.display = 'none';
                closeDropdown();
                applyFilters();
                searchInput.focus();
            });

            searchInput.addEventListener('keydown', e => {
                if (e.key === 'Escape') {
                    closeDropdown();
                    searchInput.blur();
                }
            });

            document.addEventListener('click', e => {
                if (!document.getElementById('map-search').contains(e.target)) {
                    closeDropdown();
                }
            });

            // === Фильтр по категориям ===
            items.forEach(item => {
                item.addEventListener('click', () => {
                    const cat = item.dataset.cat;
                    const allActive = activeCategories.size === ALL_CATS.length;

                    if (allActive) {
                        activeCategories.clear();
                        activeCategories.add(cat);
                    } else if (activeCategories.has(cat) && activeCategories.size === 1) {
                        ALL_CATS.forEach(c => activeCategories.add(c));
                    } else {
                        if (activeCategories.has(cat)) activeCategories.delete(cat);
                        else activeCategories.add(cat);
                    }
                    applyFilters();

                    // Обновить дропдаун с учётом новой категории
                    if (searchQuery) {
                        const results = allObjects.filter(o =>
                            activeCategories.has(o.category) && matchesQuery(o, searchQuery)
                        );
                        if (results.length) renderDropdown(results, results.length);
                        else closeDropdown();
                    }
                });
            });

            resetBtn.addEventListener('click', () => {
                ALL_CATS.forEach(c => activeCategories.add(c));
                applyFilters();
            });

        } catch (e) {
            console.error('[map] error:', e);
        } finally {
            const loader = document.querySelector('.map-loader');
            if (loader) loader.style.display = 'none';
        }
    }

    initMap();
</script>

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

    /* === Переключатель типа карты === */
    .map-type-control {
        display: flex;
        gap: 0;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        border: 1px solid #ddd;
    }

    .map-type-btn {
        padding: 7px 14px;
        font-size: 13px;
        font-weight: 500;
        color: #333;
        background: #fff;
        border: none;
        cursor: pointer;
        transition: background 0.15s, color 0.15s;
        border-right: 1px solid #ddd;
        line-height: 1;
        white-space: nowrap;
    }

    .map-type-btn:last-child {
        border-right: none;
    }

    .map-type-btn:hover {
        background: #f0f4ff;
    }

    .map-type-btn--active {
        background: #0a2342;
        color: #fff;
    }

    .map-type-btn--active:hover {
        background: #0a2342;
    }

    /* === Легенда === */
    .map-legend {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 10px 0;
        margin-top: 18px;
        padding: 16px 20px;
        background: #f8f8f8;
        border: 1px solid #e4e4e4;
        border-radius: 10px;
    }

    .map-legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #333;
        line-height: 1;
    }

    .map-legend-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.35);
        flex-shrink: 0;
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

<div class="map-legend" aria-label="Обозначения маркеров">
    <div class="map-legend-item">
        <span class="map-legend-dot" style="background:#2563eb"></span>
        <span>Жилой дом</span>
    </div>
    <div class="map-legend-item">
        <span class="map-legend-dot" style="background:#16a34a"></span>
        <span>Баня</span>
    </div>
    <div class="map-legend-item">
        <span class="map-legend-dot" style="background:#9333ea"></span>
        <span>Забор</span>
    </div>
    <div class="map-legend-item">
        <span class="map-legend-dot" style="background:#ea580c"></span>
        <span>Коммерция</span>
    </div>
    <div class="map-legend-item">
        <span class="map-legend-dot" style="background:#dc2626"></span>
        <span>Промышленные</span>
    </div>
    <div class="map-legend-item">
        <span class="map-legend-dot" style="background:#0891b2"></span>
        <span>Водные объекты</span>
    </div>
    <div class="map-legend-item">
        <span class="map-legend-dot" style="background:#ca8a04"></span>
        <span>Социальные</span>
    </div>
    <div class="map-legend-item">
        <span class="map-legend-dot" style="background:#65a30d"></span>
        <span>Сельхоз</span>
    </div>
    <div class="map-legend-item">
        <span class="map-legend-dot" style="background:#6b7280"></span>
        <span>Прочее</span>
    </div>
</div>

<?php if (!empty($published)): ?>
<section class="objects-section">
    <div class="objects-section__header">
        <h2 class="objects-section__title">Подробные описания объектов</h2>
        <span class="objects-section__count"><?= count($published) ?> объект<?= count($published) === 1 ? '' : (count($published) < 5 ? 'а' : 'ов') ?></span>
    </div>
    <div class="objects-grid">
        <?php foreach ($published as $obj):
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
<?php endif; ?>

<script>
    const CAT_COLORS = {
        house: '#2563eb',
        banya: '#16a34a',
        fence: '#9333ea',
        commercial: '#ea580c',
        industrial: '#dc2626',
        water: '#0891b2',
        social: '#ca8a04',
        agro: '#65a30d',
        other: '#6b7280'
    };

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

    async function initMap() {
        try {
            await ymaps3.ready;

            const {
                YMap,
                YMapDefaultSchemeLayer,
                YMapDefaultFeaturesLayer,
                YMapMarker,
                YMapControls,
                YMapControl,
                YMapTileDataSource,
                YMapLayer
            } = ymaps3;

            const map = new YMap(
                document.getElementById('map-works'),
                {
                    location: { center: [56.2, 58.0], zoom: 9 },
                    behaviors: ['drag', 'pinchZoom', 'scrollZoom', 'dblClick']
                }
            );

            // Слои карты — держим в переменных для управления
            let schemeLayer = new YMapDefaultSchemeLayer({ customization: MAP_CUSTOMIZATION });
            let satelliteSource = null;
            let satelliteLayer = null;
            let hybridSchemeLayer = null;

            // Начальный режим — схема
            map.addChild(schemeLayer);
            map.addChild(new YMapDefaultFeaturesLayer());

            // === Переключатель типа карты ===
            let currentType = 'scheme';

            function setMapType(type) {
                if (type === currentType) return;

                // Убираем текущие слои подложки
                if (currentType === 'scheme') {
                    map.removeChild(schemeLayer);
                } else if (currentType === 'satellite') {
                    map.removeChild(satelliteLayer);
                    map.removeChild(satelliteSource);
                } else if (currentType === 'hybrid') {
                    map.removeChild(satelliteLayer);
                    map.removeChild(satelliteSource);
                    map.removeChild(hybridSchemeLayer);
                }

                // Добавляем новые слои подложки
                if (type === 'scheme') {
                    schemeLayer = new YMapDefaultSchemeLayer({ customization: MAP_CUSTOMIZATION });
                    map.addChild(schemeLayer, 0); // вставляем первым
                } else if (type === 'satellite') {
                    satelliteSource = new YMapTileDataSource('satellite', { url: 'https://core-sat.maps.yandex.net/tiles?l=sat&x={x}&y={y}&z={z}&scale=1&lang=ru_RU' });
                    satelliteLayer  = new YMapLayer({ source: 'satellite', type: 'ground' });
                    map.addChild(satelliteSource, 0);
                    map.addChild(satelliteLayer, 1);
                } else if (type === 'hybrid') {
                    satelliteSource   = new YMapTileDataSource('satellite', { url: 'https://core-sat.maps.yandex.net/tiles?l=sat&x={x}&y={y}&z={z}&scale=1&lang=ru_RU' });
                    satelliteLayer    = new YMapLayer({ source: 'satellite', type: 'ground' });
                    hybridSchemeLayer = new YMapDefaultSchemeLayer({ theme: 'dark' });
                    map.addChild(satelliteSource, 0);
                    map.addChild(satelliteLayer, 1);
                    map.addChild(hybridSchemeLayer, 2);
                }

                currentType = type;

                // Обновляем активную кнопку
                document.querySelectorAll('.map-type-btn').forEach(btn => {
                    btn.classList.toggle('map-type-btn--active', btn.dataset.type === type);
                });
            }

            // Рендерим кнопки переключателя через YMapControls
            const controlEl = document.createElement('div');
            controlEl.className = 'map-type-control';
            [
                { type: 'scheme',    label: 'Схема'    },
                { type: 'satellite', label: 'Спутник'  },
                { type: 'hybrid',    label: 'Гибрид'   },
            ].forEach(({ type, label }) => {
                const btn = document.createElement('button');
                btn.className = 'map-type-btn' + (type === 'scheme' ? ' map-type-btn--active' : '');
                btn.dataset.type = type;
                btn.textContent = label;
                btn.addEventListener('click', () => setMapType(type));
                controlEl.appendChild(btn);
            });

            map.addChild(new YMapControls({ position: 'top right' }).addChild(new YMapControl().addChild(controlEl)));

            // === Кластеризация и маркеры ===
            ymaps3.import.registerCdn(
                'https://cdn.jsdelivr.net/npm/{package}',
                '@yandex/ymaps3-clusterer@0.0.1'
            );

            const { YMapClusterer, clusterByGrid } = await ymaps3.import('@yandex/ymaps3-clusterer');

            let objects = [];
            try {
                const res = await fetch('/data/map.json');
                if (!res.ok) throw new Error('HTTP ' + res.status);
                objects = await res.json();
            } catch (e) {
                console.error('[map] fetch error:', e);
            }

            const points = objects.map(obj => ({
                type: 'Feature',
                id: String(obj.id),
                geometry: { type: 'Point', coordinates: [obj.coords[1], obj.coords[0]] },
                properties: { obj }
            }));

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
                            if (obj.url) {
                                window.location.href = obj.url;
                            }
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

            map.addChild(new YMapClusterer({
                method: clusterByGrid({ gridSize: 64 }),
                features: points,
                marker,
                cluster
            }));

        } catch (e) {
            console.error('[map] error:', e);
        } finally {
            const loader = document.querySelector('.map-loader');
            if (loader) loader.style.display = 'none';
        }
    }

    initMap();
</script>

<?php
/**
 * Шаблон страницы объекта.
 * Требует: $object_id (int), $object_dir (string) — __DIR__ из index.php
 */

$map_data = json_decode(file_get_contents($object_dir . '/../../../data/map.json'), true);
$obj = null;
foreach ($map_data as $item) {
    if ($item['id'] === $object_id) { $obj = $item; break; }
}

if (!$obj) {
    http_response_code(404);
    require $object_dir . '/../../404/index.php';
    exit;
}

// Навигация prev/next: только объекты с url (опубликованные страницы)
$published = array_values(array_filter($map_data, fn($o) => !empty($o['url'])));
$current_idx = null;
foreach ($published as $idx => $o) {
    if ($o['id'] === $object_id) { $current_idx = $idx; break; }
}
$prev_obj = ($current_idx !== null && $current_idx > 0) ? $published[$current_idx - 1] : null;
$next_obj = ($current_idx !== null && $current_idx < count($published) - 1) ? $published[$current_idx + 1] : null;

$title            = htmlspecialchars($obj['title']) . ' — Завод винтовых свай Гефест';
$meta_description = 'Выполненный объект: ' . htmlspecialchars($obj['techDescription']) . '. Винтовые фундаменты от завода Гефест, г. Пермь.';
$canonical        = 'https://zavodsvay.ru/objects/' . $object_id . '/';
$og_image         = '/pages/map/img/' . $object_id . '_1.webp';
$img_base         = '/pages/map/img/';

// Координаты хранятся как [lat, lng] — ymaps3 принимает их напрямую
$obj_coords_js    = json_encode($obj['coords']);
$obj_id_js        = (int)$object_id;

ob_start();
?>
<article class="object-page">
    <div class="object-page__breadcrumbs">
        <a href="/">Главная</a> &rsaquo;
        <a href="/map/">Карта объектов</a> &rsaquo;
        <span><?= htmlspecialchars($obj['title']) ?></span>
    </div>

    <h1 class="object-page__title"><?= htmlspecialchars($obj['title']) ?></h1>

    <?php if (!empty($obj['techDescription'])): ?>
    <p class="object-page__desc"><?= htmlspecialchars($obj['techDescription']) ?></p>
    <?php endif; ?>

    <?php if (!empty($obj['images'])): ?>
    <div class="object-page__gallery">
        <?php foreach ($obj['images'] as $img): ?>
        <figure class="object-page__gallery-item">
            <img src="<?= $img_base . htmlspecialchars($img) ?>"
                 alt="<?= htmlspecialchars($obj['title']) ?>"
                 loading="lazy"
                 width="800" height="600">
        </figure>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <nav class="object-page__nav" aria-label="Навигация по объектам">
        <div class="object-page__nav-prev">
            <?php if ($prev_obj): ?>
            <a href="<?= htmlspecialchars($prev_obj['url']) ?>" class="object-page__nav-link object-page__nav-link--prev">
                <span class="object-page__nav-arrow">&larr;</span>
                <span class="object-page__nav-label">Предыдущий</span>
                <span class="object-page__nav-title"><?= htmlspecialchars($prev_obj['title']) ?></span>
            </a>
            <?php endif; ?>
        </div>
        <div class="object-page__nav-back">
            <a href="/map/" class="btn btn--outline">&uarr; Все объекты</a>
        </div>
        <div class="object-page__nav-next">
            <?php if ($next_obj): ?>
            <a href="<?= htmlspecialchars($next_obj['url']) ?>" class="object-page__nav-link object-page__nav-link--next">
                <span class="object-page__nav-arrow">&rarr;</span>
                <span class="object-page__nav-label">Следующий</span>
                <span class="object-page__nav-title"><?= htmlspecialchars($next_obj['title']) ?></span>
            </a>
            <?php endif; ?>
        </div>
    </nav>
</article>

<!-- Карта объекта -->
<section class="object-map-section">
    <h2 class="object-map-section__title">Расположение объекта</h2>
    <div id="object-map" class="object-map"></div>
</section>

<style>
    .object-map-section {
        margin-top: 40px;
    }
    .object-map-section__title {
        font-size: 1.2em;
        font-weight: 700;
        color: #0a2342;
        margin-bottom: 14px;
    }
    .object-map {
        width: 100%;
        height: 500px;
        background: #f0f0f0;
        border-radius: 10px;
        overflow: hidden;
        position: relative;
    }
    /* --- Object nav --- */
    .object-page__nav {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        gap: 12px;
        margin-top: 40px;
        padding-top: 24px;
        border-top: 1px solid #e0e4ea;
    }
    .object-page__nav-prev { justify-self: start; }
    .object-page__nav-back { justify-self: center; }
    .object-page__nav-next { justify-self: end; }
    .object-page__nav-link {
        display: flex;
        flex-direction: column;
        gap: 2px;
        text-decoration: none;
        color: #0a2342;
        padding: 10px 14px;
        border: 1px solid #d0d7e2;
        border-radius: 8px;
        max-width: 240px;
        transition: background 0.15s, border-color 0.15s;
    }
    .object-page__nav-link:hover {
        background: #f0f4fa;
        border-color: #2563eb;
    }
    .object-page__nav-link--prev { align-items: flex-start; }
    .object-page__nav-link--next { align-items: flex-end; text-align: right; }
    .object-page__nav-arrow {
        font-size: 1.1em;
        color: #2563eb;
    }
    .object-page__nav-label {
        font-size: 0.75em;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .object-page__nav-title {
        font-size: 0.875em;
        font-weight: 600;
        line-height: 1.3;
        color: #0a2342;
    }
    @media (max-width: 600px) {
        .object-page__nav {
            grid-template-columns: 1fr 1fr;
            grid-template-rows: auto auto;
        }
        .object-page__nav-prev { grid-column: 1; grid-row: 1; }
        .object-page__nav-next { grid-column: 2; grid-row: 1; justify-self: end; }
        .object-page__nav-back { grid-column: 1 / -1; grid-row: 2; justify-self: center; }
        .object-page__nav-link { max-width: 100%; }
    }
    /* --- Markers --- */
    .current-marker {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #f97316;
        border: 3px solid #fff;
        box-shadow: 0 0 0 3px rgba(249,115,22,0.4);
        cursor: default;
        animation: marker-pulse 2s ease-in-out infinite;
        position: relative;
        z-index: 20;
    }
    @keyframes marker-pulse {
        0%, 100% { box-shadow: 0 0 0 3px rgba(249,115,22,0.4); }
        50%       { box-shadow: 0 0 0 8px rgba(249,115,22,0.15); }
    }
    .custom-marker {
        border: 2px solid #fff;
        border-radius: 50%;
        width: 16px;
        height: 16px;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0,0,0,0.4);
        transition: transform 0.15s;
    }
    .custom-marker:hover { transform: scale(1.3); }
    .custom-marker--published {
        width: 20px;
        height: 20px;
        border: 3px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.5);
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
        box-shadow: 0 2px 6px rgba(0,0,0,0.35);
        cursor: pointer;
    }
</style>

<script src="https://api-maps.yandex.ru/v3/?apikey=c0204985-1515-44c3-8e4e-1a57f540fd94&lang=ru_RU"></script>
<script>
(async function() {
    const CURRENT_ID     = <?= $obj_id_js ?>;
    const CURRENT_COORDS = <?= $obj_coords_js ?>;  // [lat, lng] — подставляем напрямую

    const CAT_COLORS = {
        house: '#2563eb', banya: '#16a34a', fence: '#9333ea',
        commercial: '#ea580c', industrial: '#dc2626', water: '#0891b2',
        social: '#ca8a04', agro: '#65a30d', other: '#6b7280'
    };

    const MAP_CUSTOMIZATION = [
        {
            tags: { any: ['food','restaurant','cafe','bar','shop','supermarket','convenience'] },
            elements: 'geometry',
            stylers: [{ visibility: 'off' }]
        },
        { tags: { any: ['poi'] }, elements: 'label.icon', stylers: [{ visibility: 'off' }] }
    ];

    try {
        await ymaps3.ready;

        const { YMap, YMapDefaultSchemeLayer, YMapDefaultFeaturesLayer, YMapMarker } = ymaps3;

        const map = new YMap(
            document.getElementById('object-map'),
            {
                location: { center: CURRENT_COORDS, zoom: 13 },
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

        let objects = [];
        try {
            const res = await fetch('/data/map.json');
            if (!res.ok) throw new Error('HTTP ' + res.status);
            objects = await res.json();
        } catch(e) {
            console.error('[object-map] fetch error:', e);
        }

        // Текущий объект — отдельный маркер с пульсацией
        const currentEl = document.createElement('div');
        currentEl.className = 'current-marker';
        currentEl.title = <?= json_encode($obj['title'], JSON_UNESCAPED_UNICODE) ?>;
        map.addChild(new YMapMarker(
            { coordinates: CURRENT_COORDS, zIndex: 100 },
            currentEl
        ));

        // Остальные объекты — координаты напрямую без свопа
        const points = objects
            .filter(o => o.id !== CURRENT_ID)
            .map(o => ({
                type: 'Feature',
                id: String(o.id),
                geometry: { type: 'Point', coordinates: o.coords },
                properties: { obj: o }
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

        map.addChild(new YMapClusterer({
            method: clusterByGrid({ gridSize: 64 }),
            features: points,
            marker,
            cluster
        }));

    } catch(e) {
        console.error('[object-map] error:', e);
    }
})();
</script>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "CreativeWork",
    "name": <?= json_encode($obj['title'], JSON_UNESCAPED_UNICODE) ?>,
    "description": <?= json_encode($obj['techDescription'], JSON_UNESCAPED_UNICODE) ?>,
    "url": "<?= $canonical ?>",
    "creator": {"@id": "https://zavodsvay.ru/#organization"},
    "locationCreated": {
        "@type": "Place",
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": "<?= $obj['coords'][0] ?>",
            "longitude": "<?= $obj['coords'][1] ?>"
        }
    }
    <?php if (!empty($obj['images'])): ?>,
    "image": [<?= implode(',', array_map(fn($img) => json_encode('https://zavodsvay.ru' . $img_base . $img, JSON_UNESCAPED_UNICODE), $obj['images'])) ?>]
    <?php endif; ?>
}
</script>
<?php
$content = ob_get_clean();
include $object_dir . '/../../../layouts/main.php';

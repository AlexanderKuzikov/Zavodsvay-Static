<?php
$object_id = 25;

$map_data = json_decode(file_get_contents(__DIR__ . '/../../../data/map.json'), true);
$obj = null;
foreach ($map_data as $item) {
    if ($item['id'] === $object_id) { $obj = $item; break; }
}

if (!$obj) {
    http_response_code(404);
    require __DIR__ . '/../../404/index.php';
    exit;
}

$title            = htmlspecialchars($obj['title']) . ' — Завод винтовых свай Гефест';
$meta_description = 'Выполненный объект: ' . htmlspecialchars($obj['techDescription']) . '. Винтовые фундаменты от завода Гефест, г. Пермь.';
$canonical        = 'https://zavodsvay.ru/objects/' . $object_id . '/';
$og_image         = '/pages/map/img/' . $object_id . '_1.webp';

$img_base = '/pages/map/img/';

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

    <div class="object-page__back">
        <a href="/map/" class="btn btn--outline">&larr; Вернуться к карте объектов</a>
    </div>
</article>

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
include __DIR__ . '/../../../layouts/main.php';

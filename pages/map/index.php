<?php
$title = "Карта выполненных работ — Гефест";
$meta_description = "Карта объектов с выполненными работами по монтажу винтовых свай. Более 500 реализованных проектов в Пермском крае.";
$canonical = "https://zavodsvay.ru/map/";

$map_data = json_decode(file_get_contents(__DIR__ . '/../../data/map.json'), true);
$all_published = array_values(array_filter($map_data, fn($obj) => !empty($obj['url'])));

// По одному лучшему объекту из каждой категории (приоритет — есть фото)
$cat_order = ['house', 'banya', 'fence', 'commercial', 'industrial', 'water', 'social', 'agro', 'other'];
$published = [];
foreach ($cat_order as $cat) {
    $candidates = array_values(array_filter($all_published, fn($o) => $o['category'] === $cat));
    if (empty($candidates)) continue;
    // Сначала с фото, потом без
    usort($candidates, fn($a, $b) => (int)!empty($b['images']) - (int)!empty($a['images']));
    $published[] = $candidates[0];
}

ob_start();
require __DIR__ . '/content.php';
$content = ob_get_clean();

include __DIR__ . '/../../layouts/main.php';

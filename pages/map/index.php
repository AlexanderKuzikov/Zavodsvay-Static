<?php
$title = "Карта выполненных работ — Гефест";
$meta_description = "Карта объектов с выполненными работами по монтажу винтовых свай. Более 500 реализованных проектов в Пермском крае.";
$canonical = "https://zavodsvay.ru/map/";

$map_data = json_decode(file_get_contents(__DIR__ . '/../../data/map.json'), true);
$published = array_filter($map_data, fn($obj) => !empty($obj['url']));
$published = array_values($published);

ob_start();
require __DIR__ . '/content.php';
$content = ob_get_clean();

include __DIR__ . '/../../layouts/main.php';

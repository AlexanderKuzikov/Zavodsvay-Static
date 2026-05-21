<?php
$title = isset($title) ? $title : 'Сваи и бетон — Гефест';
$meta_description = isset($meta_description) ? $meta_description : '';
$canonical = "https://zavodsvay.ru/articles/beton/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2023-06-05';
$article_modified  = '2024-06-01';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

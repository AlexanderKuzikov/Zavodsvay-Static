<?php
$title = isset($title) ? $title : 'Строительство фундамента — Гефест';
$meta_description = isset($meta_description) ? $meta_description : '';
$canonical = "https://zavodsvay.ru/articles/stroitelstvo-fundamenta/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2024-01-01'; // TODO: уточнить реальную дату
$article_modified  = '2024-01-01'; // TODO: уточнить реальную дату

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

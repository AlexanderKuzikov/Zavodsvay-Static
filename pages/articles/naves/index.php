<?php
$title = isset($title) ? $title : 'Навес на винтовых сваях: расчёт и монтаж опор';
$meta_description = isset($meta_description) ? $meta_description : 'Фундамент под навес на винтовых сваях: сваи под навес для машины, террасы и крыльца. Парусность, шаг опор, монтаж в Перми и крае.';
$canonical = "https://zavodsvay.ru/articles/naves/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2026-08-10';
$article_modified  = '2026-08-12';
$extra_css = '<link rel="stylesheet" href="/assets/css/catalog.css">';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

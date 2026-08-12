<?php
$title = isset($title) ? $title : 'Ворота на винтовых сваях: откатные и распашные';
$meta_description = isset($meta_description) ? $meta_description : 'Фундамент для ворот на винтовых сваях: какие сваи под откатные и распашные ворота, шаг установки, монтаж за день. Решение для заборов в Перми.';
$canonical = "https://zavodsvay.ru/articles/vorota/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2026-08-10';
$article_modified  = '2026-08-12';
$extra_css = '<link rel="stylesheet" href="/assets/css/catalog.css">';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

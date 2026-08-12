<?php
$title = isset($title) ? $title : 'Пристройка к дому на винтовых сваях: фундамент';
$meta_description = isset($meta_description) ? $meta_description : 'Фундамент для пристройки на винтовых сваях: как связать со старым фундаментом, деформационный шов, монтаж вплотную к дому. Опыт из Перми.';
$canonical = "https://zavodsvay.ru/articles/pristroyka/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2026-08-10';
$article_modified  = '2026-08-12';
$extra_css = '<link rel="stylesheet" href="/assets/css/catalog.css">';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

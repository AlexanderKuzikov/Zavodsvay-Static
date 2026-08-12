<?php
$title = isset($title) ? $title : 'Веранда на винтовых сваях: фундамент и монтаж';
$meta_description = isset($meta_description) ? $meta_description : 'Фундамент для веранды на винтовых сваях: закрытая и открытая веранда, примыкание к дому, полы и ограждение. Монтаж под ключ в Перми.';
$canonical = "https://zavodsvay.ru/articles/veranda/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2026-08-10';
$article_modified  = '2026-08-12';
$extra_css = '<link rel="stylesheet" href="/assets/css/catalog.css">';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

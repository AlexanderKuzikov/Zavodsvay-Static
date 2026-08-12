<?php
$title = isset($title) ? $title : 'Терраса на винтовых сваях: фундамент под ключ';
$meta_description = isset($meta_description) ? $meta_description : 'Фундамент для террасы на винтовых сваях: расчёт под настил, шаг свай, монтаж на склоне и у воды. Решения для домов и дач в Пермском крае.';
$canonical = "https://zavodsvay.ru/articles/terrasa/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2026-08-10';
$article_modified  = '2026-08-12';
$extra_css = '<link rel="stylesheet" href="/assets/css/catalog.css">';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

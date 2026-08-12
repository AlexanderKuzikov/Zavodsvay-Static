<?php
$title = isset($title) ? $title : 'Гараж на винтовых сваях: какой фундамент выбрать';
$meta_description = isset($meta_description) ? $meta_description : 'Гараж на винтовых сваях: расчёт под каркасный, металлический и пеноблочный гараж, шаг свай, ростверк и полы. Практика монтажа в Пермском крае.';
$canonical = "https://zavodsvay.ru/articles/garazh/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2026-08-10';
$article_modified  = '2026-08-12';
$extra_css = '<link rel="stylesheet" href="/assets/css/catalog.css">';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

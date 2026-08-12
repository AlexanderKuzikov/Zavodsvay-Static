<?php
$title = isset($title) ? $title : 'Механизированный монтаж винтовых свай техникой';
$meta_description = isset($meta_description) ? $meta_description : 'Монтаж винтовых свай техникой: ямобур, сваевёрт или мини-экскаватор. Сроки, ограничения на участке, сравнение с ручным монтажом.';
$canonical = "https://zavodsvay.ru/articles/mashin/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2024-05-15';
$article_modified  = '2025-05-01';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

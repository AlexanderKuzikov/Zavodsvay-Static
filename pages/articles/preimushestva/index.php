<?php
$title = isset($title) ? $title : 'Преимущества винтовых свай: плюсы и минусы';
$meta_description = isset($meta_description) ? $meta_description : 'Плюсы и минусы винтовых свай: срок службы, монтаж зимой, работа на склоне и в торфе. Разбор для дома, бани и забора, сравнение с лентой.';
$canonical = "https://zavodsvay.ru/articles/preimushestva/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2022-09-08';
$article_modified  = '2024-08-20';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

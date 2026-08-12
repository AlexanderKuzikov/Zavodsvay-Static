<?php
$title = isset($title) ? $title : 'Виды винтовых свай: диаметр, длина и размеры';
$meta_description = isset($meta_description) ? $meta_description : 'Какие бывают винтовые сваи: диаметр, длина, толщина стенки и лопасти. Как выбрать правильную сваю под дом, баню, каркасник или забор.';
$canonical = "https://zavodsvay.ru/articles/vidy/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2022-04-15';
$article_modified  = '2024-03-10';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

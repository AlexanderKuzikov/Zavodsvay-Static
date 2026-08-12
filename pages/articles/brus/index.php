<?php
$title = isset($title) ? $title : 'Фундамент для дома из бруса на винтовых сваях';
$meta_description = isset($meta_description) ? $meta_description : 'Винтовые сваи под дом из бруса: диаметр, длина и шаг под стены 150-200 мм, ростверк, полы и цоколь. Практика монтажа в Перми.';
$canonical = "https://zavodsvay.ru/articles/brus/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2023-09-25';
$article_modified  = '2024-09-20';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

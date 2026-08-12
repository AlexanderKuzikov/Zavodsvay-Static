<?php
$title = isset($title) ? $title : 'Фундамент для дома из пеноблоков на винтовых сваях';
$meta_description = isset($meta_description) ? $meta_description : 'Винтовые сваи под дом из пеноблока: расчёт нагрузки, шаг свай, ростверк и защита стен от трещин. Что учесть перед строительством.';
$canonical = "https://zavodsvay.ru/articles/penobeton/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2023-07-19';
$article_modified  = '2024-07-08';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

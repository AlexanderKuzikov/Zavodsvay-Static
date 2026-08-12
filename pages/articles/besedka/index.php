<?php
$title = isset($title) ? $title : 'Беседка на винтовых сваях: фундамент своими руками';
$meta_description = isset($meta_description) ? $meta_description : 'Фундамент для беседки на винтовых сваях: 4-6 свай под типовую беседку 3х3, ручное вкручивание, обвязка брусом. Пошаговый план с размерами.';
$canonical = "https://zavodsvay.ru/articles/besedka/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2026-08-10';
$article_modified  = '2026-08-12';
$extra_css = '<link rel="stylesheet" href="/assets/css/catalog.css">';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

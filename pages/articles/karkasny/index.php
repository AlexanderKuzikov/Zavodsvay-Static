<?php
$title = isset($title) ? $title : 'Сваи для каркасного дома — Гефест';
$meta_description = isset($meta_description) ? $meta_description : '';
$canonical = "https://zavodsvay.ru/articles/karkasny/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2023-10-14';
$article_modified  = '2024-10-10';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

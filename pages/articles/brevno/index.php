<?php
$title = isset($title) ? $title : 'Сваи для бревенчатого дома — Гефест';
$meta_description = isset($meta_description) ? $meta_description : '';
$canonical = "https://zavodsvay.ru/articles/brevno/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2023-08-11';
$article_modified  = '2024-09-03';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

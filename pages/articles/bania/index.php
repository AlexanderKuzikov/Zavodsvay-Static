<?php
$title = isset($title) ? $title : 'Сваи для бани — Гефест';
$meta_description = isset($meta_description) ? $meta_description : '';
$canonical = "https://zavodsvay.ru/articles/bania/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2024-09-09';
$article_modified  = '2025-08-25';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

<?php
$title = isset($title) ? $title : 'Подпорная стена на сваях — Гефест';
$meta_description = isset($meta_description) ? $meta_description : '';
$canonical = "https://zavodsvay.ru/articles/podpornaystena/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2024-07-22';
$article_modified  = '2025-07-15';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

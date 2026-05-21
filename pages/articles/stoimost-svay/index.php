<?php
$title = isset($title) ? $title : 'Стоимость винтовых свай — Гефест';
$meta_description = isset($meta_description) ? $meta_description : '';
$canonical = "https://zavodsvay.ru/articles/stoimost-svay/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2025-02-12';
$article_modified  = '2026-02-01';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

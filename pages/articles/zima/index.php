<?php
$title = isset($title) ? $title : 'Завинчивание свай зимой — Гефест';
$meta_description = isset($meta_description) ? $meta_description : '';
$canonical = "https://zavodsvay.ru/articles/zima/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2024-12-05';
$article_modified  = '2025-11-20';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

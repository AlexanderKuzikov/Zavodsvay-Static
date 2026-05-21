<?php
$title = isset($title) ? $title : 'Наконечник винтовой сваи — Гефест';
$meta_description = isset($meta_description) ? $meta_description : '';
$canonical = "https://zavodsvay.ru/articles/nakon/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2025-05-08';
$article_modified  = '2026-05-01';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

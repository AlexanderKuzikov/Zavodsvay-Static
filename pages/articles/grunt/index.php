<?php
$title = isset($title) ? $title : 'Сваи в сложных грунтах — Гефест';
$meta_description = isset($meta_description) ? $meta_description : '';
$canonical = "https://zavodsvay.ru/articles/grunt/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2023-03-22';
$article_modified  = '2025-02-18';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

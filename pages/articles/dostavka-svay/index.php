<?php
$title = isset($title) ? $title : 'Доставка свай — Гефест';
$meta_description = isset($meta_description) ? $meta_description : '';
$canonical = "https://zavodsvay.ru/articles/dostavka-svay/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2025-03-20';
$article_modified  = '2026-03-10';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

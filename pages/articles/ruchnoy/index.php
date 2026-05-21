<?php
$title = isset($title) ? $title : 'Ручное завинчивание свай — Гефест';
$meta_description = isset($meta_description) ? $meta_description : '';
$canonical = "https://zavodsvay.ru/articles/ruchnoy/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2024-04-28';
$article_modified  = '2025-05-10';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

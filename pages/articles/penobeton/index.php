<?php
$title = isset($title) ? $title : 'Сваи для дома из пенобетона — Гефест';
$meta_description = isset($meta_description) ? $meta_description : '';
$canonical = "https://zavodsvay.ru/articles/penobeton/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2023-07-19';
$article_modified  = '2024-07-08';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

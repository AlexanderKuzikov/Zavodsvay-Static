<?php
$title = isset($title) ? $title : 'Виды винтовых свай — Гефест';
$meta_description = isset($meta_description) ? $meta_description : '';
$canonical = "https://zavodsvay.ru/articles/vidy/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2022-04-15';
$article_modified  = '2024-03-10';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

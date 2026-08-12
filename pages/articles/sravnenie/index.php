<?php
$title = isset($title) ? $title : 'Лучшие винтовые сваи: сравнение ВСГ-1 89 и 108';
$meta_description = isset($meta_description) ? $meta_description : 'Что лучше: винтовая свая ВСГ-1 89/300 или 108/300. Несущая способность, толщина стенки, область применения и для каких строений подходит.';
$canonical = "https://zavodsvay.ru/articles/sravnenie/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2023-05-17';
$article_modified  = '2024-04-12';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

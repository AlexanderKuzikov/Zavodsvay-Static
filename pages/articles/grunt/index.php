<?php
$title = isset($title) ? $title : 'Грунты и несущая способность винтовых свай';
$meta_description = isset($meta_description) ? $meta_description : 'Несущая способность винтовой сваи в глине, песке, торфе и на плывуне. Как длина и диаметр влияют на нагрузку, что учесть перед расчётом.';
$canonical = "https://zavodsvay.ru/articles/grunt/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2023-03-22';
$article_modified  = '2025-02-18';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

<?php
$title = isset($title) ? $title : 'Фундамент для бани на винтовых сваях: выбор и монтаж';
$meta_description = isset($meta_description) ? $meta_description : 'Винтовые сваи для бани: диаметр и длина под сруб 3х4 и 6х6, шаг установки, обвязка и полы. Советы по монтажу в Перми.';
$canonical = "https://zavodsvay.ru/articles/bania/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2024-09-09';
$article_modified  = '2025-08-25';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

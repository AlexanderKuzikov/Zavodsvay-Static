<?php
$title = isset($title) ? $title : 'Подпорная стена на винтовых сваях: склон и откосы';
$meta_description = isset($meta_description) ? $meta_description : 'Подпорная стена, пандус и откосы на винтовых сваях: как укрепить склон, шаг и длина свай, дренаж. Решения для участков с перепадом высот.';
$canonical = "https://zavodsvay.ru/articles/podpornaystena/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2024-07-22';
$article_modified  = '2025-07-15';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

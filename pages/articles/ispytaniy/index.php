<?php
$title = isset($title) ? $title : 'Испытания винтовых свай нагрузкой: как проводят';
$meta_description = isset($meta_description) ? $meta_description : 'Испытание винтовых свай: зачем проверять несущую способность, статическая и динамическая нагрузка, нормы и оформление результатов.';
$canonical = "https://zavodsvay.ru/articles/ispytaniy/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2024-10-03';
$article_modified  = '2025-09-18';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

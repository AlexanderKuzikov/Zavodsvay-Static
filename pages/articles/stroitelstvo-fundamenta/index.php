<?php
$title = isset($title) ? $title : 'Полы, цоколь и утепление дома на винтовых сваях';
$meta_description = isset($meta_description) ? $meta_description : 'Как сделать полы, цоколь и утепление дома на винтовых сваях: пирог перекрытия, вентиляция подполья, забирка и защита от промерзания.';
$canonical = "https://zavodsvay.ru/articles/stroitelstvo-fundamenta/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2023-11-07';
$article_modified  = '2025-04-15';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

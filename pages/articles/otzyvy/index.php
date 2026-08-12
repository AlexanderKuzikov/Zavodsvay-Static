<?php
$title = isset($title) ? $title : 'Отзывы о винтовых сваях: что пишут владельцы';
$meta_description = isset($meta_description) ? $meta_description : 'Отзывы владельцев о винтовых сваях Гефест: плюсы и минусы для дома, бани и забора. На что жалуются и что хвалят спустя годы.';
$canonical = "https://zavodsvay.ru/articles/otzyvy/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2025-09-15';
$article_modified  = '2026-05-10';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

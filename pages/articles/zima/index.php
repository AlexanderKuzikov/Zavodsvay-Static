<?php
$title = isset($title) ? $title : 'Монтаж винтовых свай зимой: можно ли ставить';
$meta_description = isset($meta_description) ? $meta_description : 'Можно ли ставить винтовые сваи зимой: монтаж в мёрзлый грунт, плюсы зимнего строительства, защита от морозного пучения. Опыт Перми.';
$canonical = "https://zavodsvay.ru/articles/zima/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2024-12-05';
$article_modified  = '2025-11-20';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

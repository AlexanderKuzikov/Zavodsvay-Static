<?php
$title = isset($title) ? $title : 'Расстояние между винтовыми сваями: шаг и схема';
$meta_description = isset($meta_description) ? $meta_description : 'Какое расстояние между винтовыми сваями выбрать: максимальный шаг, раскладка под стены, углы и лаги пола. Схемы свайного поля для дома и бани.';
$canonical = "https://zavodsvay.ru/articles/razmeshenie-svay/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2024-06-03';
$article_modified  = '2025-06-10';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

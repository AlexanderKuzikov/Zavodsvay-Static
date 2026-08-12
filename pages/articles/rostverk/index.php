<?php
$title = isset($title) ? $title : 'Обвязка винтовых свай: оголовки и ростверк';
$meta_description = isset($meta_description) ? $meta_description : 'Обвязка свайно-винтового фундамента: оголовок, брус, швеллер или профтруба. Как крепить обвязку, защитить узлы и вывести уровень в ноль.';
$canonical = "https://zavodsvay.ru/articles/rostverk/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2024-03-18';
$article_modified  = '2025-03-05';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

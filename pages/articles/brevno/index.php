<?php
$title = isset($title) ? $title : 'Фундамент для дома из бревна на винтовых сваях';
$meta_description = isset($meta_description) ? $meta_description : 'Винтовые сваи под дом из бревна: расчёт нагрузки, шаг свай, обвязка и защита от пучения. Разбор решений для срубов в Пермском крае.';
$canonical = "https://zavodsvay.ru/articles/brevno/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2023-08-11';
$article_modified  = '2024-09-03';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

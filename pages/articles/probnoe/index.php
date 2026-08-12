<?php
$title = isset($title) ? $title : 'Пробное вкручивание винтовых свай: зачем нужно';
$meta_description = isset($meta_description) ? $meta_description : 'Пробное вкручивание свай: как найти плотный грунт и подобрать длину до покупки фундамента. Услуга завода Гефест в Перми и крае.';
$canonical = "https://zavodsvay.ru/articles/probnoe/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2024-10-28';
$article_modified  = '2025-10-05';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

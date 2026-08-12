<?php
$title = isset($title) ? $title : 'Как рассчитать количество винтовых свай на фундамент';
$meta_description = isset($meta_description) ? $meta_description : 'Расчёт винтовых свай: сколько нужно под дом, баню или забор, свайное поле, шаг и нагрузка. Примеры расчёта для строений разных размеров.';
$canonical = "https://zavodsvay.ru/articles/raschet/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2023-04-10';
$article_modified  = '2025-03-25';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

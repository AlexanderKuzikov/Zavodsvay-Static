<?php
$title = isset($title) ? $title : 'Винтовые сваи для теплицы: надёжный фундамент';
$meta_description = isset($meta_description) ? $meta_description : 'Винтовые сваи под теплицу из поликарбоната: защита от ветра и пучения, монтаж за пару часов, перенос на новое место. Дачные решения в Перми.';
$canonical = "https://zavodsvay.ru/articles/teplitsa/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2026-08-10';
$article_modified  = '2026-08-12';
$extra_css = '<link rel="stylesheet" href="/assets/css/catalog.css">';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

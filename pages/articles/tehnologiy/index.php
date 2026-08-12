<?php
$title = isset($title) ? $title : 'Монтаж винтовых свай своими руками: технология';
$meta_description = isset($meta_description) ? $meta_description : 'Установка винтовых свай своими руками: разметка свайного поля, вкручивание сваекрутом, подрезка, бетонирование. Пошаговая инструкция, подъём дома на сваи.';
$canonical = "https://zavodsvay.ru/articles/tehnologiy/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2022-06-20';
$article_modified  = '2024-05-15';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

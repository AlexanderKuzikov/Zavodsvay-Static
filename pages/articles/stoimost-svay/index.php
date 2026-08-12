<?php
$title = isset($title) ? $title : 'Цена винтовых свай: от чего зависит стоимость';
$meta_description = isset($meta_description) ? $meta_description : 'Сколько стоят винтовые сваи: диаметр, длина, толщина стенки и покрытие влияют на цену. Как собрать смету: сваи, оголовки, доставка, монтаж.';
$canonical = "https://zavodsvay.ru/articles/stoimost-svay/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2025-02-12';
$article_modified  = '2026-02-01';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

<?php
$title = "Фундамент для ангара на винтовых сваях — Гефест";
$meta_description = "Свайно-винтовой фундамент для бескаркасных и каркасных ангаров: расчёт свай, схемы расстановки, полевые испытания и сравнение стоимости с ленточным фундаментом.";
$canonical = "https://zavodsvay.ru/articles/angar/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2026-01-01';
$article_modified  = '2026-01-01';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

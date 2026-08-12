<?php
$title = "Ангар на винтовых сваях: расчёт и монтаж фундамента";
$meta_description = "Как построить ангар на винтовых сваях: выбор диаметра и шага свай, монтаж на склоне и зимой. Практика завода из Перми.";
$canonical = "https://zavodsvay.ru/articles/angar/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2024-08-14';
$article_modified  = '2025-08-05';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

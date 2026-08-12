<?php
$title = isset($title) ? $title : 'Типичные ошибки монтажа винтовых свай и как их избежать';
$meta_description = isset($meta_description) ? $meta_description : 'Ошибки при устройстве фундамента на винтовых сваях: неверный шаг, недостаточная глубина, слабая обвязка. Разбор и способы предотвратить.';
$canonical = "https://zavodsvay.ru/articles/oshibki/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2024-11-19';
$article_modified  = '2025-11-10';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

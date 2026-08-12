<?php
$title = isset($title) ? $title : 'Аренда ямобура в Перми для винтовых свай';
$meta_description = isset($meta_description) ? $meta_description : 'Аренда ямобура завода Гефест для монтажа винтовых свай: когда нужна техника, подача на объект, стеснённые условия. Пермь и край.';
$canonical = "https://zavodsvay.ru/articles/arenda-yamobura/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2025-04-17';
$article_modified  = '2026-04-05';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

<?php
$title = isset($title) ? $title : 'История винтовых свай: от маяков до фундаментов';
$meta_description = isset($meta_description) ? $meta_description : 'История винтовых свай: изобретение в XIX веке, первые маяки и мосты, приход технологии в Россию. Почему сваи прижились в частном строительстве.';
$canonical = "https://zavodsvay.ru/articles/history/";
$og_type = 'article';
$schema_type = 'Article';
$article_published = '2022-11-14';
$article_modified  = '2023-10-05';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

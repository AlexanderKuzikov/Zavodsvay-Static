<?php
$title = "Виды винтовых свай";
$meta_description = "Обзор видов винтовых свай.";
$canonical = "https://zavodsvay.ru/articles/vidy-svay/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

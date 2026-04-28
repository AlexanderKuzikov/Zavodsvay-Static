<?php
$title = "Доставка винтовых свай";
$meta_description = "Условия доставки винтовых свай.";
$canonical = "https://zavodsvay.ru/articles/dostavka-svay/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

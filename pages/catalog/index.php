<?php
$title = "Каталог винтовых свай — Гефест";
$meta_description = "Каталог винтовых свай от производителя. Все диаметры и длины. Доставка и монтаж.";
$canonical = "https://zavodsvay.ru/catalog/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../layouts/main.php';

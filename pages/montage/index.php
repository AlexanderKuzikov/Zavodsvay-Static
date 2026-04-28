<?php
$title = "Услуги монтажа винтовых свай — Гефест";
$meta_description = "Профессиональный монтаж винтовых свай. Монтаж в день доставки. Гарантия.";
$canonical = "https://zavodsvay.ru/montage/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../layouts/main.php';

<?php
$title = "Аренда ямобура в Перми — Гефест";
$meta_description = "Аренда ямобуров в Перми: УБМ-85, КАМАЗ 43101 и КАМАЗ 4326. Монтаж винтовых свай и бурение отверстий до 1200 мм. Выезд по всему Пермскому краю.";
$canonical = "https://zavodsvay.ru/articles/arenda-yamobura/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

<?php
$title = "Фундамент для дома из бревна на винтовых сваях — Гефест";
$meta_description = "Свайно-винтовой фундамент под бревенчатый дом в Пермском крае: защита от морозного пучения, выбор свай, ростверк и сроки монтажа.";
$canonical = "https://zavodsvay.ru/articles/brevno/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

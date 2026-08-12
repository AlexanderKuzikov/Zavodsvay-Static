<?php
$title = "Статьи о винтовых сваях и фундаментах — Гефест";
$meta_description = "База знаний завода винтовых свай: технология монтажа, расчёты, сравнения и практика. Статьи инженеров с опытом 600+ объектов в Пермском крае.";
$canonical = "https://zavodsvay.ru/articles/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../layouts/main.php';

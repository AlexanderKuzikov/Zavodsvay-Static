<?php
$title = "Машинный монтаж фундамента на винтовых сваях — Гефест";
$meta_description = "Установка свай спецтехникой: ямобур и гидравлические машины. Преимущества машинного монтажа для крупных объектов и сложных условий грунта.";
$canonical = "https://zavodsvay.ru/articles/svoi-rukami/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

<?php
$title = "Калькулятор фундамента на винтовых сваях";
$meta_description = "Калькулятор фундамента на винтовых сваях.";
$canonical = "https://zavodsvay.ru/calc/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../layouts/main.php';

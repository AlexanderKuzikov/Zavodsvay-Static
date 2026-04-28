<?php
$title = "Строительство фундамента";
$meta_description = "Этапы строительства фундамента на винтовых сваях.";
$canonical = "https://zavodsvay.ru/articles/stroitelstvo-fundamenta/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

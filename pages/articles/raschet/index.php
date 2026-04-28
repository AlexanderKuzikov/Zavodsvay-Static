<?php
$title = "Расчёт количества свай";
$meta_description = "Как рассчитать количество винтовых свай для фундамента.";
$canonical = "https://zavodsvay.ru/articles/raschet-svay/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

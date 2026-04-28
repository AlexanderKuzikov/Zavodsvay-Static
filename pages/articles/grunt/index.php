<?php
$title = "Несущая способность винтовых свай в разных грунтах — Гефест";
$meta_description = "Как тип грунта влияет на выбор и несущую способность свай. Характеристики для суглинков, торфа, супесей и других видов грунтов.";
$canonical = "https://zavodsvay.ru/articles/fundament-na-svayah/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

<?php
$title = "Карта выполненных работ — Гефест";
$meta_description = "Карта объектов с выполненными работами по монтажу винтовых свай.";
$canonical = "https://zavodsvay.ru/map/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../layouts/wide.php';

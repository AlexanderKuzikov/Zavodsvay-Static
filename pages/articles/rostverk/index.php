<?php
$title = "Ростверк свайно-винтового фундамента — Гефест";
$meta_description = "Типы ростверков для свайно-винтового фундамента: высокий, повышенный и заглублённый. Выбор материала обвязки, типы оголовков, рекомендации по монтажу.";
$canonical = "https://zavodsvay.ru/articles/rostverk/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

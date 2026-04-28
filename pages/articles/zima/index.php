<?php
$title = "Фундамент на винтовых сваях зимой — Гефест";
$meta_description = "Монтаж свайно-винтового фундамента в зимних условиях: особенности завинчивания в мёрзлый грунт, выбор свай и сравнение с зимним бетонированием.";
$canonical = "https://zavodsvay.ru/articles/zima/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

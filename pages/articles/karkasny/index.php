<?php
$title = "Фундамент для каркасного дома на винтовых сваях — Гефест";
$meta_description = "Расчёт свайно-винтового фундамента для каркасного дома 6×6 м: выбор свай, ростверк из бруса, сроки и стоимость монтажа в Пермском крае.";
$canonical = "https://zavodsvay.ru/articles/karkasny/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

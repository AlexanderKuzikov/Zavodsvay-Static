<?php
$title = "Фундамент для бани на винтовых сваях — Гефест";
$meta_description = "Какие винтовые сваи выбрать для фундамента бани в Пермском крае: расчёт, типоразмеры, глубина погружения и особенности грунта под баней.";
$canonical = "https://zavodsvay.ru/articles/bania/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

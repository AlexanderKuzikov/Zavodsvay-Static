<?php
$title = "Фундамент для дома из бруса на винтовых сваях — Гефест";
$meta_description = "Свайно-винтовой фундамент под дом из бруса в Пермском крае: выбор типоразмера свай, шаг установки, ростверк из нижнего венца и монтаж за один день.";
$canonical = "https://zavodsvay.ru/articles/brus/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

<?php
$title = "Фундамент для дома из пенобетона на винтовых сваях — Гефест";
$meta_description = "Свайно-винтовой фундамент под дом из пеноблоков: выбор свай, высокий и повышенный ростверк, технология устройства на ровном участке и на склоне.";
$canonical = "https://zavodsvay.ru/articles/penobeton/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

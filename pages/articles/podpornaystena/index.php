<?php
$title = "Подпорные стены, пирсы и причалы на винтовых сваях — Гефест";
$meta_description = "Берегоукрепление и гидротехнические сооружения на винтовых сваях: подпорные стенки, пирсы и причалы. Опыт монтажа на Камском водохранилище через лёд.";
$canonical = "https://zavodsvay.ru/articles/podpornaystena/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

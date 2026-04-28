<?php
$title = "Преимущества фундамента на винтовых сваях — Гефест";
$meta_description = "Почему свайно-винтовой фундамент выгоднее ленточного и плитного: скорость монтажа, цена, минимальная нагрузка на грунт. Сравнение для частного строительства.";
$canonical = "https://zavodsvay.ru/articles/montazh-svay/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

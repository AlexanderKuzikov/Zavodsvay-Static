<?php
$title = "Негативные отзывы о винтовых сваях — как выбрать надёжного производителя";
$meta_description = "Почему появляются плохие отзывы о винтовых сваях: некачественное производство, ошибки монтажа, отсутствие расчётов. Как проверить подрядчика перед заказом.";
$canonical = "https://zavodsvay.ru/articles/otzyvy/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

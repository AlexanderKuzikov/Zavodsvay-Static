<?php
$title = "Типичные ошибки при заказе фундамента на сваях — Гефест";
$meta_description = "Разбираем распространённые ошибки заказчиков: занижение числа свай, неверный диаметр, отказ от обвязки. Как не допустить проблем при строительстве.";
$canonical = "https://zavodsvay.ru/articles/vidy-svay/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

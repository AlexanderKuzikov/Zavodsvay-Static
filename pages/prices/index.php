<?php
$title = "Цены на винтовые сваи в Перми — прайс-лист";
$meta_description = "Актуальный прайс на винтовые сваи ВСГ и работы по монтажу в Перми. Рассчитаем смету по вашему проекту: диаметр, длина, количество.";
$canonical = "https://zavodsvay.ru/prices/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../layouts/main.php';

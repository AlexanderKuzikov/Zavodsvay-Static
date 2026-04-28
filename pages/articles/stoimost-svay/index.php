<?php
$title = "Стоимость винтовых свай — Гефест";
$meta_description = "Из чего складывается цена на винтовые сваи: диаметр, длина, покрытие и тип лопасти. Советы по оптимизации бюджета на фундамент.";
$canonical = "https://zavodsvay.ru/articles/stoimost-svay/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

<?php
$title            = '[Preview E] Главная — Завод Гефест';
$meta_description = 'Производство и монтаж винтовых свай в Перми.';
$canonical        = 'https://zavodsvay.ru/';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../layouts/preview-e.php';

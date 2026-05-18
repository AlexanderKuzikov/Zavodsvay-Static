<?php
/**
 * Preview D — на основе референсного макета
 */
$title            = '[Preview D] Главная — Завод Гефест';
$meta_description = 'Производство и монтаж винтовых свай в Перми. Фундаменты под ключ. Гарантия до 50 лет.';
$canonical        = 'https://zavodsvay.ru/';
$extra_css        = '<link rel="stylesheet" href="/assets/css/home-d.css">';

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../layouts/home.php';

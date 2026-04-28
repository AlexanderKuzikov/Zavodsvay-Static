<?php
/**
 * Главная страница
 */
$title = "Винтовые сваи в Перми — Завод Гефест";
$meta_description = "Производство и монтаж винтовых свай в Перми. Фундаменты под ключ. Собственное производство. Гарантия качества.";
$canonical = "https://zavodsvay.ru/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../layouts/home.php';

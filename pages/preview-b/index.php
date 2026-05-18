<?php
/**
 * Preview: Вариант B — Product First
 */
$title = "[Preview B] Product First — Главная";
$meta_description = "Preview variant B";
$canonical = "https://zavodsvay.ru/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../layouts/home.php';

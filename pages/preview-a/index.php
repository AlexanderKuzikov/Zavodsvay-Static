<?php
/**
 * Preview: Вариант A — Trust First
 */
$title = "[Preview A] Trust First — Главная";
$meta_description = "Preview variant A";
$canonical = "https://zavodsvay.ru/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../layouts/home.php';

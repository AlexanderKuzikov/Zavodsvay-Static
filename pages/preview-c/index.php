<?php
/**
 * Preview: Вариант C — Map First
 */
$title = "[Preview C] Map First — Главная";
$meta_description = "Preview variant C";
$canonical = "https://zavodsvay.ru/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../layouts/home.php';

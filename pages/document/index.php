<?php
$title = "Техническая документация — Гефест";
$meta_description = "Техническая документация на винтовые сваи. Сертификаты, ГОСТ, чертежи.";
$canonical = "https://zavodsvay.ru/document/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../layouts/main.php';

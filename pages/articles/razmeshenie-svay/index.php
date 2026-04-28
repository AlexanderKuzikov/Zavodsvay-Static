<?php
$title = "Размещение винтовых свай в фундаменте — Гефест";
$meta_description = "Правила расстановки свай под углы, ленту и тяжёлые узлы. Схемы и рекомендации по шагу для жилых домов, бань и хозяйственных построек.";
$canonical = "https://zavodsvay.ru/articles/vintovye-svai/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

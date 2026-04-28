<?php
$title = "Сваи со сварной и литой лопастью — сравнение — Гефест";
$meta_description = "Чем отличаются сваи со сварной лопастью от литых: прочность, срок службы и цена. Практические рекомендации по выбору для разных условий эксплуатации.";
$canonical = "https://zavodsvay.ru/articles/vidy-svay/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

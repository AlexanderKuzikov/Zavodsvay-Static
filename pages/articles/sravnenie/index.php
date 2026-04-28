<?php
$title = "Сравнение винтовых свай ВСГ-1 89/300 и ВС-1 108/300 — Гефест";
$meta_description = "Чем отличаются сваи ВСГ и ВС с одинаковой несущей способностью: материал ствола, прочность, коррозионная стойкость и поведение при зимнем монтаже.";
$canonical = "https://zavodsvay.ru/articles/sravnenie/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

<?php
$title = "Технология производства винтовых свай — Гефест";
$meta_description = "Как изготавливаются винтовые сваи: материалы, сварка и покрытие. Требования ГОСТ и контроль качества на заводе Гефест в Перми.";
$canonical = "https://zavodsvay.ru/articles/texnologiya/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

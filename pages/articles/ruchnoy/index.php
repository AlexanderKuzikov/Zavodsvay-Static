<?php
$title = "Ручной монтаж фундамента на винтовых сваях — Гефест";
$meta_description = "Технология ручного завинчивания свай: инструменты, контроль глубины и вертикали. Когда ручной монтаж предпочтительнее машинного способа установки.";
$canonical = "https://zavodsvay.ru/articles/preimushchestva-svay/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

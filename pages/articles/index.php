<?php
$title = "Виды винтовых свай — Гефест";
$meta_description = "Обзор видов винтовых свай: однолопастные, многолопастные, с литой и сварной лопастью. Особенности применения каждого вида в строительстве.";
$canonical = "https://zavodsvay.ru/articles/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../layouts/main.php';

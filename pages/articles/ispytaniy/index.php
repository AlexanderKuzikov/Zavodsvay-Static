<?php
$title = "Испытания винтовых свай — Гефест";
$meta_description = "Статические и динамические испытания свай на площадке: методика, цели и результаты. Соответствие требованиям ГОСТ Р 59106-2020.";
$canonical = "https://zavodsvay.ru/articles/svai-dlya-zabora/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

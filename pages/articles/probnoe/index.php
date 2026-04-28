<?php
$title = "Пробное завинчивание винтовой сваи — полевые испытания грунтов";
$meta_description = "Методика полевых испытаний винтовых свай: состав установки, порядок нагружения, критерии стабилизации деформации и требования к контрольным испытаниям.";
$canonical = "https://zavodsvay.ru/articles/probnoe/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

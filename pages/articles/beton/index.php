<?php
$title = "Бетонирование винтовой сваи — зачем заполнять полость ствола";
$meta_description = "Нужно ли бетонировать внутреннюю полость винтовой сваи: разбираем причины, разницу между трубами ВГП и НКТ и когда без бетона можно обойтись.";
$canonical = "https://zavodsvay.ru/articles/beton/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../../layouts/main.php';

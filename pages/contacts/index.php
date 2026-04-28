<?php
$title = "Контакты — Завод винтовых свай Гефест";
$meta_description = "Контактная информация завода винтовых свай Гефест. Адрес, телефон, мессенджеры.";
$canonical = "https://zavodsvay.ru/contacts/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../layouts/main.php';

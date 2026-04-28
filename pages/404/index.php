<?php
/**
 * Страница 404
 */
http_response_code(404);
$title = "Страница не найдена — Завод винтовых свай Гефест";
$meta_description = "Страница не найдена. Вернитесь на главную или воспользуйтесь меню.";
$canonical = "https://zavodsvay.ru/404/";

ob_start();
readfile(__DIR__ . '/content.html');
$content = ob_get_clean();

include __DIR__ . '/../../layouts/main.php';

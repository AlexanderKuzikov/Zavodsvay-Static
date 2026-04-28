<?php
/**
 * Точка входа - роутер
 * Перенаправляет запросы на соответствующие страницы в /pages/
 */

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Убираем возможные query параметры
$uri = explode('?', $uri)[0];

// Главная страница
if ($uri === '' || $uri === 'index.php') {
    $page = __DIR__ . '/pages/index/index.php';
} else {
    $page = __DIR__ . '/pages/' . $uri . '/index.php';
}

if (file_exists($page)) {
    require $page;
} else {
    http_response_code(404);
    require __DIR__ . '/pages/404/index.php';
}

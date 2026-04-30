<?php
/**
 * Точка входа - роутер
 * Перенаправляет запросы на соответствующие страницы в /pages/
 */

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$uri = explode('?', $uri)[0];
$uri = rtrim($uri, '/');

// Блокируем прямой доступ к pages/
if (strpos($uri, 'pages/') === 0) {
    http_response_code(403);
    exit;
}

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
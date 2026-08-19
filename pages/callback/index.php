<?php
/**
 * pages/callback/index.php — POST /callback/ «Заказать звонок»
 * Валидация, rate-limit, уведомление на stas@zavodsvay.ru через mail().
 * Ответ всегда JSON.
 */

header('Content-Type: application/json; charset=utf-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'method_not_allowed']);
    exit;
}

function respond(int $code, bool $ok, string $error = ''): void
{
    http_response_code($code);
    echo json_encode(['ok' => $ok, 'error' => $error]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = $_POST;
}

// Honeypot: бот заполняет скрытое поле — отвечаем успехом, письмо не шлём
if (!empty($input['company'])) {
    respond(200, true);
}

// Rate-limit: не чаще одного запроса за 120 секунд на сессию
session_start();
if (isset($_SESSION['callback_last']) && (time() - (int) $_SESSION['callback_last']) < 120) {
    respond(429, false, 'rate_limited');
}

$phone = trim((string) ($input['phone'] ?? ''));
$digits = preg_replace('/\D/', '', $phone);
if ($digits === '' || strlen($digits) < 10 || strlen($digits) > 15) {
    respond(422, false, 'invalid_phone');
}

// Анти-спам по числу отправок с IP за сутки (файловый лог, ~1 КБ)
$logFile = __DIR__ . '/../../data/leads-callback.log';
$ip      = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$logLine = date('c') . '|' . $ip . '|' . preg_replace('/[^0-9+()\-\s]/', '', $phone) . '|' . substr(preg_replace('/[^\x20-\x7E]/', '', (string) ($_SERVER['HTTP_REFERER'] ?? '')), 0, 500) . "\n";
if (is_file($logFile) && filemtime($logFile) > time() - 86400) {
    $todayHits = 0;
    foreach (file($logFile, FILE_IGNORE_NEW_LINES) as $line) {
        if (str_ends_with($line, '|' . $ip)) {
            $todayHits++;
        }
    }
    if ($todayHits >= 5) {
        respond(429, false, 'rate_limited');
    }
}
@file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);

$_SESSION['callback_last'] = time();

$subject = 'Заказ звонка: ' . $phone;
$body  = "Поступила заявка на обратный звонок с сайта zavodsvay.ru\n\n";
$body .= "Телефон:  {$phone}\n";
$body .= "Страница: " . (preg_replace('/[^\x20-\x7E]/', '', (string) ($_SERVER['HTTP_REFERER'] ?? '')) ?: '-') . "\n";
$body .= "Время:    " . date('d.m.Y H:i') . "\n";
$body .= "IP:       {$ip}\n";

$headers  = "From: webmaster@zavodsvay.ru\r\n";
$headers .= "Reply-To: webmaster@zavodsvay.ru\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=utf-8\r\n";

$sent = @mail('stas@zavodsvay.ru', $subject, $body, $headers);
if (!$sent) {
    respond(500, false, 'send_failed');
}
respond(200, true);

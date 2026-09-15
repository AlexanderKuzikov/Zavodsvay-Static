<?php
/**
 * pages/callback/index.php — POST /callback/ «Заказать звонок»
 * Валидация, rate-limit, уведомление через SMTP (см. конфиг),
 * fallback — mail(). Ответ всегда JSON.
 * Получатель заявок: stas@zavodsvay.ru.
 * SMTP-конфиг — ВНЕ git и ВНЕ webroot:
 *   <home>/callback-smtp-config.php  (залит по FTP вручную)
 *   возвращает ['host' => ..., 'user' => ..., 'pass' => ...]
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

/**
 * Отправка письма через SMTP без зависимостей: сначала implicit SSL (465),
 * затем STARTTLS (587). true — только если сервер принял письмо (250 после точки).
 */
function smtp_send(string $host, string $user, string $pass, string $envelopeFrom, string $to, string $subject, string $body, string $headers): bool
{
    foreach ([['ssl', 465], ['tcp', 587]] as [$scheme, $port]) {
        if (smtp_attempt($scheme, $host, $port, $user, $pass, $envelopeFrom, $to, $subject, $body, $headers)) {
            return true;
        }
    }
    return false;
}

function smtp_attempt(string $scheme, string $host, int $port, string $user, string $pass, string $envelopeFrom, string $to, string $subject, string $body, string $headers): bool
{
    $tag = "{$scheme}:{$port}";
    $err = error_reporting(0);
    $fp = @stream_socket_client("{$scheme}://{$host}:{$port}", $errno, $errstr, 8);
    error_reporting($err);
    if (!$fp) {
        error_log("callback smtp fail at {$tag}.connect errno={$errno} err={$errstr}");
        return false;
    }
    stream_set_timeout($fp, 10);
    $read = static function () use ($fp): string {
        $out = '';
        while (($line = fgets($fp, 512)) !== false) {
            $out .= $line;
            if (preg_match('/^\d{3} /', $line)) {
                break;
            }
        }
        return $out;
    };
    $expect = static function (string $resp, string ...$codes): bool {
        foreach ($codes as $c) {
            if (str_starts_with($resp, $c)) {
                return true;
            }
        }
        return false;
    };
    $stage = 'greet';
    $fail = static function () use ($fp, &$stage, $tag): bool {
        error_log("callback smtp fail at {$tag}.{$stage}");
        fclose($fp);
        return false;
    };
    if (!$expect($read(), '220')) {
        return $fail();
    }
    $stage = 'ehlo';
    fwrite($fp, "EHLO zavodsvay.ru\r\n");
    if (!$expect($read(), '250')) {
        return $fail();
    }
    if ($scheme === 'tcp') {
        $stage = 'starttls';
        fwrite($fp, "STARTTLS\r\n");
        if (!$expect($read(), '220')) {
            return $fail();
        }
        $stage = 'tlshandshake';
        $cerr = error_reporting(0);
        $cok = @stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        error_reporting($cerr);
        if (!$cok) {
            return $fail();
        }
        $stage = 'ehlo2';
        fwrite($fp, "EHLO zavodsvay.ru\r\n");
        if (!$expect($read(), '250')) {
            return $fail();
        }
    }
    $stage = 'auth';
    fwrite($fp, "AUTH LOGIN\r\n");
    if (!$expect($read(), '334')) {
        return $fail();
    }
    fwrite($fp, base64_encode($user) . "\r\n");
    if (!$expect($read(), '334')) {
        return $fail();
    }
    fwrite($fp, base64_encode($pass) . "\r\n");
    if (!$expect($read(), '235')) {
        return $fail();
    }
    $stage = 'mailfrom';
    fwrite($fp, "MAIL FROM:<{$envelopeFrom}>\r\n");
    if (!$expect($read(), '250')) {
        return $fail();
    }
    $stage = 'rcptto';
    fwrite($fp, "RCPT TO:<{$to}>\r\n");
    if (!$expect($read(), '250', '251')) {
        return $fail();
    }
    $stage = 'data';
    fwrite($fp, "DATA\r\n");
    if (!$expect($read(), '354')) {
        return $fail();
    }
    $msg = "To: {$to}\r\nSubject: {$subject}\r\n{$headers}\r\n{$body}";
    $msg = str_replace("\r\n", "\n", $msg);
    $msg = str_replace("\n", "\r\n", $msg);
    $msg = (string) preg_replace('/^\./m', '..', $msg);
    fwrite($fp, $msg . "\r\n.\r\n");
    $ok = $expect($read(), '250');
    fwrite($fp, "QUIT\r\n");
    fclose($fp);
    return $ok;
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
// Санитизация для письма: только безопасные символы (защита от инъекции заголовков)
$phone = substr((string) preg_replace('/[^\d+()\-\s]/', '', $phone), 0, 30);

// Анти-спам: лимит заявок с одного IP за сутки.
// ВРЕМЕННО ВЫКЛЮЧЕН на период отладки доставки (0 = без лимита). Прод: 5.
$IP_DAILY_LIMIT = 0;
$logFile = __DIR__ . '/../../data/leads-callback.log';
$ip      = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$ref     = substr((string) preg_replace('/[^\x20-\x7E]/', '', (string) ($_SERVER['HTTP_REFERER'] ?? '')), 0, 500);
$logLine = date('c') . '|' . $ip . '|' . $phone . '|' . $ref . "\n";
$dayAgo  = time() - 86400;
if (is_file($logFile) && $IP_DAILY_LIMIT > 0) {
    $hits = 0;
    foreach (file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $f = explode('|', $line, 4);
        if (count($f) < 3) {
            continue;
        }
        if (($f[1] ?? '') === $ip && (strtotime((string) ($f[0] ?? '')) ?: 0) > $dayAgo) {
            $hits++;
            if ($hits >= $IP_DAILY_LIMIT) {
                respond(429, false, 'rate_limited');
            }
        }
    }
    // Ротация: не даём логу расти бесконечно
    if (filesize($logFile) > 102400) {
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        @file_put_contents($logFile, implode("\n", array_slice($lines, -500)) . "\n", LOCK_EX);
    }
}
@file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);

$_SESSION['callback_last'] = time();

$to = 'stas@zavodsvay.ru';

$subject = '=?UTF-8?B?' . base64_encode('Заказ звонка: ' . $phone) . '?=';
$body  = "Поступила заявка на обратный звонок с сайта zavodsvay.ru\n\n";
$body .= "Телефон:  {$phone}\n";
$body .= 'Страница: ' . ($ref ?: '-') . "\n";
$body .= 'Время:    ' . date('d.m.Y H:i') . "\n";
$body .= "IP:       {$ip}\n";

$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=utf-8\r\n";
$headers .= "Content-Transfer-Encoding: 8bit\r\n";

// SMTP-конфиг — вне webroot (см. шапку). Нет конфига — сразу к mail().
$smtpCfg = [];
$cfgFile = dirname(__DIR__, 4) . '/callback-smtp-config.php';
if (is_file($cfgFile)) {
    $smtpCfg = (require $cfgFile) ?: [];
}

$sent = false;
$smtpFrom = '';
if (!empty($smtpCfg['user']) && isset($smtpCfg['pass'])) {
    // Конвертный отправитель = ящик из конфига (совпадает с SMTP-логином)
    $smtpHost = (string) ($smtpCfg['host'] ?? 'kompleks-s.ru');
    $smtpFrom = (string) $smtpCfg['user'];
    $fromName = '=?UTF-8?B?' . base64_encode('Заказ звонка zavodsvay.ru') . '?=';
    $headers  = "From: {$fromName} <{$smtpFrom}>\r\n" . $headers;
    $sent = smtp_send($smtpHost, $smtpFrom, (string) $smtpCfg['pass'], $smtpFrom, $to, $subject, $body, $headers);
}
if (!$sent) {
    $sent = @mail($to, $subject, $body, $headers);
}
if (!$sent) {
    respond(500, false, 'send_failed');
}
respond(200, true);

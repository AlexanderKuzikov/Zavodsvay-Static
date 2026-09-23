<?php
/**
 * pages/inbox/index.php — POST /inbox/ «Входящие» (очередь заявок вместо почты)
 *
 * Приём заявок из MapControl по закрытому токену, хранение очереди на диске,
 * мини-админка (список / просмотр / approve / reject / скачивание пакета),
 * уведомление письмом БЕЗ фото (только ссылка на админку).
 *
 * Роутер: index.php отдаёт /inbox/ сюда (pages/inbox/index.php).
 * Публикация в data/map.json — НЕ здесь (остаётся локальной через add-object + деплой).
 *
 * Закрытые конфиги — ВНЕ git и ВНЕ webroot (залиты по FTP вручную):
 *   <home>/inbox-config.php     возвращает ['token' => ..., 'notify_to' => ...]
 *   <home>/callback-smtp-config.php  возвращает ['host' => ..., 'user' => ..., 'pass' => ...]
 * Формат — как callback-smtp-config.php. Локально для тестов — env INBOX_TOKEN / INBOX_NOTIFY_TO.
 *
 * Контракт приёма (для MapControl, шаг 0009):
 *   POST /inbox/ multipart/form-data:
 *     token  — или заголовок X-Inbox-Token, или ?token= в query, или Bearer
 *     meta   — JSON меты MapControl (как data/submissions/pending/{id}/meta.json)
 *     images — файлы WebP (до 20, каждый до 30 МБ; имена строго = meta.images)
 *   200 {"ok":true,"id":...,"images":N,"notify_sent":bool}
 *   без токена → 401/403; кривые данные → 400; дубль submission_id → 409 (без перезаписи).
 *
 * Админка (тот же токен в query ?token=):
 *   GET  /inbox/?token=T                       — список очереди
 *   GET  /inbox/?token=T&action=view&id=X      — просмотр меты + фото
 *   GET  /inbox/?token=T&action=meta&id=X      — скачать meta.json
 *   GET  /inbox/?token=T&action=image&id=X&file=F — фото (через PHP, диск закрыт .htaccess)
 *   GET  /inbox/?token=T&action=download&id=X  — ZIP-пакет (meta.json + images/)
 *   POST /inbox/?token=T action=approve&id=X   — status=approved
 *   POST /inbox/?token=T action=reject&id=X&reason=... — status=rejected + rejection_reason
 */

const INBOX_MAX_FILES = 20;
const INBOX_MAX_FILE_SIZE = 30 * 1024 * 1024;
const INBOX_DIR_NAME = 'inbox';

// ---------- helpers ----------

function inbox_json(int $code, array $payload): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function inbox_len(string $s): int
{
    if (function_exists('mb_strlen')) {
        return mb_strlen($s, 'UTF-8');
    }
    $a = preg_split('//u', $s, -1, PREG_SPLIT_NO_EMPTY);
    return $a === false ? strlen($s) : count($a);
}

/** Конфиг приёмника: файл вне webroot, иначе env (для локальных тестов). */
function inbox_load_config(): array
{
    $cfg = [];
    $file = dirname(__DIR__, 4) . '/inbox-config.php';
    if (is_file($file)) {
        $cfg = (require $file) ?: [];
    }
    if (empty($cfg['token'])) {
        $env = getenv('INBOX_TOKEN');
        if (is_string($env) && $env !== '') {
            $cfg['token'] = $env;
        }
    }
    if (empty($cfg['notify_to'])) {
        $env = getenv('INBOX_NOTIFY_TO');
        if (is_string($env) && $env !== '') {
            $cfg['notify_to'] = $env;
        }
    }
    return is_array($cfg) ? $cfg : [];
}

/** SMTP-конфиг почты: тот же закрытый файл, что у /callback/. */
function inbox_load_smtp(): array
{
    $cfg = [];
    $file = dirname(__DIR__, 4) . '/callback-smtp-config.php';
    if (is_file($file)) {
        $cfg = (require $file) ?: [];
    }
    return is_array($cfg) ? $cfg : [];
}

/** Токен из заголовка X-Inbox-Token / Bearer / query / POST / JSON-тела. */
function inbox_provided_token(array $jsonBody): string
{
    $hdr = (string) ($_SERVER['HTTP_X_INBOX_TOKEN'] ?? '');
    if ($hdr !== '') {
        return $hdr;
    }
    $auth = (string) ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
    if (preg_match('/^Bearer\s+(.+)$/i', trim($auth), $m)) {
        return trim($m[1]);
    }
    if (isset($_GET['token']) && is_string($_GET['token']) && $_GET['token'] !== '') {
        return $_GET['token'];
    }
    if (isset($_POST['token']) && is_string($_POST['token']) && $_POST['token'] !== '') {
        return $_POST['token'];
    }
    if (isset($jsonBody['token']) && is_string($jsonBody['token'])) {
        return $jsonBody['token'];
    }
    return '';
}

function inbox_check_token(string $provided, string $expected): string
{
    // 'ok' | 'missing' | 'invalid' | 'not_configured'
    if ($expected === '') {
        return 'not_configured';
    }
    if ($provided === '') {
        return 'missing';
    }
    return hash_equals($expected, $provided) ? 'ok' : 'invalid';
}

function inbox_sanitize_id(mixed $id): string|false
{
    if (!is_string($id) || !preg_match('/^[A-Za-z0-9_-]{1,32}$/', $id)) {
        return false;
    }
    return $id;
}

function inbox_base(): string
{
    $p = __DIR__ . '/../../data/' . INBOX_DIR_NAME;
    $r = realpath($p);
    return $r !== false ? $r : $p;
}

function inbox_paths(string $id): array
{
    $root = inbox_base() . '/' . $id;
    return ['root' => $root, 'meta' => $root . '/meta.json', 'images' => $root . '/images'];
}

/** Строгая проверка «путь внутри очереди» (защита от обхода путей). */
function inbox_inside(string $path): bool
{
    $baseN = str_replace('\\', '/', inbox_base());
    // Существующий путь — через realpath; иначе через realpath каталога + basename.
    $real = realpath($path);
    if ($real === false) {
        $realDir = realpath(dirname($path));
        $resolved = $realDir !== false
            ? str_replace('\\', '/', $realDir) . '/' . basename($path)
            : str_replace('\\', '/', $path);
    } else {
        $resolved = str_replace('\\', '/', $real);
    }
    return $resolved === $baseN || str_starts_with($resolved, $baseN . '/');
}

/** Атомарная запись JSON: tmp в том же каталоге + rename. */
function inbox_write_atomic(string $path, array $data): bool
{
    $tmp = $path . '.tmp-' . getmypid() . '-' . time();
    if (@file_put_contents($tmp, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n", LOCK_EX) === false) {
        return false;
    }
    if (!@rename($tmp, $path)) {
        @unlink($tmp);
        return false;
    }
    return true;
}

// ---------- SMTP (копия клиента из pages/callback/index.php) ----------

function inbox_smtp_send(string $host, string $user, string $pass, string $envelopeFrom, string $to, string $subject, string $body, string $headers): bool
{
    foreach ([['ssl', 465], ['tcp', 587]] as [$scheme, $port]) {
        if (inbox_smtp_attempt($scheme, $host, $port, $user, $pass, $envelopeFrom, $to, $subject, $body, $headers)) {
            return true;
        }
    }
    return false;
}

function inbox_smtp_attempt(string $scheme, string $host, int $port, string $user, string $pass, string $envelopeFrom, string $to, string $subject, string $body, string $headers): bool
{
    $tag = "{$scheme}:{$port}";
    $err = error_reporting(0);
    $fp = @stream_socket_client("{$scheme}://{$host}:{$port}", $errno, $errstr, 8);
    error_reporting($err);
    if (!$fp) {
        error_log("inbox smtp fail at {$tag}.connect errno={$errno} err={$errstr}");
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
        error_log("inbox smtp fail at {$tag}.{$stage}");
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

// ---------- pure-PHP ZIP (stored, без расширений) ----------

function inbox_dostime(int $ts): array
{
    $d = getdate($ts);
    $time = ($d['hours'] << 11) | ($d['minutes'] << 5) | ((int) ($d['seconds'] / 2));
    $date = (($d['year'] - 1980) << 9) | ($d['mon'] << 5) | $d['mday'];
    return [$time & 0xffff, $date & 0xffff];
}

/** Минимальный ZIP-writer (метод stored): работает везде без php-zip. Имена — ASCII. */
function inbox_zip_stored(array $entries): string
{
    [$t, $d] = inbox_dostime(time());
    $out = '';
    $central = '';
    $offset = 0;
    foreach ($entries as $e) {
        $name = (string) $e['name'];
        $data = (string) $e['data'];
        $crc = crc32($data);
        $len = strlen($data);
        $nl = strlen($name);
        $local = pack('VvvvvvVVVvv', 0x04034b50, 20, 0x0800, 0, $t, $d, $crc, $len, $len, $nl, 0);
        $out .= $local . $name . $data;
        $central .= pack('VvvvvvvVVVvvvvvVV', 0x02014b50, 20, 20, 0x0800, 0, $t, $d, $crc, $len, $len, $nl, 0, 0, 0, 0, 32, $offset);
        $central .= $name;
        $offset += strlen($local) + $nl + $len;
    }
    $cdLen = strlen($central);
    $count = count($entries);
    $out .= $central;
    $out .= pack('VvvvvVVv', 0x06054b50, 0, 0, $count, $count, $cdLen, $offset, 0);
    return $out;
}

// ---------- валидация пакета ----------

/**
 * Возвращает [title, tech, coords, files] или inbox_json(400) при ошибке.
 * $meta — декодированная мета MapControl, $uploads — нормализованные файлы.
 */
function inbox_validate_package(array $meta): array
{
    $rawId = $meta['submission_id'] ?? null;
    $id = inbox_sanitize_id($rawId);
    if ($id === false) {
        inbox_json(400, ['ok' => false, 'error' => 'invalid_submission_id']);
    }

    $title = trim((string) ($meta['title_operator_final'] ?? $meta['title_original'] ?? $meta['title'] ?? ''));
    if ($title === '' || inbox_len($title) > 200) {
        inbox_json(400, ['ok' => false, 'error' => 'invalid_title']);
    }
    $tech = trim((string) ($meta['techDescription_operator_final'] ?? $meta['techDescription_original'] ?? $meta['techDescription'] ?? ''));
    if ($tech === '' || inbox_len($tech) > 2000) {
        inbox_json(400, ['ok' => false, 'error' => 'invalid_description']);
    }

    $coords = $meta['coords'] ?? null;
    if (!is_array($coords) || count($coords) !== 2
        || !is_numeric($coords[0]) || !is_numeric($coords[1])
        || !is_finite((float) $coords[0]) || !is_finite((float) $coords[1])
    ) {
        inbox_json(400, ['ok' => false, 'error' => 'invalid_coords']);
    }
    $lat = (float) $coords[0];
    $lng = (float) $coords[1];
    if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
        inbox_json(400, ['ok' => false, 'error' => 'invalid_coords']);
    }

    $rawImages = $meta['images'] ?? null;
    if (!is_array($rawImages) || count($rawImages) === 0 || count($rawImages) > INBOX_MAX_FILES) {
        inbox_json(400, ['ok' => false, 'error' => 'invalid_images']);
    }
    $names = [];
    foreach ($rawImages as $item) {
        $fn = is_string($item) ? $item : (is_array($item) ? ($item['filename'] ?? '') : '');
        if (!is_string($fn) || !preg_match('/^[A-Za-z0-9][A-Za-z0-9_.-]*\.webp$/i', $fn) || strlen($fn) > 64) {
            inbox_json(400, ['ok' => false, 'error' => 'invalid_images']);
        }
        $names[] = basename($fn);
    }
    if (count(array_unique($names)) !== count($names)) {
        inbox_json(400, ['ok' => false, 'error' => 'invalid_images']);
    }

    return [$id, $title, $tech, [$lat, $lng], $names];
}

/** Нормализация $_FILES['images'] к списку [name, tmp, size, error]. */
function inbox_normalize_uploads(): array
{
    if (!isset($_FILES['images'])) {
        return [];
    }
    $f = $_FILES['images'];
    $out = [];
    if (is_array($f['name'])) {
        $n = count($f['name']);
        for ($i = 0; $i < $n; $i++) {
            $out[] = [
                'name' => (string) ($f['name'][$i] ?? ''),
                'tmp' => (string) ($f['tmp_name'][$i] ?? ''),
                'size' => (int) ($f['size'][$i] ?? 0),
                'error' => (int) ($f['error'][$i] ?? UPLOAD_ERR_NO_FILE),
            ];
        }
    } else {
        $out[] = [
            'name' => (string) ($f['name'] ?? ''),
            'tmp' => (string) ($f['tmp_name'] ?? ''),
            'size' => (int) ($f['size'] ?? 0),
            'error' => (int) ($f['error'] ?? UPLOAD_ERR_NO_FILE),
        ];
    }
    return $out;
}

// ---------- уведомление (только ссылка, без фото) ----------

function inbox_notify(array $smtpCfg, string $notifyTo, string $id, string $title, array $coords): bool
{
    $notifyTo = trim($notifyTo);
    if ($notifyTo === '') {
        return false;
    }
    $recipients = array_values(array_filter(array_map('trim', explode(',', $notifyTo))));
    if (!$recipients) {
        return false;
    }
    $host = (string) ($_SERVER['HTTP_HOST'] ?? 'zavodsvay.ru');
    $host = (string) preg_replace('/[^A-Za-z0-9.:-]/', '', $host);
    if ($host === '') {
        $host = 'zavodsvay.ru';
    }
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'https';
    $adminUrl = $scheme . '://' . $host . '/inbox/';

    $shortTitle = inbox_len($title) > 120 ? (function_exists('mb_substr') ? mb_substr($title, 0, 120, 'UTF-8') : substr($title, 0, 120)) . '…' : $title;
    $subject = '=?UTF-8?B?' . base64_encode('[Входящие] ' . $shortTitle . ' — ' . $id) . '?=';

    $body = "Новая заявка во входящих MapControl\n\n";
    $body .= "Объект: {$title}\n";
    $body .= "ID:       {$id}\n";
    $body .= 'Координаты: ' . $coords[0] . ', ' . $coords[1] . "\n";
    $body .= 'Время:      ' . date('d.m.Y H:i') . "\n";
    $body .= "\nАдминка: {$adminUrl}\n(откройте ссылку и добавьте токен: ?token=...)\n";
    $body .= "\nФото — только в админке, к письму не приложены.\n";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=utf-8\r\n";
    $headers .= "Content-Transfer-Encoding: 8bit\r\n";

    $sentAny = false;
    if (!empty($smtpCfg['user']) && isset($smtpCfg['pass'])) {
        $smtpHost = (string) ($smtpCfg['host'] ?? 'smtp.mail.ru');
        $smtpFrom = (string) $smtpCfg['user'];
        $fromName = '=?UTF-8?B?' . base64_encode('Входящие zavodsvay.ru') . '?=';
        $hdrs = "From: {$fromName} <{$smtpFrom}>\r\n" . $headers;
        foreach ($recipients as $to) {
            if (inbox_smtp_send($smtpHost, $smtpFrom, (string) $smtpCfg['pass'], $smtpFrom, $to, $subject, $body, $hdrs)) {
                $sentAny = true;
            } else {
                error_log("inbox smtp fail recipient {$to}");
            }
        }
    }
    if (!$sentAny) {
        $sentAny = (bool) @mail($recipients[0], $subject, $body, $headers);
    }
    return $sentAny;
}

// ---------- приём пакета (POST без action) ----------

function inbox_handle_receive(string $expectedToken, array $jsonBody): void
{
    $rawMeta = $_POST['meta'] ?? null;
    if (is_string($rawMeta)) {
        $meta = json_decode($rawMeta, true);
    } elseif (isset($jsonBody['meta']) && is_array($jsonBody['meta'])) {
        $meta = $jsonBody['meta'];
    } elseif (isset($jsonBody['submission_id'])) {
        $meta = $jsonBody;
    } else {
        $meta = null;
    }
    if (!is_array($meta)) {
        inbox_json(400, ['ok' => false, 'error' => 'invalid_meta']);
    }

    [$id, $title, $tech, $coords, $names] = inbox_validate_package($meta);

    $paths = inbox_paths($id);
    if (!inbox_inside($paths['root']) || !inbox_inside($paths['meta'])) {
        inbox_json(400, ['ok' => false, 'error' => 'invalid_submission_id']);
    }
    // Дубль — отклоняем без перезаписи (версионирования нет).
    if (is_dir($paths['root']) || is_file($paths['meta'])) {
        inbox_json(409, ['ok' => false, 'error' => 'duplicate_submission_id']);
    }

    $uploads = inbox_normalize_uploads();
    if (!$uploads) {
        inbox_json(400, ['ok' => false, 'error' => 'missing_files']);
    }
    if (count($uploads) > INBOX_MAX_FILES) {
        inbox_json(400, ['ok' => false, 'error' => 'too_many_files']);
    }
    $upNames = [];
    foreach ($uploads as $u) {
        if ($u['error'] !== UPLOAD_ERR_OK) {
            inbox_json(400, ['ok' => false, 'error' => 'upload_failed']);
        }
        if ($u['size'] <= 0 || $u['size'] > INBOX_MAX_FILE_SIZE) {
            inbox_json(400, ['ok' => false, 'error' => 'file_too_large']);
        }
        $bn = basename($u['name']);
        if (!preg_match('/^[A-Za-z0-9][A-Za-z0-9_.-]*\.webp$/i', $bn)) {
            inbox_json(400, ['ok' => false, 'error' => 'invalid_file_type']);
        }
        // Сигнатура WebP: RIFF....WEBP (без зависимости от fileinfo).
        $head = @file_get_contents($u['tmp'], false, null, 0, 12);
        if (!is_string($head) || strlen($head) < 12 || substr($head, 0, 4) !== 'RIFF' || substr($head, 8, 4) !== 'WEBP') {
            inbox_json(400, ['ok' => false, 'error' => 'invalid_file_type']);
        }
        $upNames[] = $bn;
    }
    // Файлы на диске строго совпадают с метой (состав и имена).
    $a = $names;
    $b = $upNames;
    sort($a);
    sort($b);
    if ($a !== $b) {
        inbox_json(400, ['ok' => false, 'error' => 'files_meta_mismatch']);
    }

    if (!@mkdir($paths['images'], 0777, true) && !is_dir($paths['images'])) {
        inbox_json(500, ['ok' => false, 'error' => 'storage_failed']);
    }
    foreach ($uploads as $u) {
        $dst = $paths['images'] . '/' . basename($u['name']);
        if (!inbox_inside($dst)) {
            inbox_json(400, ['ok' => false, 'error' => 'invalid_file_type']);
        }
        if (!@move_uploaded_file($u['tmp'], $dst)) {
            inbox_json(500, ['ok' => false, 'error' => 'storage_failed']);
        }
    }

    // Мета — как пришла + inbox-статус (статус приёмника: new → approved/rejected).
    $meta['status'] = 'new';
    $meta['inbox_received_at'] = date('c');
    $meta['inbox_ip'] = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
    if (!inbox_write_atomic($paths['meta'], $meta)) {
        inbox_json(500, ['ok' => false, 'error' => 'storage_failed']);
    }

    // Уведомление — best effort: приём уже сохранён, письмо его не отменяет.
    $cfg = inbox_load_config();
    $smtp = inbox_load_smtp();
    $notifySent = inbox_notify($smtp, (string) ($cfg['notify_to'] ?? ''), $id, $title, $coords);

    inbox_json(200, ['ok' => true, 'id' => $id, 'images' => count($names), 'notify_sent' => $notifySent]);
}

// ---------- админка ----------

function inbox_admin_guard(string $expectedToken, array $jsonBody): string
{
    $provided = inbox_provided_token($jsonBody);
    $res = inbox_check_token($provided, $expectedToken);
    if ($res !== 'ok') {
        $code = $res === 'missing' ? 401 : 403;
        if (($res === 'not_configured')) {
            $code = 500;
        }
        $accept = (string) ($_SERVER['HTTP_ACCEPT'] ?? '');
        if (str_contains($accept, 'application/json')) {
            inbox_json($code, ['ok' => false, 'error' => $res === 'ok' ? 'forbidden' : ($res === 'not_configured' ? 'not_configured' : 'forbidden')]);
        }
        http_response_code($code);
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html lang="ru"><head><meta charset="utf-8"><title>Входящие — доступ запрещён</title></head>'
            . '<body><h1>403</h1><p>Нужен токен: откройте /inbox/?token=...</p></body></html>';
        exit;
    }
    return $provided;
}

function inbox_read_meta(string $id): array|null
{
    $paths = inbox_paths($id);
    if (!inbox_inside($paths['meta']) || !is_file($paths['meta'])) {
        return null;
    }
    $raw = @file_get_contents($paths['meta']);
    if (!is_string($raw)) {
        return null;
    }
    $meta = json_decode($raw, true);
    return is_array($meta) ? $meta : null;
}

function inbox_list_all(): array
{
    $base = inbox_base();
    $out = [];
    if (!is_dir($base)) {
        return $out;
    }
    foreach (scandir($base) ?: [] as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }
        if (inbox_sanitize_id($entry) === false) {
            continue;
        }
        $meta = inbox_read_meta($entry);
        if ($meta === null) {
            continue;
        }
        $title = trim((string) ($meta['title_operator_final'] ?? $meta['title_original'] ?? $meta['title'] ?? $entry));
        $out[] = [
            'id' => $entry,
            'title' => $title,
            'status' => (string) ($meta['status'] ?? 'new'),
            'date' => (string) ($meta['inbox_received_at'] ?? $meta['created_at'] ?? $meta['updated_at'] ?? ''),
        ];
    }
    usort($out, static fn($a, $b) => strcmp((string) $b['date'], (string) $a['date']));
    return $out;
}

function inbox_status_label(string $s): string
{
    return match ($s) {
        'new' => 'Новая',
        'approved' => 'Одобрена',
        'rejected' => 'Отклонена',
        default => $s,
    };
}

function inbox_admin_page(string $token, string $action): void
{
    header('Content-Type: text/html; charset=utf-8');
    $q = '?token=' . urlencode($token);
    $css = '<style>body{font-family:system-ui,Arial,sans-serif;max-width:960px;margin:24px auto;padding:0 16px;color:#111}'
        . 'table{border-collapse:collapse;width:100%}td,th{border:1px solid #ccc;padding:6px 8px;text-align:left;font-size:14px}'
        . '.pill{display:inline-block;padding:2px 10px;border-radius:10px;font-size:12px;background:#eee}'
        . '.pill.new{background:#fff3cd}.pill.approved{background:#d4edda}.pill.rejected{background:#f8d7da}'
        . 'img.thumb{max-width:220px;margin:4px;border:1px solid #ccc}pre{background:#f6f8fa;padding:12px;overflow:auto;font-size:12px}'
        . 'form.inline{display:inline}input[type=text]{width:320px}button{margin:4px 4px 4px 0}</style>';

    if ($action === '' || $action === 'list') {
        $rows = inbox_list_all();
        echo '<!DOCTYPE html><html lang="ru"><head><meta charset="utf-8"><title>Входящие — очередь</title>' . $css . '</head><body>';
        echo '<h1>Входящие — очередь (' . count($rows) . ')</h1>';
        if (!$rows) {
            echo '<p>Очередь пуста.</p>';
        } else {
            echo '<table><tr><th>ID</th><th>Дата</th><th>Заголовок</th><th>Статус</th><th></th></tr>';
            foreach ($rows as $r) {
                $id = $r['id'];
                echo '<tr><td>' . h($id) . '</td><td>' . h($r['date']) . '</td><td>' . h($r['title']) . '</td>'
                    . '<td><span class="pill ' . h($r['status']) . '">' . h(inbox_status_label($r['status'])) . '</span></td>'
                    . '<td><a href="' . $q . '&action=view&id=' . urlencode($id) . '">открыть</a>'
                    . ' · <a href="' . $q . '&action=download&id=' . urlencode($id) . '">zip</a></td></tr>';
            }
            echo '</table>';
        }
        echo '</body></html>';
        exit;
    }

    $id = inbox_sanitize_id($_GET['id'] ?? null);
    if ($id === false) {
        http_response_code(400);
        echo '<!DOCTYPE html><html lang="ru"><head><meta charset="utf-8"><title>400</title></head><body><p>Некорректный id.</p></body></html>';
        exit;
    }
    $meta = inbox_read_meta($id);
    if ($meta === null) {
        http_response_code(404);
        echo '<!DOCTYPE html><html lang="ru"><head><meta charset="utf-8"><title>404</title></head><body><p>Заявка не найдена.</p></body></html>';
        exit;
    }

    if ($action === 'meta') {
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $id . '-meta.json"');
        echo json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    if ($action === 'image') {
        $file = basename((string) ($_GET['file'] ?? ''));
        $allowed = [];
        foreach ((array) ($meta['images'] ?? []) as $item) {
            $fn = is_string($item) ? $item : (is_array($item) ? ($item['filename'] ?? '') : '');
            if (is_string($fn) && $fn !== '') {
                $allowed[] = basename($fn);
            }
        }
        if ($file === '' || !in_array($file, $allowed, true)) {
            http_response_code(404);
            exit;
        }
        $path = inbox_paths($id)['images'] . '/' . $file;
        if (!inbox_inside($path) || !is_file($path)) {
            http_response_code(404);
            exit;
        }
        header('Content-Type: image/webp');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }

    if ($action === 'download') {
        $entries = [['name' => 'meta.json', 'data' => json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n"]];
        foreach ((array) ($meta['images'] ?? []) as $item) {
            $fn = is_string($item) ? $item : (is_array($item) ? ($item['filename'] ?? '') : '');
            if (!is_string($fn) || $fn === '') {
                continue;
            }
            $fn = basename($fn);
            $path = inbox_paths($id)['images'] . '/' . $fn;
            if (!inbox_inside($path) || !is_file($path)) {
                continue;
            }
            $entries[] = ['name' => 'images/' . $fn, 'data' => (string) file_get_contents($path)];
        }
        $zip = inbox_zip_stored($entries);
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="inbox-' . $id . '.zip"');
        header('Content-Length: ' . strlen($zip));
        echo $zip;
        exit;
    }

    if ($action === 'view') {
        $status = (string) ($meta['status'] ?? 'new');
        echo '<!DOCTYPE html><html lang="ru"><head><meta charset="utf-8"><title>Входящие — ' . h($id) . '</title>' . $css . '</head><body>';
        echo '<p><a href="' . $q . '">← очередь</a></p>';
        echo '<h1>' . h((string) ($meta['title_operator_final'] ?? $meta['title_original'] ?? $id)) . '</h1>';
        echo '<p>ID: <b>' . h($id) . '</b> · Статус: <span class="pill ' . h($status) . '">' . h(inbox_status_label($status)) . '</span></p>';
        $coords = $meta['coords'] ?? null;
        if (is_array($coords) && count($coords) === 2) {
            echo '<p>Координаты: ' . h((string) $coords[0]) . ', ' . h((string) $coords[1]) . '</p>';
        }
        echo '<p>' . nl2br(h((string) ($meta['techDescription_operator_final'] ?? $meta['techDescription_original'] ?? ''))) . '</p>';
        if (!empty($meta['rejection_reason'])) {
            echo '<p>Причина отклонения: <b>' . h((string) $meta['rejection_reason']) . '</b></p>';
        }
        $imgs = (array) ($meta['images'] ?? []);
        if ($imgs) {
            echo '<h2>Фото (' . count($imgs) . ')</h2>';
            foreach ($imgs as $item) {
                $fn = is_string($item) ? $item : (is_array($item) ? ($item['filename'] ?? '') : '');
                if (!is_string($fn) || $fn === '') {
                    continue;
                }
                $fn = basename($fn);
                $src = $q . '&action=image&id=' . urlencode($id) . '&file=' . urlencode($fn);
                echo '<a href="' . $src . '"><img class="thumb" src="' . $src . '" alt="' . h($fn) . '" loading="lazy"></a>';
            }
        }
        echo '<h2>Решение</h2>';
        echo '<form method="post" action="' . $q . '"><input type="hidden" name="action" value="approve">'
            . '<input type="hidden" name="id" value="' . h($id) . '">'
            . '<button type="submit">Одобрить</button></form> ';
        echo '<form method="post" action="' . $q . '"><input type="hidden" name="action" value="reject">'
            . '<input type="hidden" name="id" value="' . h($id) . '">'
            . '<input type="text" name="reason" placeholder="Причина отклонения (обязательно)" maxlength="500">'
            . '<button type="submit">Отклонить</button></form>';
        echo '<p><a href="' . $q . '&action=download&id=' . urlencode($id) . '">Скачать пакет (zip)</a>'
            . ' · <a href="' . $q . '&action=meta&id=' . urlencode($id) . '">meta.json</a></p>';
        echo '<h2>Мета</h2><pre>' . h((string) json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)) . '</pre>';
        echo '</body></html>';
        exit;
    }

    http_response_code(400);
    echo '<!DOCTYPE html><html lang="ru"><head><meta charset="utf-8"><title>400</title></head><body><p>Неизвестное действие.</p></body></html>';
    exit;
}

/** POST approve/reject из админки. */
function inbox_admin_post(string $token): void
{
    $action = (string) ($_POST['action'] ?? '');
    if ($action !== 'approve' && $action !== 'reject') {
        return; // не админский POST — это приём пакета
    }
    $id = inbox_sanitize_id($_POST['id'] ?? null);
    if ($id === false) {
        inbox_json(400, ['ok' => false, 'error' => 'invalid_submission_id']);
    }
    $paths = inbox_paths($id);
    $meta = inbox_read_meta($id);
    if ($meta === null) {
        inbox_json(404, ['ok' => false, 'error' => 'not_found']);
    }
    if (($meta['status'] ?? 'new') !== 'new') {
        inbox_json(409, ['ok' => false, 'error' => 'already_reviewed']);
    }
    if ($action === 'approve') {
        $meta['status'] = 'approved';
        unset($meta['rejection_reason']);
    } else {
        $reason = trim((string) ($_POST['reason'] ?? ''));
        if ($reason === '' || inbox_len($reason) > 500) {
            inbox_json(400, ['ok' => false, 'error' => 'invalid_reason']);
        }
        $meta['status'] = 'rejected';
        $meta['rejection_reason'] = $reason;
    }
    $meta['inbox_reviewed_at'] = date('c');
    if (!inbox_write_atomic($paths['meta'], $meta)) {
        inbox_json(500, ['ok' => false, 'error' => 'storage_failed']);
    }
    // Браузерная форма — редирект в очередь; API-клиент — JSON.
    $accept = (string) ($_SERVER['HTTP_ACCEPT'] ?? '');
    if (str_contains($accept, 'application/json')) {
        inbox_json(200, ['ok' => true, 'id' => $id, 'status' => $meta['status']]);
    }
    header('Location: /inbox/?token=' . urlencode($token) . '&action=view&id=' . urlencode($id), true, 303);
    exit;
}

// ---------- точка входа ----------

$method = (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET');
if ($method !== 'GET' && $method !== 'POST') {
    inbox_json(405, ['ok' => false, 'error' => 'method_not_allowed']);
}

$jsonBody = [];
if ($method === 'POST' && empty($_POST) && empty($_FILES)) {
    $raw = file_get_contents('php://input');
    if (is_string($raw) && $raw !== '') {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $jsonBody = $decoded;
        }
    }
}

$cfg = inbox_load_config();
$expectedToken = (string) ($cfg['token'] ?? '');

if ($method === 'POST' && isset($_POST['action']) && ((string) $_POST['action']) !== '' && !isset($_POST['meta'])) {
    // Админское решение из формы.
    $token = inbox_admin_guard($expectedToken, $jsonBody);
    inbox_admin_post($token);
}

if ($method === 'POST') {
    $provided = inbox_provided_token($jsonBody);
    $res = inbox_check_token($provided, $expectedToken);
    if ($res !== 'ok') {
        inbox_json($res === 'missing' ? 401 : ($res === 'not_configured' ? 500 : 403), ['ok' => false, 'error' => $res === 'missing' ? 'unauthorized' : ($res === 'not_configured' ? 'not_configured' : 'forbidden')]);
    }
    inbox_handle_receive($expectedToken, $jsonBody);
}

// GET — мини-админка (закрыта тем же токеном в query).
$token = inbox_admin_guard($expectedToken, $jsonBody);
inbox_admin_page($token, (string) ($_GET['action'] ?? 'list'));

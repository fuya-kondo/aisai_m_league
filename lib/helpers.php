<?php

/**
 * 共通ヘルパー関数群
 */

/**
 * 指定キーと値に一致する最初の要素を返す
 *
 * @param array $list
 * @param string $key
 * @param mixed $value
 * @return array|null
 */
function findFirstByKey(array $list, string $key, $value): ?array
{
    foreach ($list as $item) {
        if (isset($item[$key]) && $item[$key] === $value) {
            return $item;
        }
    }
    return null;
}

/**
 * 配列を指定キーで連想配列化して返す
 *
 * @param array $list
 * @param string $key
 * @return array
 */
function indexByKey(array $list, string $key): array
{
    $result = [];
    foreach ($list as $item) {
        if (isset($item[$key])) {
            $result[$item[$key]] = $item;
        }
    }
    return $result;
}

/**
 * HTML escape helper.
 *
 * @param mixed $value
 * @return string
 */
function h($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Ensure session is started for CSRF protection.
 */
function ensureSession(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

/**
 * Get or create CSRF token.
 */
function csrf_token(): string
{
    ensureSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Render hidden CSRF input field.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}

/**
 * Verify CSRF token for POST requests.
 */
function verify_csrf(): bool
{
    ensureSession();
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    return !empty($token) && hash_equals((string)($_SESSION['csrf_token'] ?? ''), (string)$token);
}

/**
 * Debug log (disabled in production unless APP_DEBUG=1).
 */
function debug_log(string $message): void
{
    $debug = getenv('APP_DEBUG') === '1';
    if (function_exists('isProduction') && isProduction() && !$debug) {
        return;
    }
    error_log($message);
}

?>


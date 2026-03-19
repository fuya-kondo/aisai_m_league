<?php
/**
 * ビューとコントローラーから共通利用する軽量ヘルパー関数群。
 * 配列整形・エスケープ・デバッグ出力など、依存を増やさず使える補助処理をまとめる。
 */

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
 * ローカル JS ファイル配列から script タグ群を描画する。
 */
function renderScriptTags(string $baseUrl, array $scriptPaths): void
{
    foreach ($scriptPaths as $scriptPath) {
        echo '<script src="' . h($baseUrl) . '/' . ltrim($scriptPath, '/') . '"></script>' . PHP_EOL;
    }
}

/**
 * 外部 JS URL 配列から script タグ群を描画する。
 */
function renderExternalScriptTags(array $scriptUrls): void
{
    foreach ($scriptUrls as $scriptUrl) {
        echo '<script src="' . h($scriptUrl) . '"></script>' . PHP_EOL;
    }
}

/**
 * Session/CSRF helpers are intentionally disabled in this app.
 * Keep the function signatures so existing views/controllers keep working.
 */
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

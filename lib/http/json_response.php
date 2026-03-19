<?php
/**
 * JSON 応答の形をアプリ全体で統一するための補助関数群。
 * success / message / data / error の4キーを必ず持つレスポンスを返す。
 */

/**
 * 共通 JSON ペイロードを構築する。
 */
function buildJsonPayload(bool $success, string $message = '', $data = null, ?string $error = null): array
{
    return [
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'error' => $error,
    ];
}

/**
 * JSON レスポンスを送信して処理を終了する。
 */
function sendJsonPayload(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit();
}

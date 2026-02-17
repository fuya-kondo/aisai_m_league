<?php

/**
 * 重要操作の監査ログ記録。
 * CloudWatch Logsに転送しやすいJSON形式でerror_logへ出力する。
 */
class AuditLogger
{
    public static function log(string $operation, string $resourceType, $resourceId, string $result, array $detail = []): void
    {
        $record = [
            'event_type' => 'audit',
            'operation' => $operation,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'result' => $result,
            'timestamp' => gmdate('c'),
            'request_id' => $_SERVER['UNIQUE_ID'] ?? '',
            'actor_role' => self::extractActorRole(),
            'actor_sub' => self::extractActorSub(),
            'source_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'detail' => $detail,
        ];

        error_log('[AUDIT] ' . json_encode($record, JSON_UNESCAPED_UNICODE));
    }

    private static function extractActorRole(): string
    {
        $claims = $_SERVER['jwt_claims'] ?? null;
        if (is_array($claims) && !empty($claims['role'])) {
            return (string)$claims['role'];
        }
        return 'unknown';
    }

    private static function extractActorSub(): string
    {
        $claims = $_SERVER['jwt_claims'] ?? null;
        if (is_array($claims) && !empty($claims['sub'])) {
            return (string)$claims['sub'];
        }
        return 'unknown';
    }
}

<?php

/**
 * 認証/認可関連のセキュリティ制御。
 */
class AuthSecurity
{
    /**
     * ログイン試行の簡易スロットリング判定。
     */
    public static function checkLoginThrottle(string $ipAddress): array
    {
        $maxAttempts = (int)(getenv('LOGIN_MAX_ATTEMPTS') ?: 5);
        $windowSeconds = (int)(getenv('LOGIN_WINDOW_SECONDS') ?: 300);
        $storeFile = sys_get_temp_dir() . '/aisai_login_throttle.json';

        $records = [];
        if (file_exists($storeFile)) {
            $records = json_decode((string)file_get_contents($storeFile), true) ?: [];
        }

        $now = time();
        $ipRecords = $records[$ipAddress] ?? [];
        $ipRecords = array_values(array_filter($ipRecords, function ($timestamp) use ($now, $windowSeconds) {
            return ($now - (int)$timestamp) <= $windowSeconds;
        }));

        if (count($ipRecords) >= $maxAttempts) {
            $retryAfter = $windowSeconds - ($now - (int)$ipRecords[0]);
            return ['allowed' => false, 'retry_after' => max(1, $retryAfter)];
        }

        return ['allowed' => true, 'records' => $records, 'ip_records' => $ipRecords, 'store_file' => $storeFile];
    }

    /**
     * ログイン失敗を記録。
     */
    public static function recordLoginFailure(string $ipAddress): void
    {
        $result = self::checkLoginThrottle($ipAddress);
        $records = $result['records'] ?? [];
        $ipRecords = $result['ip_records'] ?? [];
        $storeFile = $result['store_file'] ?? (sys_get_temp_dir() . '/aisai_login_throttle.json');

        $ipRecords[] = time();
        $records[$ipAddress] = $ipRecords;
        file_put_contents($storeFile, json_encode($records, JSON_UNESCAPED_UNICODE), LOCK_EX);
    }

    /**
     * ログイン成功時に試行履歴をクリア。
     */
    public static function clearLoginFailures(string $ipAddress): void
    {
        $storeFile = sys_get_temp_dir() . '/aisai_login_throttle.json';
        if (!file_exists($storeFile)) {
            return;
        }

        $records = json_decode((string)file_get_contents($storeFile), true) ?: [];
        unset($records[$ipAddress]);
        file_put_contents($storeFile, json_encode($records, JSON_UNESCAPED_UNICODE), LOCK_EX);
    }
}

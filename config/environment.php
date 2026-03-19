<?php
/**
 * 環境判定とベース URL 解決を担う設定ファイル。
 * ホスト名とスクリプト配置から、ローカル/本番の違いを吸収して各画面で使う URL を組み立てる。
 */

/**
 * .env ファイルを読み込み、環境変数へ反映します。
 * 既に設定済みの環境変数は上書きしません。
 */
function loadEnvFile(string $envPath): void
{
    if (!is_file($envPath) || !is_readable($envPath)) {
        return;
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        $separatorPos = strpos($line, '=');
        if ($separatorPos === false) {
            continue;
        }

        $name = trim(substr($line, 0, $separatorPos));
        $value = trim(substr($line, $separatorPos + 1));

        if ($name === '') {
            continue;
        }

        if (
            array_key_exists($name, $_ENV) ||
            array_key_exists($name, $_SERVER) ||
            getenv($name) !== false
        ) {
            continue;
        }

        if (
            (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        putenv($name . '=' . $value);
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

loadEnvFile(__DIR__ . '/.env');

// 環境判定
function isProduction() {
    // 本番環境の判定条件
    $productionHosts = [
        'aisai-m-league.com',
        'www.aisai-m-league.com'
    ];
    
    $currentHost = $_SERVER['HTTP_HOST'] ?? '';
    
    // ローカル開発環境の判定条件
    $localHosts = [
        'localhost',
        '127.0.0.1',
        '::1'
    ];
    
    // ローカル環境かどうかを先に判定
    foreach ($localHosts as $host) {
        if (strpos($currentHost, $host) !== false) {
            return false;
        }
    }
    
    // 本番環境のドメインかどうかを判定
    foreach ($productionHosts as $host) {
        if (strpos($currentHost, $host) !== false) {
            return true;
        }
    }
    
    // デフォルトはローカル開発環境
    return false;
}

// ローカル環境でのベースパスを取得
function getLocalBasePath() {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    
    // スクリプト名からベースパスを抽出
    if (preg_match('/\/([^\/]+)\/index\.php$/', $scriptName, $matches)) {
        return '/' . $matches[1];
    }
    
    // ドキュメントルートからの相対パスを計算
    $currentDir = dirname($_SERVER['SCRIPT_NAME']);
    if ($currentDir === '/') {
        return '';
    }
    
    return $currentDir;
}

// 環境に応じたベースURLを取得
function getBaseUrl() {
    if (isProduction()) {
        return 'https://aisai-m-league.com';
    } else {
        // ローカル開発環境
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $basePath = getLocalBasePath();
        
        return $protocol . '://' . $host . $basePath;
    }
}

// 環境に応じたアセットパスを取得
function getAssetPath($path) {
    $baseUrl = getBaseUrl();
    return $baseUrl . $path;
}

// 環境情報を取得
function getEnvironmentInfo() {
    return [
        'is_production' => isProduction(),
        'base_url' => getBaseUrl(),
        'host' => $_SERVER['HTTP_HOST'] ?? 'unknown',
        'protocol' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http',
        'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'unknown',
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'unknown',
        'local_base_path' => getLocalBasePath()
    ];
}

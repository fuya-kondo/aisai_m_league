<?php
/**
 * 環境設定ファイル
 * 開発環境と本番環境を自動判定
 */

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

<?php
namespace App\Support;

/**
 * 実行環境依存の URL / パス解決を集約する設定ヘルパー。
 * view や router が $_SERVER を直接読む量を減らし、共通規則を一か所に固定する。
 */
final class AppConfig
{
    public static function isProduction(): bool
    {
        return function_exists('isProduction') ? isProduction() : false;
    }

    public static function baseUrl(): string
    {
        $resolvedBaseUrl = function_exists('getBaseUrl') ? getBaseUrl() : '';
        return rtrim((string)$resolvedBaseUrl, '/');
    }

    public static function basePath(): string
    {
        $basePath = parse_url(self::baseUrl(), PHP_URL_PATH) ?? '';
        return self::normalizePath((string)$basePath);
    }

    public static function requestPath(): string
    {
        $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
        return self::normalizePath((string)$requestPath);
    }

    public static function relativeRequestPath(): string
    {
        $requestPath = self::requestPath();
        $basePath = self::basePath();

        if ($basePath !== '/' && str_starts_with($requestPath, $basePath)) {
            $relativePath = substr($requestPath, strlen($basePath));
            return self::normalizePath($relativePath === false ? '/' : $relativePath);
        }

        return $requestPath;
    }

    public static function assetUrl(string $path): string
    {
        $normalizedPath = '/' . ltrim($path, '/');
        return self::baseUrl() . $normalizedPath;
    }

    public static function normalizePath(string $path): string
    {
        $trimmedPath = trim($path);
        if ($trimmedPath === '') {
            return '/';
        }

        $withoutIndex = preg_replace('#/index\.php$#', '', $trimmedPath) ?? $trimmedPath;
        $normalizedPath = rtrim($withoutIndex, '/');

        return $normalizedPath === '' ? '/' : $normalizedPath;
    }
}

<?php
namespace App\Http;

/**
 * 低レベルな HTTP 応答をまとめる最小レスポンス helper。
 * router からの redirect / 404 / 500 を一か所で扱う。
 */
final class Response
{
    public static function redirect(string $location): void
    {
        header('Location: ' . $location);
        exit();
    }

    public static function notFound(): void
    {
        http_response_code(404);
        echo 'Not Found';
    }

    public static function internalServerError(): void
    {
        http_response_code(500);
        echo 'Internal Server Error';
    }
}

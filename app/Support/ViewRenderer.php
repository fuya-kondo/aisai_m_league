<?php
namespace App\Support;

/**
 * 全 view に共有する描画コンテキストを注入する薄い renderer。
 * 共通 URL や request 情報をここでまとめ、view から bootstrap 直読込をなくす。
 */
final class ViewRenderer
{
    public static function render(string $viewPath, array $variables = []): void
    {
        $sharedVariables = [
            'baseUrl' => AppConfig::baseUrl(),
            'basePath' => AppConfig::basePath(),
            'requestPath' => AppConfig::requestPath(),
            'relativeRequestPath' => AppConfig::relativeRequestPath(),
        ];

        extract($sharedVariables + $variables, EXTR_SKIP);
        include $viewPath;
    }
}

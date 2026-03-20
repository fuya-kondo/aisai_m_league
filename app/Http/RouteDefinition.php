<?php
namespace App\Http;

/**
 * 1ルート分の path と dispatch 先を表す値オブジェクト。
 */
final class RouteDefinition
{
    public function __construct(
        public readonly string $path,
        public readonly string $controller,
        public readonly string $action
    ) {
    }
}

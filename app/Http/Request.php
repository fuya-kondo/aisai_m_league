<?php
namespace App\Http;

use App\Support\AppConfig;

/**
 * superglobal から入力値をまとめて扱う request オブジェクト。
 * router / controller が生の $_GET, $_POST, $_SERVER へ直接依存しない入口を用意する。
 */
final class Request
{
    public function __construct(
        private readonly array $query,
        private readonly array $post,
        private readonly array $server
    ) {
    }

    public static function capture(): self
    {
        return new self($_GET, $_POST, $_SERVER);
    }

    public function method(): string
    {
        return strtoupper((string)($this->server['REQUEST_METHOD'] ?? 'GET'));
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    public function allQuery(): array
    {
        return $this->query;
    }

    public function allPost(): array
    {
        return $this->post;
    }

    public function path(): string
    {
        return AppConfig::relativeRequestPath();
    }

    public function controller(): string
    {
        return (string)($this->query('controller', 'main'));
    }

    public function action(): string
    {
        return (string)($this->post('action', $this->query('action', 'top')));
    }
}

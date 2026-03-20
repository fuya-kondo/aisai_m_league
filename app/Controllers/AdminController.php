<?php
namespace App\Controllers;

/**
 * 新 app レイヤーの admin controller アダプタ。
 * URL と router は app 配下へ移しつつ、既存 CRUD 実装を段階的に置換できるようにする。
 */
final class AdminController
{
    private \AdminController $legacyController;

    public function __construct()
    {
        $this->legacyController = new \AdminController();
    }

    public function __call(string $name, array $arguments): mixed
    {
        return $this->legacyController->{$name}(...$arguments);
    }
}

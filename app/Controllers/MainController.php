<?php
namespace App\Controllers;

/**
 * 新 app レイヤーの main controller アダプタ。
 * 現時点では既存 MainController を内部利用し、入口の責務だけ app 配下へ移す。
 */
final class MainController
{
    private \MainController $legacyController;

    public function __construct()
    {
        $this->legacyController = new \MainController();
    }

    public function __call(string $name, array $arguments): mixed
    {
        return $this->legacyController->{$name}(...$arguments);
    }
}

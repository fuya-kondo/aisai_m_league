<?php
/**
 * アプリケーションのフロントコントローラ。
 * app 配下の bootstrap と router を読み込み、全リクエストを新 runtime へ委譲する。
 */
require_once __DIR__ . '/app/bootstrap.php';

$router = new \App\Http\Router();
$router->dispatch(\App\Http\Request::capture());

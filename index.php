<?php
require_once __DIR__ . '/config/import_file.php';

// ルーターを使用してリクエストを処理
$router = new Router();
$router->handleRequest();

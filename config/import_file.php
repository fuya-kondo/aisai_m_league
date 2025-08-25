<?php

// 環境設定
require_once __DIR__ . '/environment.php';

// 必要なファイルの読み込みのみ
// 設定類
require_once __DIR__ . '/db_connect.php';
use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/stats_column.php';
require_once __DIR__ . '/../vendor/autoload.php';

// モデルとサービス類
require_once __DIR__ . '/../model/m_badge.php';
require_once __DIR__ . '/../model/m_direction.php';
require_once __DIR__ . '/../model/m_game_day.php';
require_once __DIR__ . '/../model/m_group.php';
require_once __DIR__ . '/../model/m_rule.php';
require_once __DIR__ . '/../model/m_setting.php';
require_once __DIR__ . '/../model/m_tier.php';
require_once __DIR__ . '/../model/m_title.php';
require_once __DIR__ . '/../model/u_game_history.php';
require_once __DIR__ . '/../model/u_table.php';
require_once __DIR__ . '/../model/u_tier_history.php';
require_once __DIR__ . '/../model/u_title.php';
require_once __DIR__ . '/../model/u_user.php';

// サービス・コントローラ定義
require_once __DIR__ . '/../controller/base_controller.php';
require_once __DIR__ . '/../controller/admin/admin_controller.php';
require_once __DIR__ . '/../controller/main/stats_service.php';
require_once __DIR__ . '/../controller/main/main_controller.php';
require_once __DIR__ . '/../controller/router.php';
<?php

// 必要なファイルの読み込み
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/google_gemini.php';
require_once __DIR__ . '/../model/u_user.php';
require_once __DIR__ . '/../model/m_badge.php';
require_once __DIR__ . '/../model/m_direction.php';
require_once __DIR__ . '/../model/m_game_day.php';
require_once __DIR__ . '/../model/m_rule.php';
require_once __DIR__ . '/../model/m_group.php';
require_once __DIR__ . '/../model/m_tier.php';
require_once __DIR__ . '/../model/m_title.php';
require_once __DIR__ . '/../model/m_setting.php';
require_once __DIR__ . '/../model/u_game_history.php';
require_once __DIR__ . '/../model/u_table.php';
require_once __DIR__ . '/../model/u_tier_history.php';
require_once __DIR__ . '/../model/u_title.php';
require_once __DIR__ . '/../controller/stats_service.php';
require_once __DIR__ . '/../controller/main_controller.php';
require_once __DIR__ . '/stats_column_config.php';
require_once __DIR__ . '/../vendor/autoload.php';
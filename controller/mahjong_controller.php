
<?php
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/mahjong_stats_service.php';
require_once __DIR__ . '/../model/m_direction.php';
require_once __DIR__ . '/../model/m_game_day.php';
require_once __DIR__ . '/../model/m_group.php';
require_once __DIR__ . '/../model/m_rule.php';
require_once __DIR__ . '/../model/u_history.php';
require_once __DIR__ . '/../model/u_table.php';
require_once __DIR__ . '/../model/u_user.php';

// 定数の定義
const GROUP_A_USER_IDS   = [1, 2, 3, 4];  // グループAに属するユーザーID
const YEARS_TO_CALCULATE = [2025, 2024, 2023, 2022, '全期間'];  // 計算対象年度

// クラスのインスタンス化
// $mahjongStats = new MahjongStatsService( $u_mahjong_user_result, $u_mahjong_history_result, GROUP_A_USER_IDS, YEARS_TO_CALCULATE );
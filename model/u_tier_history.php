<?php

/**
 * ティア履歴（u_user_tier_history）一覧を取得します。
 */

// クエリ定義
$uTierHistorySql = 'SELECT * FROM `u_tier_history`';

try {
    // DB接続
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    // 問い合わせ実行
    $uTierHistoryStatement = $pdo->query($uTierHistorySql);
    $uTierHistoryList = $uTierHistoryStatement->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

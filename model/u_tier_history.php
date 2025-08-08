<?php

// ----------------------------------------------------------------------
// データ取得
// ----------------------------------------------------------------------
$uTierHistorySql = 'SELECT * FROM `u_tier_history`';

try {
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    $uTierHistorySth = $pdo->query($uTierHistorySql);
    $uTierHistoryList = $uTierHistorySth->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

<?php

/**
 * ティア（m_tier）一覧を取得し、IDをキーに整形します。
 */

// クエリ定義
$mTierSql = 'SELECT * FROM `m_tier`';

try {
    // DB接続
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    // 問い合わせ実行
    $mTierStatement = $pdo->query($mTierSql);
    $mTierList = $mTierStatement->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

// IDをキーに整形
$tierRows = $mTierList;
$mTierList = [];
foreach ($tierRows as $tierRow) {
    $mTierList[$tierRow['m_tier_id']] = $tierRow;
}
<?php

/**
 * 称号（m_badge）を取得し、IDをキーにした連想配列へ整形します。
 */

// クエリ定義
$mBadgeSql = 'SELECT * FROM `m_badge`';

try {
    // DB接続
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    // 問い合わせ実行
    $mBadgeStatement = $pdo->query($mBadgeSql);
    $mBadgeList = $mBadgeStatement->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

// IDをキーにして再構成
$badgeRows = $mBadgeList;
$mBadgeList = [];
foreach ($badgeRows as $badgeRow) {
    $mBadgeList[$badgeRow['m_badge_id']] = $badgeRow;
}
<?php

// ----------------------------------------------------------------------
// データ取得
// ----------------------------------------------------------------------
$mBadgeSql = 'SELECT * FROM `m_badge`';

try {
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    $mBadgeSth = $pdo->query($mBadgeSql);
    $mBadgeList = $mBadgeSth->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

$tmp = $mBadgeList;
$mBadgeList = [];
foreach ($tmp as $key => $value) {
    $mBadgeList[$value['m_badge_id']] = $value;
}
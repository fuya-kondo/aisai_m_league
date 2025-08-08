<?php

// ----------------------------------------------------------------------
// データ取得
// ----------------------------------------------------------------------
$mTierSql = 'SELECT * FROM `m_tier`';

try {
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    $mTierSth = $pdo->query($mTierSql);
    $mTierList = $mTierSth->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

$tmp = $mTierList;
$mTierList = [];
foreach ($tmp as $key => $value) {
    $mTierList[$value['m_tier_id']] = $value;
}
<?php

// ----------------------------------------------------------------------
// データ取得
// ----------------------------------------------------------------------
$mTitleSql = 'SELECT * FROM `m_title`;';

try {
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    $mTitleSth = $pdo->query($mTitleSql);
    $mTitleList = $mTitleSth->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

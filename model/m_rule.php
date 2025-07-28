<?php

// ----------------------------------------------------------------------
// データ取得
// ----------------------------------------------------------------------
$mRuleSql = 'SELECT * FROM `m_rule`;';

try {
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    $mRuleSth = $pdo->query($mRuleSql);
    $mRuleList = $mRuleSth->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

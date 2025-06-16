<?php

// ----------------------------------------------------------------------
// データ取得
// ----------------------------------------------------------------------
$mRuleSql = 'SELECT * FROM `m_rule`;';

try {
    $db = Database::getInstance($dsn, $dbUser, $dbPass)->getConnection();
    $mRuleSth = $db->query($mRuleSql);
    $mRuleList = $mRuleSth->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

?>
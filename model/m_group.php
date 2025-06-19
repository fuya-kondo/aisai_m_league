<?php

// ----------------------------------------------------------------------
// データ取得
// ----------------------------------------------------------------------
$mGroupSql = 'SELECT * FROM `m_group`;';

try {
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    $mGroupSth = $pdo->query($mGroupSql);
    $mGroupList = $mGroupSth->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

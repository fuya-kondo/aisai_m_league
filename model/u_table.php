<?php

// ----------------------------------------------------------------------
// データ取得
// ----------------------------------------------------------------------
$uTableSql = 'SELECT * FROM `u_table`;';

try {
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    $uTableSth = $pdo->query($uTableSql);
    $uTableList = $uTableSth->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

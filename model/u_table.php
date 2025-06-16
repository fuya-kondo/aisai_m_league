<?php

// ----------------------------------------------------------------------
// データ取得
// ----------------------------------------------------------------------
$uTableSql = 'SELECT * FROM `u_table`;';

try {
    $db = Database::getInstance($dsn, $dbUser, $dbPass)->getConnection();
    $uTableSth = $db->query($uTableSql);
    $uTableList = $uTableSth->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

?>
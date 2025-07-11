<?php

// ----------------------------------------------------------------------
// データ取得
// ----------------------------------------------------------------------
$uTitleSql = 'SELECT * FROM `u_title`;';

try {
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    $uTitleSth = $pdo->query($uTitleSql);
    $uTitleList = $uTitleSth->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

?>

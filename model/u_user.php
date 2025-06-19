<?php

// ----------------------------------------------------------------------
// データ取得
// ----------------------------------------------------------------------
$uUserSql = 'SELECT * FROM `u_user`;';

try {
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    $uUserSth = $pdo->query($uUserSql);
    $uUserList = $uUserSth->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

?>

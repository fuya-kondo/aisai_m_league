<?php

// ----------------------------------------------------------------------
// データ取得
// ----------------------------------------------------------------------
$uUserSql = 'SELECT * FROM `u_user`;';

try {
    $db = Database::getInstance($dsn, $dbUser, $dbPass)->getConnection();
    $uUserSth = $db->query($uUserSql);
    $uUserList = $uUserSth->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

?>

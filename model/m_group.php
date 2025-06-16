<?php

// ----------------------------------------------------------------------
// データ取得
// ----------------------------------------------------------------------
$mGroupSql = 'SELECT * FROM `m_group`;';

try {
    $db = Database::getInstance($dsn, $dbUser, $dbPass)->getConnection();
    $mGroupSth = $db->query($mGroupSql);
    $mGroupList = $mGroupSth->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

?>
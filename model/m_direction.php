<?php

// ----------------------------------------------------------------------
// データ取得
// ----------------------------------------------------------------------
$mDirectionSql = 'SELECT * FROM `m_direction`;';

try {
    $db = Database::getInstance($dsn, $dbUser, $dbPass)->getConnection();
    $mDirectionSth = $db->query($mDirectionSql);
    $mDirectionList = $mDirectionSth->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

$output = [];
foreach ($mDirectionList as $item) {
    $output[$item["m_direction_id"]] = $item["name"];
}
$mDirectionList = $output;

?>
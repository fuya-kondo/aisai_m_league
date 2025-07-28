<?php

// ----------------------------------------------------------------------
// データ取得
// ----------------------------------------------------------------------
$mDirectionSql = 'SELECT * FROM `m_direction`;';

try {
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    $mDirectionSth = $pdo->query($mDirectionSql);
    $mDirectionList = $mDirectionSth->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

$output = [];
foreach ($mDirectionList as $item) {
    $output[$item["m_direction_id"]] = $item["name"];
}
$mDirectionList = $output;

<?php

// ----------------------------------------------------------------------
// データ取得
// ----------------------------------------------------------------------
$mGameDaySql = 'SELECT * FROM `m_game_day`';

try {
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    $mGameDaySth = $pdo->query($mGameDaySql);
    $mGameDayList = $mGameDaySth->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

?>
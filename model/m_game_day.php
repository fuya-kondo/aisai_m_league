<?php

// ----------------------------------------------------------------------
// データ取得
// ----------------------------------------------------------------------
$mGameDaySql = 'SELECT * FROM `m_game_day`';

try {
    $db = Database::getInstance($dsn, $dbUser, $dbPass)->getConnection();
    $mGameDaySth = $db->query($mGameDaySql);
    $mGameDayList = $mGameDaySth->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

?>
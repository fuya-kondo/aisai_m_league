<?php

/**
 * 試合日（m_game_day）テーブルからデータを取得して配列に格納します。
 */

// クエリ定義
$mGameDaySql = 'SELECT * FROM `m_game_day`';

try {
    // DB接続
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    // 問い合わせ実行
    $mGameDayStatement = $pdo->query($mGameDaySql);
    $mGameDayList = $mGameDayStatement->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

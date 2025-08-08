<?php

/**
 * 方位（m_direction）を取得し、ID=>名称 の配列へ整形します。
 */

// クエリ定義
$mDirectionSql = 'SELECT * FROM `m_direction`;';

try {
    // DB接続
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    // 問い合わせ実行
    $mDirectionStatement = $pdo->query($mDirectionSql);
    $mDirectionList = $mDirectionStatement->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

// 表示用に ID=>name 形式へ変換
$directionNameById = [];
foreach ($mDirectionList as $directionRow) {
    $directionNameById[$directionRow['m_direction_id']] = $directionRow['name'];
}
$mDirectionList = $directionNameById;

<?php

/**
 * 個人タイトル（u_title）一覧を取得します。
 */

// クエリ定義
$uTitleSql = 'SELECT * FROM `u_title`;';

try {
    // DB接続
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    // 問い合わせ実行
    $uTitleStatement = $pdo->query($uTitleSql);
    $uTitleList = $uTitleStatement->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

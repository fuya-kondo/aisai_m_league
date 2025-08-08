<?php

/**
 * 卓（u_table）一覧を取得します。
 */

// クエリ定義
$uTableSql = 'SELECT * FROM `u_table`;';

try {
    // DB接続
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    // 問い合わせ実行
    $uTableStatement = $pdo->query($uTableSql);
    $uTableList = $uTableStatement->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

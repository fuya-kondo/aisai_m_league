<?php

/**
 * タイトル（m_title）一覧を取得します。
 */

// クエリ定義
$mTitleSql = 'SELECT * FROM `m_title`;';

try {
    // DB接続
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    // 問い合わせ実行
    $mTitleStatement = $pdo->query($mTitleSql);
    $mTitleList = $mTitleStatement->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

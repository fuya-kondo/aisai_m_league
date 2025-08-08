<?php

/**
 * グループ（m_group）一覧を取得します。
 */

// クエリ定義
$mGroupSql = 'SELECT * FROM `m_group`;';

try {
    // DB接続
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    // 問い合わせ実行
    $mGroupStatement = $pdo->query($mGroupSql);
    $mGroupList = $mGroupStatement->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

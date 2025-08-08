<?php

/**
 * ルール（m_rule）一覧を取得します。
 */

// クエリ定義
$mRuleSql = 'SELECT * FROM `m_rule`;';

try {
    // DB接続
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    // 問い合わせ実行
    $mRuleStatement = $pdo->query($mRuleSql);
    $mRuleList = $mRuleStatement->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

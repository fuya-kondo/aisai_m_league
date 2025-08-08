<?php

// ----------------------------------------------------------------------
// データ取得
// ----------------------------------------------------------------------

// クエリ定義
$uUserSql = 'SELECT * FROM `u_user`;';

try {
    // DB接続
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    // 問い合わせ実行
    $uUserStatement = $pdo->query($uUserSql);
    $uUserList = $uUserStatement->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

function updateUserBadge( $userId, $badgeId ) {
    try {
        $dbConfig = getDatabaseConfig();
        $db = Database::getInstance($dbConfig);
        $pdo = $db->getConnection();
        $sql = 'UPDATE `u_user` SET `m_badge_id` = :m_badge_id WHERE `u_user_id` = :u_user_id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':m_badge_id', $badgeId);
        $stmt->bindParam(':u_user_id', $userId);

        return $stmt->execute();
    } catch (Exception $e) {
        error_log('データ更新エラー: ' . $e->getMessage());
        return false;
    }
}
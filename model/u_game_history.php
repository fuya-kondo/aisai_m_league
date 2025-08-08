<?php

// ----------------------------------------------------------------------
// データ取得
// ----------------------------------------------------------------------

// クエリ定義
$uGameHistorySql = 'SELECT * FROM `u_game_history` WHERE `del_flag` = 0 ORDER BY `u_user_id`, `u_game_history_id` DESC';

try {
    // DB接続
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    // 問い合わせ実行
    $stmt = $pdo->query($uGameHistorySql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $uGameHistoryList = [];
    foreach ($rows as $row) {
        $userId = $row['u_user_id'];
        if (!isset($uGameHistoryList[$userId])) {
            $uGameHistoryList[$userId] = [];
        }
        $uGameHistoryList[$userId][] = $row;
    }
} catch (Exception $e) {
    exit($e->getMessage());
}

// ----------------------------------------------------------------------
// データ操作関数群
// ----------------------------------------------------------------------

/**
 * 成績レコードを追加します。
 */
function addData(int $userId, int $tableId, int $game, int $direction, string $rank, int $score, string $playDate): bool {
    try {
        $dbConfig = getDatabaseConfig();
        $db = Database::getInstance($dbConfig);
        $pdo = $db->getConnection();
        $sql = 'INSERT INTO `u_game_history` (u_user_id, u_table_id, game, m_direction_id, rank, score, point, play_date, reg_date) VALUES (:userId, :tableId, :game, :direction, :rank, :score, :point, :playDate, NOW())';
        $stmt = $pdo->prepare($sql);
        $point = calculatePoint($rank, $score);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':tableId', $tableId, PDO::PARAM_INT);
        $stmt->bindParam(':game', $game, PDO::PARAM_INT);
        $stmt->bindParam(':direction', $direction, PDO::PARAM_INT);
        $stmt->bindParam(':rank', $rank);
        $stmt->bindParam(':score', $score, PDO::PARAM_INT);
        $stmt->bindParam(':point', $point);
        $stmt->bindParam(':playDate', $playDate);
        return $stmt->execute();
    } catch (Exception $e) {
        error_log('データ追加エラー: ' . $e->getMessage()); // エラーログ出力
        return false;
    }
}

/**
 * 成績レコードを更新します。
 */
function updateData(int $historyId, string $rank, int $score, int $game, int $direction): bool {
    try {
        $dbConfig = getDatabaseConfig();
        $db = Database::getInstance($dbConfig);
        $pdo = $db->getConnection();
        $sql = 'UPDATE `u_game_history` SET `rank` = :rank, `score` = :score, `game` = :game, `m_direction_id` = :direction, `point` = :point WHERE `u_game_history_id` = :u_game_history_id';
        $stmt = $pdo->prepare($sql);
        $point = calculatePoint($rank, $score);
        $stmt->bindParam(':rank', $rank);
        $stmt->bindParam(':score', $score, PDO::PARAM_INT);
        $stmt->bindParam(':game', $game, PDO::PARAM_INT);
        $stmt->bindParam(':direction', $direction, PDO::PARAM_INT);
        $stmt->bindParam(':point', $point);
        $stmt->bindParam(':u_game_history_id', $historyId, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (Exception $e) {
        error_log('データ更新エラー: ' . $e->getMessage());
        return false;
    }
}

/**
 * 成績レコードを論理削除します（del_flag=1）。
 */
function deleteData(int $historyId): bool {
    try {
        $dbConfig = getDatabaseConfig();
        $db = Database::getInstance($dbConfig);
        $pdo = $db->getConnection();
        $sql = 'UPDATE `u_game_history` SET `del_flag` = 1 WHERE `u_game_history_id` = :u_game_history_id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':u_game_history_id', $historyId, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (Exception $e) {
        error_log('データ削除エラー: ' . $e->getMessage());
        return false;
    }
}

/**
 * ルールに基づいたポイント計算を行います。
 */
function calculatePoint($rank, int $score) {
    $point = 0;
    switch ($rank) {
        case 1:
            $point = ($score - 30000) / 1000 + 50;
            break;
        case '1=1':
            $point = ($score - 30000) / 1000 + 30;
            break;
        case 2:
            $point = ($score - 30000) / 1000 + 10;
            break;
        case '2=2':
            $point = ($score - 30000) / 1000;
            break;
        case 3:
            $point = ($score - 30000) / 1000 - 10;
            break;
        case '3=3':
            $point = ($score - 30000) / 1000 - 20;
            break;
        case 4:
            $point = ($score - 30000) / 1000 - 30;
            break;
    }
    return $point;
}
?>
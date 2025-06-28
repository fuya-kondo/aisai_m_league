<?php

// ----------------------------------------------------------------------
// データ取得
// ----------------------------------------------------------------------

try {
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    foreach($uUserList as $user) {
        $uGameHistorySql = 'SELECT * FROM u_game_history WHERE u_user_id = '.$user['u_user_id'].' AND del_flag = 0 ORDER BY `u_game_history`.`u_game_history_id` DESC;';
        $uGameHistorySth = $pdo->query($uGameHistorySql);
        $uGameHistoryList[$user['u_user_id']] = $uGameHistorySth->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    exit($e->getMessage());
}

// ----------------------------------------------------------------------
// データ操作関数群
// ----------------------------------------------------------------------

/**
 * データを追加
 *
 * @param int $userId
 * @param int $tableId
 * @param int $game
 * @param int $direction
 * @param int|string $rank
 * @param int $score
 * @param string $playDate YYYY-MM-DD形式
 * @return bool
 */
function addData(int $userId, int $tableId, int $game, int $direction, string $rank, int $score, string $playDate): bool {
    try {
        $dbConfig = getDatabaseConfig();
        $db = Database::getInstance($dbConfig);
        $pdo = $db->getConnection();
        $sql = 'INSERT INTO `u_game_history` (u_user_id, u_table_id, game, m_direction_id, rank, score, point, play_date, reg_date) VALUES (:userId, :tableId, :game, :direction, :rank, :score, :point, :playDate, NOW())';
        $stmt = $pdo->prepare($sql);
        $point = _calculationPoint($rank, $score);
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
 * データを更新
 *
 * @param int $historyId
 * @param int|string $rank
 * @param int $score
 * @param int $game
 * @param int $direction
 * @return bool
 */
function updateData(int $historyId, string $rank, int $score, int $game, int $direction): bool {
    try {
        $dbConfig = getDatabaseConfig();
        $db = Database::getInstance($dbConfig);
        $pdo = $db->getConnection();
        $sql = 'UPDATE `u_game_history` SET `rank` = :rank, `score` = :score, `game` = :game, `m_direction_id` = :direction, `point` = :point WHERE `u_game_history_id` = :u_game_history_id';
        $stmt = $pdo->prepare($sql);
        $point = _calculationPoint($rank, $score);
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
 * データを削除（del_flagを1にする）
 *
 * @param int $historyId
 * @return bool
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
 * ポイント計算
 *
 * @param int|string $rank
 * @param int $score
 */
function _calculationPoint($rank, int $score) {
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
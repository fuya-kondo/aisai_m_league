<?php

// ----------------------------------------------------------------------
// データ取得
// ----------------------------------------------------------------------

try {
    $db = Database::getInstance($dsn, $dbUser, $dbPass)->getConnection();
    foreach($u_mahjong_user_result as $user) {
        $u_mahjong_history_sql = 'SELECT * FROM u_mahjong_history WHERE u_mahjong_user_id = '.$user['u_mahjong_user_id'].' AND del_flag = 0 ORDER BY `u_mahjong_history`.`u_mahjong_history_id` DESC;';
        $u_mahjong_history_sth = $db->query($u_mahjong_history_sql);
        $u_mahjong_history_result[$user['u_mahjong_user_id']] = $u_mahjong_history_sth->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    exit($e->getMessage());
}

// データ整形
$u_mahjong_history_result = array_column($u_mahjong_history_result, null, 'u_mahjong_user_id');

// ----------------------------------------------------------------------
// データ操作関数群
// ----------------------------------------------------------------------

/**
 * ポイント計算
 *
 * @param int|string $rank
 * @param int $score
 * @return int
 */
function _calculationPoint($rank, int $score): int {
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
    return (int)round($point); // 明示的に整数型で返す
}

/**
 * データを追加
 *
 * @param int $userId
 * @param int $groupId
 * @param int $game
 * @param int $direction
 * @param int|string $rank
 * @param int $score
 * @param string $playDate YYYY-MM-DD形式
 * @return bool
 */
function addData(int $userId, int $groupId, int $game, int $direction, $rank, int $score, string $playDate): bool {
    try {
        $db = Database::getInstance($dsn, $dbUser, $dbPass)->getConnection();
        $sql = 'INSERT INTO `' . $db . '`.`u_mahjong_history` (u_mahjong_user_id, m_mahjong_group_id, game, m_direction_id, rank, score, point, play_date, reg_date) VALUES (:userId, :groupId, :game, :direction, :rank, :score, :point, :playDate, NOW())';
        $stmt = $db->prepare($sql);
        $point = _calculationPoint($rank, $score);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':groupId', $groupId, PDO::PARAM_INT);
        $stmt->bindParam(':game', $game, PDO::PARAM_INT);
        $stmt->bindParam(':direction', $direction, PDO::PARAM_INT);
        $stmt->bindParam(':rank', $rank);
        $stmt->bindParam(':score', $score, PDO::PARAM_INT);
        $stmt->bindParam(':point', $point, PDO::PARAM_INT);
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
 * @param int $hisotryId
 * @param int|string $rank
 * @param int $score
 * @param int $game
 * @param int $direction
 * @return bool
 */
function updateData(int $hisotryId, $rank, int $score, int $game, int $direction): bool {
    try {
        $db = Database::getInstance($dsn, $dbUser, $dbPass)->getConnection();
        $sql = 'UPDATE `' . $db . '`.`u_mahjong_history` SET `rank` = :rank, `score` = :score, `game` = :game, `m_direction_id` = :direction, `point` = :point WHERE `u_mahjong_history_id` = :u_mahjong_history_id';
        $stmt = $db->prepare($sql);
        $point = _calculationPoint($rank, $score);
        $stmt->bindParam(':rank', $rank);
        $stmt->bindParam(':score', $score, PDO::PARAM_INT);
        $stmt->bindParam(':game', $game, PDO::PARAM_INT);
        $stmt->bindParam(':direction', $direction, PDO::PARAM_INT);
        $stmt->bindParam(':point', $point, PDO::PARAM_INT);
        $stmt->bindParam(':u_mahjong_history_id', $hisotryId, PDO::PARAM_INT);
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
        $db = Database::getInstance($dsn, $dbUser, $dbPass)->getConnection();
        $sql = 'UPDATE `' . $db . '`.`u_mahjong_history` SET `del_flag` = 1 WHERE `u_mahjong_history_id` = :u_mahjong_history_id';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':u_mahjong_history_id', $historyId, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (Exception $e) {
        error_log('データ削除エラー: ' . $e->getMessage());
        return false;
    }
}
?>
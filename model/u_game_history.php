<?php

require_once __DIR__ . '/../config/db_connect.php';

/**
 * ゲーム履歴モデルクラス
 * ゲーム履歴の取得、更新、削除を行う
 */
class UGameHistory
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * 全履歴をユーザーIDごとにグループ化して返す
     */
    public function getAllGameHistory()
    {
        try {
            $sql = 'SELECT * FROM u_game_history WHERE del_flag = 0 ORDER BY play_date DESC';
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $grouped = [];
            foreach ($rows as $row) {
                $userId = $row['u_user_id'];
                $grouped[$userId][] = $row;
            }
            return $grouped;
        } catch (Exception $e) {
            error_log('ゲーム履歴取得エラー: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 全履歴をフラットな配列で返す（管理画面用）
     */
    public function getAllGameHistoryFlat()
    {
        try {
            $sql = 'SELECT * FROM u_game_history WHERE del_flag = 0 ORDER BY u_game_history_id DESC';
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('ゲーム履歴取得エラー: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 成績レコードを追加します。
     */
    public function addData(int $userId, int $tableId, int $game, int $direction, string $rank, int $score, string $playDate, int $mistakeCount = 0): bool {
        try {

            $sql = 'INSERT INTO `u_game_history` (u_user_id, u_table_id, game, m_direction_id, rank, score, point, play_date, mistake_count, reg_date) VALUES (:userId, :tableId, :game, :direction, :rank, :score, :point, :playDate, :mistakeCount, NOW())';
            $stmt = $this->db->prepare($sql);
            $point = $this->_calculatePoint($rank, $score);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':tableId', $tableId, PDO::PARAM_INT);
            $stmt->bindParam(':game', $game, PDO::PARAM_INT);
            $stmt->bindParam(':direction', $direction, PDO::PARAM_INT);
            $stmt->bindParam(':rank', $rank);
            $stmt->bindParam(':score', $score, PDO::PARAM_INT);
            $stmt->bindParam(':point', $point);
            $stmt->bindParam(':playDate', $playDate);
            $stmt->bindParam(':mistakeCount', $mistakeCount, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('データ追加エラー: ' . $e->getMessage()); // エラーログ出力
            return false;
        }
    }

    /**
     * 成績レコードを更新します。
     */
    public function updateData(int $historyId, string $rank, int $score, int $game, int $direction): bool {
        try {
            $sql = 'UPDATE `u_game_history` SET `rank` = :rank, `score` = :score, `game` = :game, `m_direction_id` = :direction, `point` = :point WHERE `u_game_history_id` = :u_game_history_id';
            $stmt = $this->db->prepare($sql);
            $point = $this->_calculatePoint($rank, $score);
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
     * ゲーム履歴を追加（管理画面用）
     */
    public function addGameHistory(array $data)
    {
        try {
            $sql = 'INSERT INTO u_game_history (play_date, game, u_user_id, u_table_id, rank, score, point, m_direction_id, mistake_count) VALUES (:play_date, :game, :u_user_id, :u_table_id, :rank, :score, :point, :m_direction_id, :mistake_count)';
            $stmt = $this->db->prepare($sql);
            $point = $this->_calculatePoint($data['rank'], $data['score']);
            $stmt->bindParam(':play_date', $data['play_date']);
            $stmt->bindParam(':game', $data['game']);
            $stmt->bindParam(':u_user_id', $data['u_user_id']);
            $stmt->bindParam(':u_table_id', $data['u_table_id']);
            $stmt->bindParam(':rank', $data['rank']);
            $stmt->bindParam(':score', $data['score']);
            $stmt->bindParam(':point', $point);
            $stmt->bindParam(':m_direction_id', $data['m_direction_id']);
            $stmt->bindParam(':mistake_count', $data['mistake_count']);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('ゲーム履歴追加エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ゲーム履歴を更新（管理画面用）
     */
    public function updateGameHistory($gameId, array $data)
    {
        try {
            $sql = 'UPDATE u_game_history SET play_date = :play_date, game = :game, u_user_id = :u_user_id, u_table_id = :u_table_id, rank = :rank, score = :score, point = :point, m_direction_id = :m_direction_id, mistake_count = :mistake_count WHERE u_game_history_id = :game_id';
            $stmt = $this->db->prepare($sql);
            $point = $this->_calculatePoint($data['rank'], $data['score']);
            $stmt->bindParam(':play_date', $data['play_date']);
            $stmt->bindParam(':game', $data['game']);
            $stmt->bindParam(':u_user_id', $data['u_user_id']);
            $stmt->bindParam(':u_table_id', $data['u_table_id']);
            $stmt->bindParam(':rank', $data['rank']);
            $stmt->bindParam(':score', $data['score']);
            $stmt->bindParam(':point', $point);
            $stmt->bindParam(':m_direction_id', $data['m_direction_id']);
            $stmt->bindParam(':mistake_count', $data['mistake_count']);
            $stmt->bindParam(':game_id', $gameId);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('ゲーム履歴更新エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 成績レコードを論理削除します（del_flag=1）。
     */
    public function deleteData(int $historyId): bool {
        try {
            $sql = 'UPDATE `u_game_history` SET `del_flag` = 1 WHERE `u_game_history_id` = :u_game_history_id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':u_game_history_id', $historyId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('データ削除エラー: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ゲーム履歴を削除（管理画面用）
     */
    public function deleteGameHistory($gameId)
    {
        try {
            $sql = 'UPDATE u_game_history SET del_flag = 1 WHERE u_game_history_id = :game_id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':game_id', $gameId);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('ゲーム履歴削除エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ルールに基づいたポイント計算を行います。
     */
    private function _calculatePoint($rank, int $score) {
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

}
?>
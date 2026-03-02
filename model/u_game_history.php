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
     * 指定日・指定卓の最新半荘（最大game）の4件を返す
     */
    public function getLatestGameByDate(int $tableId, string $playDate): array
    {
        try {
            $maxSql = 'SELECT MAX(game) AS max_game
                       FROM u_game_history
                       WHERE u_table_id = :tableId
                         AND DATE(play_date) = :playDate
                         AND del_flag = 0';
            $maxStmt = $this->db->prepare($maxSql);
            $maxStmt->bindValue(':tableId', $tableId, PDO::PARAM_INT);
            $maxStmt->bindValue(':playDate', $playDate);
            $maxStmt->execute();
            $maxGame = $maxStmt->fetchColumn();

            if ($maxGame === false || $maxGame === null) {
                return [];
            }

            $sql = 'SELECT *
                    FROM u_game_history
                    WHERE u_table_id = :tableId
                      AND DATE(play_date) = :playDate
                      AND game = :game
                      AND del_flag = 0
                    ORDER BY m_direction_id ASC, u_game_history_id ASC';
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':tableId', $tableId, PDO::PARAM_INT);
            $stmt->bindValue(':playDate', $playDate);
            $stmt->bindValue(':game', (int)$maxGame, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('最新半荘取得エラー: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 指定日・指定卓の次半荘番号を返す（未登録なら1）
     */
    public function getNextGameNumberByDate(int $tableId, string $playDate): int
    {
        try {
            $sql = 'SELECT COALESCE(MAX(game), 0) + 1 AS next_game
                    FROM u_game_history
                    WHERE u_table_id = :tableId
                      AND DATE(play_date) = :playDate
                      AND del_flag = 0';
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':tableId', $tableId, PDO::PARAM_INT);
            $stmt->bindValue(':playDate', $playDate);
            $stmt->execute();
            $nextGame = (int)$stmt->fetchColumn();
            return max($nextGame, 1);
        } catch (Exception $e) {
            error_log('次半荘番号取得エラー: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * 指定日・指定卓・指定半荘番号のデータ存在有無
     */
    public function existsGameForDate(int $tableId, string $playDate, int $game): bool
    {
        try {
            $sql = 'SELECT COUNT(*) AS cnt
                    FROM u_game_history
                    WHERE u_table_id = :tableId
                      AND DATE(play_date) = :playDate
                      AND game = :game
                      AND del_flag = 0';
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':tableId', $tableId, PDO::PARAM_INT);
            $stmt->bindValue(':playDate', $playDate);
            $stmt->bindValue(':game', $game, PDO::PARAM_INT);
            $stmt->execute();
            return (int)$stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            error_log('半荘重複チェックエラー: ' . $e->getMessage());
            return true;
        }
    }

    /**
     * 成績レコードをトランザクションで一括追加
     */
    public function addBatchData(array $records): bool
    {
        if (empty($records)) {
            return false;
        }

        $startedTransaction = false;
        try {
            if (method_exists($this->db, 'beginTransaction') && method_exists($this->db, 'inTransaction')) {
                if (!$this->db->inTransaction()) {
                    $this->db->beginTransaction();
                    $startedTransaction = true;
                }
            }

            $sql = 'INSERT INTO `u_game_history`
                    (u_user_id, u_table_id, game, m_direction_id, rank, score, point, play_date, mistake_count, reg_date)
                    VALUES
                    (:userId, :tableId, :game, :direction, :rank, :score, :point, :playDate, :mistakeCount, NOW())';
            $stmt = $this->db->prepare($sql);

            foreach ($records as $record) {
                $userId = (int)($record['u_user_id'] ?? 0);
                $tableId = (int)($record['u_table_id'] ?? 0);
                $game = (int)($record['game'] ?? 0);
                $direction = (int)($record['m_direction_id'] ?? 0);
                $rank = (string)($record['rank'] ?? '');
                $score = (int)($record['score'] ?? 0);
                $playDate = (string)($record['play_date'] ?? '');
                $mistakeCount = (int)($record['mistake_count'] ?? 0);
                $point = $this->_calculatePoint($rank, $score);

                $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
                $stmt->bindValue(':tableId', $tableId, PDO::PARAM_INT);
                $stmt->bindValue(':game', $game, PDO::PARAM_INT);
                $stmt->bindValue(':direction', $direction, PDO::PARAM_INT);
                $stmt->bindValue(':rank', $rank);
                $stmt->bindValue(':score', $score, PDO::PARAM_INT);
                $stmt->bindValue(':point', $point);
                $stmt->bindValue(':playDate', $playDate);
                $stmt->bindValue(':mistakeCount', $mistakeCount, PDO::PARAM_INT);

                if (!$stmt->execute()) {
                    if ($startedTransaction && method_exists($this->db, 'rollBack')) {
                        $this->db->rollBack();
                    }
                    return false;
                }
            }

            if ($startedTransaction && method_exists($this->db, 'commit')) {
                $this->db->commit();
            }
            return true;
        } catch (Exception $e) {
            if (
                $startedTransaction &&
                method_exists($this->db, 'inTransaction') &&
                method_exists($this->db, 'rollBack') &&
                $this->db->inTransaction()
            ) {
                $this->db->rollBack();
            }
            error_log('一括登録エラー: ' . $e->getMessage());
            return false;
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

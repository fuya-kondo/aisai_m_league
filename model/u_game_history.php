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
     * 1ゲームを1アイテムとして返す（管理画面用）
     */
    public function getAllGameHistoryByGame(): array
    {
        try {
            $sql = 'SELECT * FROM u_game_history WHERE del_flag = 0 ORDER BY play_date DESC, u_game_history_id DESC';
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $games = [];
            foreach ($rows as $row) {
                $gameId = $this->buildGameId($row['play_date'], (int)$row['game'], (int)$row['u_table_id']);
                if (!isset($games[$gameId])) {
                    $games[$gameId] = [
                        'game_id' => $gameId,
                        'play_date' => $row['play_date'],
                        'game' => (int)$row['game'],
                        'u_table_id' => (int)$row['u_table_id'],
                        'participants' => []
                    ];
                }

                $games[$gameId]['participants'][(int)$row['m_direction_id']] = [
                    'historyId' => (int)$row['u_game_history_id'],
                    'playerId' => (int)$row['u_user_id'],
                    'seat' => (int)$row['m_direction_id'],
                    'rank' => (string)$row['rank'],
                    'score' => (int)$row['score'],
                    'point' => (float)$row['point'],
                    'chombo' => (int)$row['mistake_count']
                ];
            }

            foreach ($games as &$game) {
                ksort($game['participants']);
                $game['participants'] = array_values($game['participants']);
            }

            return array_values($games);
        } catch (Exception $e) {
            error_log('ゲーム履歴取得(ゲーム単位)エラー: ' . $e->getMessage());
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
     * ゲーム全体（4人分）を登録・更新
     */
    public function upsertGameHistory(array $gameData, ?string $originalGameId = null): bool
    {
        $this->validateGamePayload($gameData);

        $playDate = $gameData['play_date'];
        $game = (int)$gameData['game'];
        $tableId = (int)$gameData['u_table_id'];
        $newGameId = $this->buildGameId($playDate, $game, $tableId);

        try {
            $this->db->beginTransaction();

            if (!empty($originalGameId)) {
                $this->markGameDeletedByGameId($originalGameId);
            }
            $this->markGameDeletedByGameId($newGameId);

            $sql = 'INSERT INTO u_game_history (play_date, game, u_user_id, u_table_id, rank, score, point, m_direction_id, mistake_count, reg_date) VALUES (:play_date, :game, :u_user_id, :u_table_id, :rank, :score, :point, :m_direction_id, :mistake_count, NOW())';
            $stmt = $this->db->prepare($sql);

            foreach ($gameData['participants'] as $participant) {
                $rank = (string)$participant['rank'];
                $score = (int)$participant['score'];
                $point = $this->_calculatePoint($rank, $score);
                $chombo = isset($participant['chombo']) ? (int)$participant['chombo'] : 0;

                $stmt->bindValue(':play_date', $playDate);
                $stmt->bindValue(':game', $game, PDO::PARAM_INT);
                $stmt->bindValue(':u_user_id', (int)$participant['playerId'], PDO::PARAM_INT);
                $stmt->bindValue(':u_table_id', $tableId, PDO::PARAM_INT);
                $stmt->bindValue(':rank', $rank);
                $stmt->bindValue(':score', $score, PDO::PARAM_INT);
                $stmt->bindValue(':point', $point);
                $stmt->bindValue(':m_direction_id', (int)$participant['seat'], PDO::PARAM_INT);
                $stmt->bindValue(':mistake_count', $chombo, PDO::PARAM_INT);
                $stmt->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('ゲーム全体更新エラー: ' . $e->getMessage());
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
     * gameId単位で論理削除
     */
    public function deleteGameHistoryByGameId(string $gameId): bool
    {
        try {
            return $this->markGameDeletedByGameId($gameId);
        } catch (Exception $e) {
            error_log('ゲーム履歴削除(ゲーム単位)エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * gameIdから1ゲーム分のデータを取得
     */
    public function getGameHistoryByGameId(string $gameId): ?array
    {
        $parsed = $this->parseGameId($gameId);
        $sql = 'SELECT * FROM u_game_history WHERE del_flag = 0 AND DATE(play_date) = :play_date AND game = :game AND u_table_id = :table_id ORDER BY m_direction_id ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':play_date', $parsed['play_date']);
        $stmt->bindValue(':game', $parsed['game'], PDO::PARAM_INT);
        $stmt->bindValue(':table_id', $parsed['table_id'], PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($rows) !== 4) {
            return null;
        }

        $participants = [];
        foreach ($rows as $row) {
            $participants[] = [
                'historyId' => (int)$row['u_game_history_id'],
                'playerId' => (int)$row['u_user_id'],
                'seat' => (int)$row['m_direction_id'],
                'rank' => (string)$row['rank'],
                'score' => (int)$row['score'],
                'point' => (float)$row['point'],
                'chombo' => (int)$row['mistake_count'],
            ];
        }

        return [
            'game_id' => $gameId,
            'play_date' => $rows[0]['play_date'],
            'game' => (int)$rows[0]['game'],
            'u_table_id' => (int)$rows[0]['u_table_id'],
            'participants' => $participants,
        ];
    }

    /**
     * gameIdを生成
     */
    public function buildGameId(string $playDate, int $game, int $tableId): string
    {
        $dateOnly = date('Y-m-d', strtotime($playDate));
        return sprintf('%s#%d#%d', $dateOnly, $game, $tableId);
    }

    private function parseGameId(string $gameId): array
    {
        $parts = explode('#', $gameId);
        if (count($parts) !== 3) {
            throw new InvalidArgumentException('不正なgameIdです');
        }

        return [
            'play_date' => $parts[0],
            'game' => (int)$parts[1],
            'table_id' => (int)$parts[2]
        ];
    }

    private function markGameDeletedByGameId(string $gameId): bool
    {
        $parsed = $this->parseGameId($gameId);
        $sql = 'UPDATE u_game_history SET del_flag = 1 WHERE del_flag = 0 AND DATE(play_date) = :play_date AND game = :game AND u_table_id = :table_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':play_date', $parsed['play_date']);
        $stmt->bindValue(':game', $parsed['game'], PDO::PARAM_INT);
        $stmt->bindValue(':table_id', $parsed['table_id'], PDO::PARAM_INT);
        return $stmt->execute();
    }

    private function validateGamePayload(array $gameData): void
    {
        if (!isset($gameData['participants']) || !is_array($gameData['participants']) || count($gameData['participants']) !== 4) {
            throw new InvalidArgumentException('participantsは4人分必須です');
        }

        $seats = [];
        foreach ($gameData['participants'] as $participant) {
            if (!isset($participant['playerId'], $participant['seat'], $participant['rank'], $participant['score'])) {
                throw new InvalidArgumentException('参加者情報が不足しています');
            }
            $seat = (int)$participant['seat'];
            if ($seat < 1 || $seat > 4) {
                throw new InvalidArgumentException('seatは1〜4で指定してください');
            }
            $seats[] = $seat;
        }

        sort($seats);
        if ($seats !== [1, 2, 3, 4]) {
            throw new InvalidArgumentException('seatは1〜4を重複なく指定してください');
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

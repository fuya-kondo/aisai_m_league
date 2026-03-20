<?php
namespace App\Repositories;

use Database;
use PDO;
use PDOStatement;

/**
 * u_game_history テーブル専用の repository。
 * SQL 実行と永続化責務だけを持ち、登録・更新時の業務判断は service 側へ委譲する。
 */
final class GameHistoryRepository
{
    private Database $database;

    public function __construct(?Database $database = null)
    {
        $this->database = $database ?? Database::getInstance();
    }

    public function getGroupedActiveHistory(): array
    {
        $rows = $this->fetchAll(
            'SELECT * FROM u_game_history WHERE del_flag = 0 ORDER BY play_date DESC, game DESC, u_game_history_id DESC'
        );

        $groupedHistory = [];
        foreach ($rows as $row) {
            $groupedHistory[$row['u_user_id']][] = $row;
        }

        return $groupedHistory;
    }

    public function getFlatActiveHistory(): array
    {
        return $this->fetchAll(
            'SELECT * FROM u_game_history WHERE del_flag = 0 ORDER BY u_game_history_id DESC'
        );
    }

    public function findLatestGameRowsByDate(int $tableId, string $playDate): array
    {
        $maxStatement = $this->database->prepare(
            'SELECT MAX(game) AS max_game
             FROM u_game_history
             WHERE u_table_id = :tableId
               AND DATE(play_date) = :playDate
               AND del_flag = 0'
        );
        $maxStatement->bindValue(':tableId', $tableId, PDO::PARAM_INT);
        $maxStatement->bindValue(':playDate', $playDate);
        $maxStatement->execute();

        $maxGame = $maxStatement->fetchColumn();
        if ($maxGame === false || $maxGame === null) {
            return [];
        }

        $statement = $this->database->prepare(
            'SELECT *
             FROM u_game_history
             WHERE u_table_id = :tableId
               AND DATE(play_date) = :playDate
               AND game = :game
               AND del_flag = 0
             ORDER BY m_direction_id ASC, u_game_history_id ASC'
        );
        $statement->bindValue(':tableId', $tableId, PDO::PARAM_INT);
        $statement->bindValue(':playDate', $playDate);
        $statement->bindValue(':game', (int)$maxGame, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNextGameNumberByDate(int $tableId, string $playDate): int
    {
        $statement = $this->database->prepare(
            'SELECT COALESCE(MAX(game), 0) + 1 AS next_game
             FROM u_game_history
             WHERE u_table_id = :tableId
               AND DATE(play_date) = :playDate
               AND del_flag = 0'
        );
        $statement->bindValue(':tableId', $tableId, PDO::PARAM_INT);
        $statement->bindValue(':playDate', $playDate);
        $statement->execute();

        return max((int)$statement->fetchColumn(), 1);
    }

    public function existsGameForDate(int $tableId, string $playDate, int $game): bool
    {
        $statement = $this->database->prepare(
            'SELECT COUNT(*)
             FROM u_game_history
             WHERE u_table_id = :tableId
               AND DATE(play_date) = :playDate
               AND game = :game
               AND del_flag = 0'
        );
        $statement->bindValue(':tableId', $tableId, PDO::PARAM_INT);
        $statement->bindValue(':playDate', $playDate);
        $statement->bindValue(':game', $game, PDO::PARAM_INT);
        $statement->execute();

        return (int)$statement->fetchColumn() > 0;
    }

    public function findById(int $historyId): ?array
    {
        $statement = $this->database->prepare(
            'SELECT * FROM u_game_history WHERE u_game_history_id = :historyId LIMIT 1'
        );
        $statement->bindValue(':historyId', $historyId, PDO::PARAM_INT);
        $statement->execute();

        $history = $statement->fetch(PDO::FETCH_ASSOC);
        return $history === false ? null : $history;
    }

    public function findBatchByTableDateGame(int $tableId, string $playDate, int $game): array
    {
        $statement = $this->database->prepare(
            'SELECT *
             FROM u_game_history
             WHERE u_table_id = :tableId
               AND DATE(play_date) = :playDate
               AND game = :game
               AND del_flag = 0
             ORDER BY m_direction_id ASC, u_game_history_id ASC'
        );
        $statement->bindValue(':tableId', $tableId, PDO::PARAM_INT);
        $statement->bindValue(':playDate', $playDate);
        $statement->bindValue(':game', $game, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert(array $payload, float|int $point, ?PDOStatement $statement = null): void
    {
        $insertStatement = $statement ?? $this->prepareInsertStatement();
        $this->bindMutationValues($insertStatement, $payload, $point);

        if (!$insertStatement->execute()) {
            throw new \RuntimeException('ゲーム履歴の追加に失敗しました。');
        }
    }

    public function update(int $historyId, array $payload, float|int $point): void
    {
        $statement = $this->database->prepare(
            'UPDATE u_game_history
             SET play_date = :playDate,
                 game = :game,
                 u_user_id = :userId,
                 u_table_id = :tableId,
                 rank = :rank,
                 score = :score,
                 point = :point,
                 m_direction_id = :directionId,
                 mistake_count = :mistakeCount
             WHERE u_game_history_id = :historyId'
        );

        $this->bindMutationValues($statement, $payload, $point);
        $statement->bindValue(':historyId', $historyId, PDO::PARAM_INT);

        if (!$statement->execute()) {
            throw new \RuntimeException('ゲーム履歴の更新に失敗しました。');
        }
    }

    public function softDelete(int $historyId): bool
    {
        $statement = $this->database->prepare(
            'UPDATE u_game_history SET del_flag = 1 WHERE u_game_history_id = :historyId'
        );
        $statement->bindValue(':historyId', $historyId, PDO::PARAM_INT);
        return $statement->execute();
    }

    public function softDeleteBatch(array $historyIds): bool
    {
        if (empty($historyIds)) {
            return false;
        }

        $placeholders = [];
        foreach (array_values($historyIds) as $index => $historyId) {
            $placeholders[] = ':historyId' . $index;
        }

        $statement = $this->database->prepare(
            'UPDATE u_game_history SET del_flag = 1 WHERE u_game_history_id IN (' . implode(', ', $placeholders) . ')'
        );

        foreach (array_values($historyIds) as $index => $historyId) {
            $statement->bindValue(':historyId' . $index, (int)$historyId, PDO::PARAM_INT);
        }

        return $statement->execute();
    }

    public function connection(): PDO
    {
        return $this->database->getConnection();
    }

    public function prepareInsertStatement(): PDOStatement
    {
        return $this->database->prepare(
            'INSERT INTO u_game_history
             (play_date, game, u_user_id, u_table_id, rank, score, point, m_direction_id, mistake_count, reg_date)
             VALUES
             (:playDate, :game, :userId, :tableId, :rank, :score, :point, :directionId, :mistakeCount, NOW())'
        );
    }

    private function bindMutationValues(PDOStatement $statement, array $payload, float|int $point): void
    {
        $statement->bindValue(':playDate', $payload['play_date']);
        $statement->bindValue(':game', $payload['game'], PDO::PARAM_INT);
        $statement->bindValue(':userId', $payload['u_user_id'], PDO::PARAM_INT);
        $statement->bindValue(':tableId', $payload['u_table_id'], PDO::PARAM_INT);
        $statement->bindValue(':rank', $payload['rank']);
        $statement->bindValue(':score', $payload['score'], PDO::PARAM_INT);
        $statement->bindValue(':point', $point);
        $statement->bindValue(':directionId', $payload['m_direction_id'], PDO::PARAM_INT);
        $statement->bindValue(':mistakeCount', $payload['mistake_count'], PDO::PARAM_INT);
    }

    private function fetchAll(string $sql): array
    {
        $statement = $this->database->prepare($sql);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}

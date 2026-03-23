<?php
namespace App\Repositories;

/**
 * 日次 AI コメントの保存・取得を扱う repository。
 * 1日1半荘ごとの4人分コメントセットを永続化する。
 */
final class DailyAiCommentRepository extends DatabaseRepository
{
    public function findByTableDateGame(int $tableId, string $playDate, int $game): ?array
    {
        return $this->fetchOne(
            'SELECT *
             FROM u_daily_ai_comment
             WHERE u_table_id = :table_id
               AND play_date = :play_date
               AND game = :game
             LIMIT 1',
            [
                ':table_id' => $tableId,
                ':play_date' => $playDate,
                ':game' => $game,
            ]
        );
    }

    public function upsert(array $payload): bool
    {
        return $this->execute(
            'INSERT INTO u_daily_ai_comment
             (play_date, game, u_table_id, model_name, status, response_json, error_message, generated_at, reg_date, upd_date)
             VALUES
             (:play_date, :game, :table_id, :model_name, :status, :response_json, :error_message, :generated_at, NOW(), NOW())
             ON DUPLICATE KEY UPDATE
                model_name = VALUES(model_name),
                status = VALUES(status),
                response_json = VALUES(response_json),
                error_message = VALUES(error_message),
                generated_at = VALUES(generated_at),
                upd_date = NOW()',
            [
                ':play_date' => $payload['play_date'],
                ':game' => $payload['game'],
                ':table_id' => $payload['u_table_id'],
                ':model_name' => $payload['model_name'],
                ':status' => $payload['status'],
                ':response_json' => $payload['response_json'],
                ':error_message' => $payload['error_message'],
                ':generated_at' => $payload['generated_at'],
            ]
        );
    }

    public function deleteByTableDateFromGame(int $tableId, string $playDate, int $fromGame): bool
    {
        return $this->execute(
            'DELETE FROM u_daily_ai_comment
             WHERE u_table_id = :table_id
               AND play_date = :play_date
               AND game >= :from_game',
            [
                ':table_id' => $tableId,
                ':play_date' => $playDate,
                ':from_game' => $fromGame,
            ]
        );
    }
}

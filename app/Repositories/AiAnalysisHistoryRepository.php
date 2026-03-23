<?php
namespace App\Repositories;

/**
 * AI分析履歴の保存・取得を扱う repository。
 */
final class AiAnalysisHistoryRepository extends DatabaseRepository
{
    public function insert(array $payload): bool
    {
        return $this->execute(
            'INSERT INTO u_ai_analysis_history
             (u_user_id, term, model_name, prompt_text, result_html, status, error_message, generated_at, reg_date, upd_date)
             VALUES
             (:u_user_id, :term, :model_name, :prompt_text, :result_html, :status, :error_message, :generated_at, NOW(), NOW())',
            [
                ':u_user_id' => $payload['u_user_id'],
                ':term' => $payload['term'],
                ':model_name' => $payload['model_name'],
                ':prompt_text' => $payload['prompt_text'],
                ':result_html' => $payload['result_html'],
                ':status' => $payload['status'],
                ':error_message' => $payload['error_message'],
                ':generated_at' => $payload['generated_at'],
            ]
        );
    }

    public function findRecentSuccessfulByUser(int $userId, int $limit): array
    {
        return $this->fetchAll(
            'SELECT *
             FROM u_ai_analysis_history
             WHERE u_user_id = :u_user_id
               AND status = :status
             ORDER BY generated_at DESC, u_ai_analysis_history_id DESC
             LIMIT ' . max($limit, 1),
            [
                ':u_user_id' => $userId,
                ':status' => 'success',
            ]
        );
    }

    public function findById(int $historyId): ?array
    {
        return $this->fetchOne(
            'SELECT *
             FROM u_ai_analysis_history
             WHERE u_ai_analysis_history_id = :history_id
             LIMIT 1',
            [':history_id' => $historyId]
        );
    }
}

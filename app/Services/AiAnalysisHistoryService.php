<?php
namespace App\Services;

use App\Repositories\AiAnalysisHistoryRepository;
use Throwable;

/**
 * AI分析履歴の保存・取得を扱う service。
 */
final class AiAnalysisHistoryService
{
    public function __construct(private readonly AiAnalysisHistoryRepository $repository)
    {
    }

    public function saveSuccess(int $userId, string $term, string $modelName, string $promptText, string $resultHtml): bool
    {
        return $this->insert([
            'u_user_id' => $userId,
            'term' => $term,
            'model_name' => $modelName,
            'prompt_text' => $promptText,
            'result_html' => $resultHtml,
            'status' => 'success',
            'error_message' => null,
            'generated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function saveFailure(int $userId, string $term, string $modelName, string $promptText, string $errorMessage): bool
    {
        return $this->insert([
            'u_user_id' => $userId,
            'term' => $term,
            'model_name' => $modelName,
            'prompt_text' => $promptText,
            'result_html' => null,
            'status' => 'failed',
            'error_message' => $errorMessage,
            'generated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function findRecentSuccessfulByUser(int $userId, int $limit = 3): array
    {
        try {
            return $this->repository->findRecentSuccessfulByUser($userId, $limit);
        } catch (Throwable $throwable) {
            error_log('AI分析履歴取得エラー: ' . $throwable->getMessage());
            return [];
        }
    }

    public function findById(int $historyId): ?array
    {
        try {
            return $this->repository->findById($historyId);
        } catch (Throwable $throwable) {
            error_log('AI分析履歴詳細取得エラー: ' . $throwable->getMessage());
            return null;
        }
    }

    private function insert(array $payload): bool
    {
        try {
            return $this->repository->insert($payload);
        } catch (Throwable $throwable) {
            error_log('AI分析履歴保存エラー: ' . $throwable->getMessage());
            return false;
        }
    }
}

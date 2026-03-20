<?php
/**
 * ティア履歴用の互換ラッパー。
 * 実処理は TierHistoryService / TierHistoryRepository へ委譲する。
 */
class UTierHistory
{
    private \App\Services\TierHistoryService $tierHistoryService;

    public function __construct()
    {
        $this->tierHistoryService = \App\Support\ServiceFactory::createTierHistoryService();
    }

    public function getAllTierHistory(): array
    {
        try {
            return $this->tierHistoryService->getAll();
        } catch (Exception $exception) {
            error_log('ティア履歴取得エラー: ' . $exception->getMessage());
            return [];
        }
    }

    public function getAllTierHistoryFlat(): array
    {
        try {
            return $this->tierHistoryService->getAllFlat();
        } catch (Exception $exception) {
            error_log('ティア履歴取得エラー: ' . $exception->getMessage());
            return [];
        }
    }

    public function getTierHistoryByUserId(int $userId): array
    {
        try {
            return $this->tierHistoryService->getByUserId($userId);
        } catch (Exception $exception) {
            error_log('ユーザーティア履歴取得エラー: ' . $exception->getMessage());
            return [];
        }
    }

    public function getTierHistoryById(int $tierHistoryId): ?array
    {
        try {
            return $this->tierHistoryService->getById($tierHistoryId);
        } catch (Exception $exception) {
            error_log('ティア履歴取得エラー: ' . $exception->getMessage());
            return null;
        }
    }

    public function updateTierHistory(int $tierHistoryId, array $tierHistoryData): bool
    {
        try {
            return $this->tierHistoryService->update($tierHistoryId, $tierHistoryData);
        } catch (Exception $exception) {
            error_log('ティア履歴更新エラー: ' . $exception->getMessage());
            throw $exception;
        }
    }

    public function deleteTierHistory(int $tierHistoryId): bool
    {
        try {
            return $this->tierHistoryService->delete($tierHistoryId);
        } catch (Exception $exception) {
            error_log('ティア履歴削除エラー: ' . $exception->getMessage());
            throw $exception;
        }
    }

    public function addTierHistory(array $tierHistoryData): bool
    {
        try {
            return $this->tierHistoryService->create($tierHistoryData);
        } catch (Exception $exception) {
            error_log('ティア履歴追加エラー: ' . $exception->getMessage());
            throw $exception;
        }
    }
}

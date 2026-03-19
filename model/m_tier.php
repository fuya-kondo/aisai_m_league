<?php
/**
 * ティアマスター用の互換ラッパー。
 * 実処理は TierService / TierRepository へ委譲する。
 */
class MTier
{
    private \App\Services\TierService $tierService;

    public function __construct()
    {
        $this->tierService = \App\Support\ServiceFactory::createTierService();
    }

    public function getAllData(): array
    {
        try {
            return $this->tierService->getAllIndexed();
        } catch (Exception $exception) {
            error_log('m_tier取得エラー: ' . $exception->getMessage());
            return [];
        }
    }

    public function updateTier(int $tierId, array $tierData): bool
    {
        try {
            return $this->tierService->update($tierId, $tierData);
        } catch (Exception $exception) {
            error_log('ティア更新エラー: ' . $exception->getMessage());
            throw $exception;
        }
    }

    public function deleteTier(int $tierId): bool
    {
        try {
            return $this->tierService->delete($tierId);
        } catch (Exception $exception) {
            error_log('ティア削除エラー: ' . $exception->getMessage());
            throw $exception;
        }
    }

    public function addTier(array $tierData): bool
    {
        try {
            return $this->tierService->create($tierData);
        } catch (Exception $exception) {
            error_log('ティア追加エラー: ' . $exception->getMessage());
            throw $exception;
        }
    }
}

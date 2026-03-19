<?php
/**
 * 対局履歴テーブル用の互換ラッパー。
 * 既存呼び出しは維持しつつ、実処理は app 側の GameHistoryService へ委譲する。
 */
class UGameHistory
{
    private \App\Services\GameHistoryService $gameHistoryService;

    public function __construct()
    {
        $this->gameHistoryService = \App\Support\ServiceFactory::createGameHistoryService();
    }

    public function getAllGameHistory(): array
    {
        return $this->gameHistoryService->getGroupedHistory();
    }

    public function getAllGameHistoryFlat(): array
    {
        return $this->gameHistoryService->getFlatHistory();
    }

    public function getLatestGameByDate(int $tableId, string $playDate): array
    {
        return $this->gameHistoryService->getLatestGameRowsByDate($tableId, $playDate);
    }

    public function getNextGameNumberByDate(int $tableId, string $playDate): int
    {
        return $this->gameHistoryService->getNextGameNumberByDate($tableId, $playDate);
    }

    public function existsGameForDate(int $tableId, string $playDate, int $game): bool
    {
        return $this->gameHistoryService->existsGameForDate($tableId, $playDate, $game);
    }

    public function addBatchData(array $records): bool
    {
        return $this->gameHistoryService->createBatch($records);
    }

    public function createHistory(array $historyData): bool
    {
        return $this->gameHistoryService->create($historyData);
    }

    public function updateHistory(int $historyId, array $historyData): bool
    {
        return $this->gameHistoryService->update($historyId, $historyData);
    }

    public function deleteHistory(int $historyId): bool
    {
        return $this->gameHistoryService->delete($historyId);
    }
}

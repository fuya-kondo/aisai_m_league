<?php
namespace App\Services;

use App\Repositories\TierHistoryRepository;

/**
 * ティア履歴の取得と CRUD を扱う service。
 */
final class TierHistoryService
{
    public function __construct(private readonly TierHistoryRepository $tierHistoryRepository)
    {
    }

    public function getAll(): array
    {
        return $this->tierHistoryRepository->findAll();
    }

    public function getAllFlat(): array
    {
        return $this->tierHistoryRepository->findAllFlat();
    }

    public function getByUserId(int $userId): array
    {
        return $this->tierHistoryRepository->findGroupedByUser($userId);
    }

    public function getById(int $tierHistoryId): ?array
    {
        return $this->tierHistoryRepository->findById($tierHistoryId);
    }

    public function create(array $tierHistoryData): bool
    {
        return $this->tierHistoryRepository->create($tierHistoryData);
    }

    public function update(int $tierHistoryId, array $tierHistoryData): bool
    {
        return $this->tierHistoryRepository->update($tierHistoryId, $tierHistoryData);
    }

    public function delete(int $tierHistoryId): bool
    {
        return $this->tierHistoryRepository->delete($tierHistoryId);
    }
}

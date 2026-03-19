<?php
namespace App\Services;

use App\Repositories\TierRepository;

/**
 * ティア定義の取得と CRUD を扱う service。
 */
final class TierService
{
    public function __construct(private readonly TierRepository $tierRepository)
    {
    }

    public function getAllIndexed(): array
    {
        return $this->tierRepository->findAllIndexed();
    }

    public function create(array $tierData): bool
    {
        return $this->tierRepository->create($tierData);
    }

    public function update(int $tierId, array $tierData): bool
    {
        return $this->tierRepository->update($tierId, $tierData);
    }

    public function delete(int $tierId): bool
    {
        return $this->tierRepository->delete($tierId);
    }
}

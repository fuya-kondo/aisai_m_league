<?php
namespace App\Services;

use App\Repositories\SettingRepository;

/**
 * 設定フラグの一覧取得とトグル更新を担当する service。
 */
final class SettingService
{
    public function __construct(private readonly SettingRepository $settingRepository)
    {
    }

    public function getAllIndexed(): array
    {
        return $this->settingRepository->findAllIndexed();
    }

    public function toggle(int $settingId): bool
    {
        return $this->settingRepository->toggle($settingId);
    }

    public function create(array $settingData): bool
    {
        return $this->settingRepository->create($settingData);
    }

    public function update(int $settingId, array $settingData): bool
    {
        return $this->settingRepository->update($settingId, $settingData);
    }

    public function delete(int $settingId): bool
    {
        return $this->settingRepository->delete($settingId);
    }
}

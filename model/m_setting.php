<?php
/**
 * アプリ全体の表示・運用モード設定を扱う互換ラッパー。
 * 実処理は SettingService / SettingRepository へ委譲する。
 */
class MSetting
{
    private \App\Services\SettingService $settingService;

    public function __construct()
    {
        $this->settingService = \App\Support\ServiceFactory::createSettingService();
    }

    public function getAllData(): array
    {
        try {
            return $this->settingService->getAllIndexed();
        } catch (Exception $exception) {
            error_log('m_setting取得エラー: ' . $exception->getMessage());
            return [];
        }
    }

    public function switchMode(int $settingId): bool
    {
        try {
            return $this->settingService->toggle($settingId);
        } catch (Exception $exception) {
            error_log('設定切り替えエラー: ' . $exception->getMessage());
            throw $exception;
        }
    }

    public function addSetting(array $settingData): bool
    {
        try {
            return $this->settingService->create($settingData);
        } catch (Exception $exception) {
            error_log('設定追加エラー: ' . $exception->getMessage());
            throw $exception;
        }
    }

    public function updateSetting(int $settingId, array $settingData): bool
    {
        try {
            return $this->settingService->update($settingId, $settingData);
        } catch (Exception $exception) {
            error_log('設定更新エラー: ' . $exception->getMessage());
            throw $exception;
        }
    }

    public function deleteSetting(int $settingId): bool
    {
        try {
            return $this->settingService->delete($settingId);
        } catch (Exception $exception) {
            error_log('設定削除エラー: ' . $exception->getMessage());
            throw $exception;
        }
    }
}

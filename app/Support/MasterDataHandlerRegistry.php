<?php
namespace App\Support;

/**
 * マスターデータ種別ごとの model handler 定義を返す support class。
 * MasterDataService が種別ごとの CRUD 定義だけを参照できるようにする。
 */
final class MasterDataHandlerRegistry
{
    public static function build(
        \MBadge $badgeModel,
        \MTier $tierModel,
        \MTitle $titleModel,
        \MDirection $directionModel,
        \MGameDay $gameDayModel,
        \MGroup $groupModel,
        \MRule $ruleModel,
        \MSetting $settingModel,
        \UTierHistory $tierHistoryModel
    ): array {
        return [
            'badge' => [
                'update' => fn(string $id, array $payload) => $badgeModel->updateBadge($id, $payload),
                'delete' => fn(string $id) => $badgeModel->deleteBadge($id),
                'create' => fn(array $payload) => $badgeModel->addBadge($payload),
            ],
            'tier' => [
                'update' => fn(string $id, array $payload) => $tierModel->updateTier((int) $id, $payload),
                'delete' => fn(string $id) => $tierModel->deleteTier((int) $id),
                'create' => fn(array $payload) => $tierModel->addTier($payload),
            ],
            'title' => [
                'update' => fn(string $id, array $payload) => $titleModel->updateTitle($id, $payload),
                'delete' => fn(string $id) => $titleModel->deleteTitle($id),
                'create' => fn(array $payload) => $titleModel->addTitle($payload),
            ],
            'direction' => [
                'update' => fn(string $id, array $payload) => $directionModel->updateDirection($id, $payload),
                'delete' => fn(string $id) => $directionModel->deleteDirection($id),
                'create' => fn(array $payload) => $directionModel->addDirection($payload),
            ],
            'game_day' => [
                'update' => fn(string $id, array $payload) => $gameDayModel->updateGameDay($id, $payload),
                'delete' => fn(string $id) => $gameDayModel->deleteGameDay($id),
                'create' => fn(array $payload) => $gameDayModel->addGameDay($payload),
            ],
            'group' => [
                'update' => fn(string $id, array $payload) => $groupModel->updateGroup($id, $payload),
                'delete' => fn(string $id) => $groupModel->deleteGroup($id),
                'create' => fn(array $payload) => $groupModel->addGroup($payload),
            ],
            'rule' => [
                'update' => fn(string $id, array $payload) => $ruleModel->updateRule($id, $payload),
                'delete' => fn(string $id) => $ruleModel->deleteRule($id),
                'create' => fn(array $payload) => $ruleModel->addRule($payload),
            ],
            'setting' => [
                'update' => fn(string $id, array $payload) => $settingModel->updateSetting((int) $id, $payload),
                'delete' => fn(string $id) => $settingModel->deleteSetting((int) $id),
                'create' => fn(array $payload) => $settingModel->addSetting($payload),
            ],
            'tier_history' => [
                'update' => fn(string $id, array $payload) => $tierHistoryModel->updateTierHistory((int) $id, $payload),
                'delete' => fn(string $id) => $tierHistoryModel->deleteTierHistory((int) $id),
                'create' => fn(array $payload) => $tierHistoryModel->addTierHistory($payload),
            ],
        ];
    }
}

<?php
namespace App\Services;

use App\Support\MasterDataHandlerRegistry;

/**
 * マスターデータ種別ごとの CRUD を一元化する service。
 * controller が switch を持たずに済むよう、type ごとの handler 定義だけを参照する。
 */
final class MasterDataService
{
    private array $handlers;

    public function __construct(
        \MBadge $badgeModel,
        \MTier $tierModel,
        \MTitle $titleModel,
        \MDirection $directionModel,
        \MGameDay $gameDayModel,
        \MGroup $groupModel,
        \MRule $ruleModel,
        \MSetting $settingModel,
        \UTierHistory $tierHistoryModel
    ) {
        $this->handlers = MasterDataHandlerRegistry::build(
            $badgeModel,
            $tierModel,
            $titleModel,
            $directionModel,
            $gameDayModel,
            $groupModel,
            $ruleModel,
            $settingModel,
            $tierHistoryModel,
        );
    }

    /**
     * 種別ごとの update handler を引き当てて処理を委譲する。
     */
    public function update(string $type, string $id, array $payload): mixed
    {
        return $this->getHandler($type, 'update')($id, $payload);
    }

    /**
     * 削除処理も registry 定義に寄せ、controller 側の分岐をなくす。
     */
    public function delete(string $type, string $id): mixed
    {
        return $this->getHandler($type, 'delete')($id);
    }

    /**
     * 新規追加処理を種別別 handler に委譲する。
     */
    public function create(string $type, array $payload): mixed
    {
        return $this->getHandler($type, 'create')($payload);
    }

    /**
     * 未定義の種別や操作はここで止め、controller へ不正な実行を流さない。
     */
    private function getHandler(string $type, string $operation): callable
    {
        if (!isset($this->handlers[$type][$operation])) {
            throw new \Exception("Unknown master data type: {$type}");
        }

        return $this->handlers[$type][$operation];
    }
}

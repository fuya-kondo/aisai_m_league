<?php
namespace App\Support;

use App\Repositories\GameHistoryRepository;
use App\Repositories\SettingRepository;
use App\Repositories\TierHistoryRepository;
use App\Repositories\TierRepository;
use App\Repositories\UserRepository;
use App\Services\GameHistoryService;
use App\Services\GamePointCalculator;
use App\Services\MasterDataService;
use App\Services\SettingService;
use App\Services\TierHistoryService;
use App\Services\TierService;
use App\Services\UserService;

/**
 * アプリ内の依存生成を集中管理する簡易 factory。
 * controller / legacy model 側で new を重ねず、生成ルールを 1 か所に揃える。
 */
final class ServiceFactory
{
    public static function createUserService(): UserService
    {
        return new UserService(new UserRepository());
    }

    public static function createGameHistoryService(): GameHistoryService
    {
        return new GameHistoryService(new GameHistoryRepository(), new GamePointCalculator());
    }

    public static function createSettingService(): SettingService
    {
        return new SettingService(new SettingRepository());
    }

    public static function createTierService(): TierService
    {
        return new TierService(new TierRepository());
    }

    public static function createTierHistoryService(): TierHistoryService
    {
        return new TierHistoryService(new TierHistoryRepository());
    }

    public static function createMasterDataService(
        \MBadge $badgeModel,
        \MTier $tierModel,
        \MTitle $titleModel,
        \MDirection $directionModel,
        \MGameDay $gameDayModel,
        \MGroup $groupModel,
        \MRule $ruleModel,
        \MSetting $settingModel,
        \UTierHistory $tierHistoryModel
    ): MasterDataService {
        return new MasterDataService(
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
}

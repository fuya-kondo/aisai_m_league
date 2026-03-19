<?php
namespace App\Support;

/**
 * main/admin 画面で使うデータ取得を用途別メソッドに分けた provider。
 * 取得責務を画面用途ごとに分け、controller が不要なデータまで抱えないようにする。
 */
final class MasterDataProvider
{
    public function __construct(
        private readonly \App\Services\UserService $userService,
        private readonly \App\Services\SettingService $settingService,
        private readonly \App\Services\TierService $tierService,
        private readonly \App\Services\TierHistoryService $tierHistoryService,
        private readonly \MBadge $badgeModel,
        private readonly \MDirection $directionModel,
        private readonly \MGameDay $gameDayModel,
        private readonly \MGroup $groupModel,
        private readonly \MRule $ruleModel,
        private readonly \MTitle $titleModel,
        private readonly \UTable $tableModel,
        private readonly \UTitle $userTitleModel,
        private readonly \App\Services\GameHistoryService $gameHistoryService,
    ) {
    }

    /**
     * main 画面で参照する一覧・履歴系データをまとめて返す。
     * 集計画面では index 済み配列が多いため、画面側の探索処理を減らす。
     */
    public function getMainData(): array
    {
        return [
            'uUserList' => $this->userService->getAllIndexed(),
            'mBadgeList' => $this->badgeModel->getAllData(),
            'mDirectionList' => $this->directionModel->getAllData(),
            'mGameDayList' => $this->gameDayModel->getAllData(),
            'mRuleList' => $this->ruleModel->getAllData(),
            'mGroupList' => $this->groupModel->getAllData(),
            'mTierList' => $this->tierService->getAllIndexed(),
            'mTitleList' => $this->titleModel->getAllData(),
            'mSettingList' => $this->settingService->getAllIndexed(),
            'uGameHistoryList' => $this->gameHistoryService->getGroupedHistory(),
            'uTableList' => $this->tableModel->getAllUserTables(),
            'uTierHistoryList' => $this->tierHistoryService->getAllFlat(),
            'uTitleList' => $this->userTitleModel->getAllUserTitles(),
        ];
    }

    /**
     * admin 画面で使う管理対象データを返す。
     * 一覧テーブルではフラットな履歴形式が扱いやすいため main 用と返却形式を分ける。
     */
    public function getAdminData(): array
    {
        return [
            'uUserList' => $this->userService->getAllIndexed(),
            'mBadgeList' => $this->badgeModel->getAllData(),
            'mDirectionList' => $this->directionModel->getAllData(),
            'mGameDayList' => $this->gameDayModel->getAllData(),
            'mRuleList' => $this->ruleModel->getAllData(),
            'mGroupList' => $this->groupModel->getAllData(),
            'mTierList' => $this->tierService->getAllIndexed(),
            'mSettingList' => $this->settingService->getAllIndexed(),
            'uGameHistoryListFlat' => $this->gameHistoryService->getFlatHistory(),
            'uTableList' => $this->tableModel->getAllUserTables(),
            'uTierHistoryList' => $this->tierHistoryService->getAllFlat(),
        ];
    }
}

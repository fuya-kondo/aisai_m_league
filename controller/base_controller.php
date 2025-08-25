<?php

class BaseController
{
    protected $uUser;
    protected $uGameHistory;
    protected $uTierHistory;
    protected $uTitle;
    protected $uTable;
    protected $mBadge;
    protected $mDirection;
    protected $mGameDay;
    protected $mGroup;
    protected $mRule;
    protected $mSetting;
    protected $mTier;
    protected $mTitle;

    public function __construct()
    {
        $this->uUser        = new UUser();
        $this->uGameHistory = new UGameHistory();
        $this->uTierHistory = new UTierHistory();
        $this->uTitle       = new UTitle();
        $this->uTable       = new UTable();
        $this->mBadge       = new MBadge();
        $this->mDirection   = new MDirection();
        $this->mGameDay     = new MGameDay();
        $this->mGroup       = new MGroup();
        $this->mRule        = new MRule();
        $this->mSetting     = new MSetting();
        $this->mTier        = new MTier();
        $this->mTitle       = new MTitle();
    }

    /**
     * 全マスターデータを取得
     */
    protected function getAllMasterData()
    {
        return [
            'uUserList' => $this->uUser->getAllData(),
            'mBadgeList' => $this->mBadge->getAllData(),
            'mDirectionList' => $this->mDirection->getAllData(),
            'mGameDayList' => $this->mGameDay->getAllData(),
            'mRuleList' => $this->mRule->getAllData(),
            'mGroupList' => $this->mGroup->getAllData(),
            'mTierList' => $this->mTier->getAllData(),
            'mTitleList' => $this->mTitle->getAllData(),
            'mSettingList' => $this->mSetting->getAllData(),
            'uGameHistoryList' => $this->uGameHistory->getAllGameHistory(),
            'uGameHistoryListFlat' => $this->uGameHistory->getAllGameHistoryFlat(),
            'uTableList' => $this->uTable->getAllUserTables(),
            'uTierHistoryList' => $this->uTierHistory->getAllTierHistoryFlat(),
            'uTitleList' => $this->uTitle->getAllUserTitles(),
        ];
    }

    /**
     * マスターデータの更新処理
     */
    protected function updateMasterDataByType($type, $id, $data)
    {
        switch ($type) {
            case 'badge':
                return $this->mBadge->updateBadge($id, $data);
            case 'tier':
                return $this->mTier->updateTier($id, $data);
            case 'title':
                return $this->mTitle->updateTitle($id, $data);
            case 'direction':
                return $this->mDirection->updateDirection($id, $data);
            case 'game_day':
                return $this->mGameDay->updateGameDay($id, $data);
            case 'group':
                return $this->mGroup->updateGroup($id, $data);
            case 'rule':
                return $this->mRule->updateRule($id, $data);
                         case 'setting':
                 return $this->mSetting->updateSetting($id, $data);
             case 'tier_history':
                 return $this->uTierHistory->updateTierHistory($id, $data);
             default:
                 throw new Exception("Unknown master data type: {$type}");
        }
    }

    /**
     * マスターデータの削除処理
     */
    protected function deleteMasterDataByType($type, $id)
    {
        switch ($type) {
            case 'badge':
                return $this->mBadge->deleteBadge($id);
            case 'tier':
                return $this->mTier->deleteTier($id);
            case 'title':
                return $this->mTitle->deleteTitle($id);
            case 'direction':
                return $this->mDirection->deleteDirection($id);
            case 'game_day':
                return $this->mGameDay->deleteGameDay($id);
            case 'group':
                return $this->mGroup->deleteGroup($id);
            case 'rule':
                return $this->mRule->deleteRule($id);
                         case 'setting':
                 return $this->mSetting->deleteSetting($id);
             case 'tier_history':
                 return $this->uTierHistory->deleteTierHistory($id);
             default:
                 throw new Exception("Unknown master data type: {$type}");
        }
    }

    /**
     * マスターデータの追加処理
     */
    protected function addMasterDataByType($type, $data)
    {
        error_log('=== addMasterDataByType called ===');
        error_log('Type: ' . $type);
        error_log('Data: ' . print_r($data, true));
        
        switch ($type) {
            case 'badge':
                error_log('Adding badge...');
                return $this->mBadge->addBadge($data);
            case 'tier':
                error_log('Adding tier...');
                return $this->mTier->addTier($data);
            case 'title':
                error_log('Adding title...');
                return $this->mTitle->addTitle($data);
            case 'direction':
                error_log('Adding direction...');
                return $this->mDirection->addDirection($data);
            case 'game_day':
                error_log('Adding game_day...');
                return $this->mGameDay->addGameDay($data);
            case 'group':
                error_log('Adding group...');
                return $this->mGroup->addGroup($data);
            case 'rule':
                error_log('Adding rule...');
                return $this->mRule->addRule($data);
            case 'setting':
                error_log('Adding setting...');
                return $this->mSetting->addSetting($data);
            case 'tier_history':
                error_log('Adding tier_history...');
                return $this->uTierHistory->addTierHistory($data);
            default:
                error_log('Unknown type: ' . $type);
                throw new Exception("Unknown master data type: {$type}");
        }
    }

    /**
     * JSONレスポンスを返す
     */
    protected function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * エラーレスポンスを返す
     */
    protected function errorResponse($message)
    {
        $this->jsonResponse(['success' => false, 'error' => $message]);
    }

    /**
     * 成功レスポンスを返す
     */
    protected function successResponse($data = null)
    {
        $response = ['success' => true];
        if ($data !== null) {
            $response['data'] = $data;
        }
        $this->jsonResponse($response);
    }
}
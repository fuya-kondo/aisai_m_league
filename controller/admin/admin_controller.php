<?php

/**
 * 管理画面コントローラー
 * データベース管理のためのCRUD操作を提供
 */
class AdminController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * メインページを表示
     */
    public function top()
    {
        $data = [
            'title' => '管理画面 - AISAI.M.LEAGUE'
        ];
        include __DIR__ . '/../../view/admin/top.php';
    }

    /**
     * ユーザー管理ページを表示
     */
    public function user()
    {
        $masterData = $this->getAllMasterData();
  
        // ユーザーデータにバッジとティアの情報を関連付け
        $users = $masterData['uUserList'];
        $badges = $masterData['mBadgeList'];
        $tiers = $masterData['mTierList'];
        
        // 各ユーザーにバッジとティアの詳細情報を追加
        $enrichedUsers = [];
        foreach ($users as $userId => $user) {
            $enrichedUser = $user;
            
            // バッジ情報を追加
            if (!empty($user['m_badge_id']) && isset($badges[$user['m_badge_id']])) {
                $enrichedUser['badge'] = $badges[$user['m_badge_id']];
            }
            
            // ティア情報を追加
            if (!empty($user['m_tier_id']) && isset($tiers[$user['m_tier_id']])) {
                $enrichedUser['tier'] = $tiers[$user['m_tier_id']];
            }
            
            $enrichedUsers[$userId] = $enrichedUser;
        }
        
        $users = $enrichedUsers;
        
        $data = [
            'title' => 'ユーザー管理 - AISAI.M.LEAGUE',
            'users' => $users,
            'badges' => $badges,
            'tiers' => $tiers
        ];
        
        include __DIR__ . '/../../view/admin/user_management.php';
    }

    /**
     * ゲーム履歴管理ページを表示
     */
    public function history()
    {
        $masterData = $this->getAllMasterData();
        
        $data = [
            'title' => 'ゲーム履歴管理 - AISAI.M.LEAGUE',
            'gameHistory' => $masterData['uGameHistoryListFlat'],
            'users' => $masterData['uUserList'],
            'tables' => $masterData['uTableList'],
            'gameDays' => $masterData['mGameDayList'],
            'directions' => $masterData['mDirectionList']
        ];
        
        include __DIR__ . '/../../view/admin/game_history_management.php';
    }

    /**
     * マスターデータ管理ページを表示
     */
    public function master()
    {
        $masterData = $this->getAllMasterData();
        
        $data = [
            'title' => 'マスターデータ管理 - AISAI.M.LEAGUE',
            'badges' => $masterData['mBadgeList'],
            'tiers' => $masterData['mTierList'],
            'directions' => $masterData['mDirectionList'],
            'gameDays' => $masterData['mGameDayList'],
            'groups' => $masterData['mGroupList'],
            'rules' => $masterData['mRuleList'],
            'settings' => $masterData['mSettingList'],
            'tierHistory' => $masterData['uTierHistoryList']
        ];
        
        include __DIR__ . '/../../view/admin/master_data_management.php';
    }

    /**
     * ユーザー情報を更新
     */
    public function updateUser()
    {
        error_log('=== updateUser called ===');
        error_log('REQUEST_METHOD: ' . $_SERVER['REQUEST_METHOD']);
        error_log('POST data: ' . print_r($_POST, true));
        error_log('GET data: ' . print_r($_GET, true));
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $userId = $_POST['user_id'];
                $data = [
                    'last_name' => $_POST['last_name'],
                    'first_name' => $_POST['first_name'],
                    'm_badge_id' => !empty($_POST['badge_id']) ? $_POST['badge_id'] : 0,
                    'm_tier_id' => !empty($_POST['tier_id']) ? $_POST['tier_id'] : 0
                ];

                error_log('Updating user ID: ' . $userId);
                error_log('Update data: ' . print_r($data, true));

                $result = $this->uUser->updateUser($userId, $data);
                error_log('Update result: ' . ($result ? 'success' : 'failed'));
                
                $this->successResponse(['message' => 'ユーザー情報を更新しました']);
            } catch (Exception $e) {
                error_log('Update error: ' . $e->getMessage());
                $this->errorResponse('ユーザー情報の更新に失敗しました: ' . $e->getMessage());
            }
        } else {
            error_log('Invalid request method: ' . $_SERVER['REQUEST_METHOD']);
            $this->errorResponse('不正なリクエストです');
        }
    }

    /**
     * ユーザーを追加
     */
    public function addUser()
    {
        error_log('addUser called');
        error_log('POST data: ' . print_r($_POST, true));
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'last_name' => $_POST['last_name'],
                    'first_name' => $_POST['first_name'],
                    'm_badge_id' => !empty($_POST['badge_id']) ? $_POST['badge_id'] : 0,
                    'm_tier_id' => !empty($_POST['tier_id']) ? $_POST['tier_id'] : 0
                ];

                error_log('Adding user with data: ' . print_r($data, true));

                $result = $this->uUser->addUser($data);
                error_log('Add result: ' . ($result ? 'success' : 'failed'));
                
                $this->successResponse(['message' => 'ユーザーを追加しました']);
            } catch (Exception $e) {
                error_log('Add error: ' . $e->getMessage());
                $this->errorResponse('ユーザーの追加に失敗しました: ' . $e->getMessage());
            }
        } else {
            error_log('Invalid request method: ' . $_SERVER['REQUEST_METHOD']);
            $this->errorResponse('不正なリクエストです');
        }
    }

    /**
     * ユーザーを削除
     */
    public function deleteUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $userId = $_POST['user_id'];
                $result = $this->uUser->deleteUser($userId);
                $this->successResponse(['message' => 'ユーザーを削除しました']);
            } catch (Exception $e) {
                $this->errorResponse('ユーザーの削除に失敗しました: ' . $e->getMessage());
            }
        } else {
            $this->errorResponse('不正なリクエストです');
        }
    }

    /**
     * ゲーム履歴を更新
     */
    public function updateGameHistory()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $gameId = $_POST['game_id'];
            
            // 日付と時刻を結合
            $playTime = !empty($_POST['play_time']) ? $_POST['play_time'] : '00:00';
            $playDate = $_POST['play_date'] . ' ' . $playTime . ':00';
            
            $data = [
                'play_date' => $playDate,
                'game' => $_POST['game'],
                'u_user_id' => $_POST['u_user_id'],
                'u_table_id' => 1, // 固定値として1を設定
                'rank' => $_POST['rank'],
                'score' => $_POST['score'],
                'm_direction_id' => !empty($_POST['m_direction_id']) ? $_POST['m_direction_id'] : 0,
                'mistake_count' => !empty($_POST['mistake_count']) ? (int)$_POST['mistake_count'] : 0
            ];

            try {
                $result = $this->uGameHistory->updateGameHistory($gameId, $data);
                $this->successResponse(['message' => 'ゲーム履歴を更新しました']);
            } catch (Exception $e) {
                $this->errorResponse('ゲーム履歴の更新に失敗しました: ' . $e->getMessage());
            }
        } else {
            $this->errorResponse('不正なリクエストです');
        }
    }

    /**
     * ゲーム履歴を追加
     */
    public function addGameHistory()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 日付と時刻を結合
            $playTime = !empty($_POST['play_time']) ? $_POST['play_time'] : '00:00';
            $playDate = $_POST['play_date'] . ' ' . $playTime . ':00';
            
            $data = [
                'play_date' => $playDate,
                'game' => $_POST['game'],
                'u_user_id' => $_POST['u_user_id'],
                'u_table_id' => 1, // 固定値として1を設定
                'rank' => $_POST['rank'],
                'score' => $_POST['score'],
                'm_direction_id' => !empty($_POST['m_direction_id']) ? $_POST['m_direction_id'] : 0,
                'mistake_count' => !empty($_POST['mistake_count']) ? (int)$_POST['mistake_count'] : 0
            ];

            try {
                $result = $this->uGameHistory->addGameHistory($data);
                $this->successResponse(['message' => 'ゲーム履歴を追加しました']);
            } catch (Exception $e) {
                $this->errorResponse('ゲーム履歴の追加に失敗しました: ' . $e->getMessage());
            }
        } else {
            $this->errorResponse('不正なリクエストです');
        }
    }

    /**
     * マスターデータを更新
     */
    public function updateMasterData()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type = $_POST['type'];
            $id = $_POST['id'];
            $data = json_decode($_POST['data'], true);

            try {
                $result = $this->updateMasterDataByType($type, $id, $data);
                $this->successResponse($result);
            } catch (Exception $e) {
                $this->errorResponse($e->getMessage());
            }
        }
    }

    /**
     * データを削除
     */
    public function deleteData()
    {
        error_log('=== deleteData called ===');
        error_log('REQUEST_METHOD: ' . $_SERVER['REQUEST_METHOD']);
        error_log('POST data: ' . print_r($_POST, true));
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type = $_POST['type'];
            $id = $_POST['id'];

            error_log('Deleting type: ' . $type . ', ID: ' . $id);

            try {
                $result = false;
                switch ($type) {
                    case 'user':
                        $result = $this->uUser->deleteUser($id);
                        break;
                    case 'game_history':
                        $result = $this->uGameHistory->deleteGameHistory($id);
                        break;
                    default:
                        $result = $this->deleteMasterDataByType($type, $id);
                        break;
                }

                error_log('Delete result: ' . ($result ? 'success' : 'failed'));
                $this->successResponse(['message' => 'データを削除しました']);
            } catch (Exception $e) {
                error_log('Delete error: ' . $e->getMessage());
                $this->errorResponse('データの削除に失敗しました: ' . $e->getMessage());
            }
        } else {
            $this->errorResponse('不正なリクエストです');
        }
    }

    /**
     * 新しいマスターデータを追加
     */
    public function addMasterData()
    {
        error_log('=== addMasterData called ===');
        error_log('REQUEST_METHOD: ' . $_SERVER['REQUEST_METHOD']);
        error_log('POST data: ' . print_r($_POST, true));
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type = $_POST['type'];
            $data = json_decode($_POST['data'], true);

            error_log('Type: ' . $type);
            error_log('Decoded data: ' . print_r($data, true));

            try {
                $result = $this->addMasterDataByType($type, $data);
                error_log('Add result: ' . ($result ? 'success' : 'failed'));
                $this->successResponse(['message' => 'データを追加しました']);
            } catch (Exception $e) {
                error_log('Add error: ' . $e->getMessage());
                $this->errorResponse($e->getMessage());
            }
        } else {
            error_log('Invalid request method: ' . $_SERVER['REQUEST_METHOD']);
            $this->errorResponse('不正なリクエストです');
        }
    }

    /**
     * ユーザー数を取得
     */
    private function getUserCount()
    {
        $users = $this->uUser->getAllUsers();
        return count($users);
    }

    /**
     * ゲーム数を取得
     */
    private function getGameCount()
    {
        $gameHistory = $this->uGameHistory->getAllGameHistory();
        return count($gameHistory);
    }

    /**
     * ティア履歴数を取得
     */
    private function getTierCount()
    {
        $tierHistory = $this->uTierHistory->getAllTierHistory();
        $count = 0;
        foreach ($tierHistory as $userHistory) {
            $count += count($userHistory);
        }
        return $count;
    }

    /**
     * バッジ数を取得
     */
    private function getBadgeCount()
    {
        $badges = $this->mBadge->getAllBadges();
        return count($badges);
    }
}

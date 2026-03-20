<?php
/**
 * 管理画面の各機能をまとめるコントローラー。
 * ユーザー・対局履歴・マスターデータの CRUD 画面と API 応答を担当する。
 */
class AdminController extends BaseController
{
    public function top(): void
    {
        $this->renderView(__DIR__ . '/../../view/admin/top.php', [
            'pageData' => ['title' => '管理画面 - AISAI.M.LEAGUE'],
        ]);
    }

    public function user(): void
    {
        $masterData = $this->getAdminMasterData();
        $pageData = [
            'title' => 'ユーザー管理 - AISAI.M.LEAGUE',
            'users' => $this->userService->attachBadgeAndTier($masterData['uUserList'], $masterData['mBadgeList'], $masterData['mTierList']),
            'badges' => $masterData['mBadgeList'],
            'tiers' => $masterData['mTierList'],
        ];

        $this->renderView(__DIR__ . '/../../view/admin/user_management.php', ['pageData' => $pageData]);
    }

    public function history(): void
    {
        $masterData = $this->getAdminMasterData();
        $pageData = [
            'title' => 'ゲーム履歴管理 - AISAI.M.LEAGUE',
            'gameHistory' => $masterData['uGameHistoryListFlat'],
            'users' => $masterData['uUserList'],
            'tables' => $masterData['uTableList'],
            'gameDays' => $masterData['mGameDayList'],
            'directions' => $masterData['mDirectionList'],
        ];

        $this->renderView(__DIR__ . '/../../view/admin/game_history_management.php', ['pageData' => $pageData]);
    }

    public function master(): void
    {
        $masterData = $this->getAdminMasterData();
        $pageData = [
            'title' => 'マスターデータ管理 - AISAI.M.LEAGUE',
            'badges' => $masterData['mBadgeList'],
            'tiers' => $masterData['mTierList'],
            'directions' => $masterData['mDirectionList'],
            'gameDays' => $masterData['mGameDayList'],
            'groups' => $masterData['mGroupList'],
            'rules' => $masterData['mRuleList'],
            'settings' => $masterData['mSettingList'],
            'tierHistory' => $masterData['uTierHistoryList'],
            'masterSections' => \App\Support\Admin\MasterDataViewConfig::buildSections([
                'badges' => $masterData['mBadgeList'],
                'tiers' => $masterData['mTierList'],
                'directions' => $masterData['mDirectionList'],
                'gameDays' => $masterData['mGameDayList'],
                'groups' => $masterData['mGroupList'],
                'rules' => $masterData['mRuleList'],
                'settings' => $masterData['mSettingList'],
                'tierHistory' => $masterData['uTierHistoryList'],
            ]),
            'masterForms' => \App\Support\Admin\MasterDataViewConfig::buildForms([
                'tierHistory' => $masterData['uTierHistoryList'],
            ]),
        ];

        $this->renderView(__DIR__ . '/../../view/admin/master_data_management.php', ['pageData' => $pageData]);
    }

    public function updateUser(): void
    {
        $this->handlePostAction(function (): void {
            $userId = adminReadRequiredInt($_POST, 'user_id', 'ユーザーID', 1);
            $this->userService->update($userId, $this->buildUserPayload($_POST));
            $this->successResponse('ユーザー情報を更新しました');
        }, 'ユーザー情報の更新に失敗しました');
    }

    public function addUser(): void
    {
        $this->handlePostAction(function (): void {
            $this->userService->create($this->buildUserPayload($_POST));
            $this->successResponse('ユーザーを追加しました');
        }, 'ユーザーの追加に失敗しました');
    }

    public function deleteUser(): void
    {
        $this->handlePostAction(function (): void {
            $userId = adminReadRequiredInt($_POST, 'user_id', 'ユーザーID', 1);
            $this->userService->delete($userId);
            $this->successResponse('ユーザーを削除しました');
        }, 'ユーザーの削除に失敗しました');
    }

    public function updateGameHistory(): void
    {
        $this->handlePostAction(function (): void {
            $gameHistoryId = adminReadRequiredInt($_POST, 'game_id', 'ゲーム履歴ID', 1);
            if (!$this->gameHistoryService->update($gameHistoryId, $this->buildGameHistoryPayload($_POST))) {
                throw new RuntimeException('ゲーム履歴を更新できませんでした。');
            }
            $this->successResponse('ゲーム履歴を更新しました');
        }, 'ゲーム履歴の更新に失敗しました');
    }

    public function addGameHistory(): void
    {
        $this->handlePostAction(function (): void {
            if (!$this->gameHistoryService->create($this->buildGameHistoryPayload($_POST))) {
                throw new RuntimeException('ゲーム履歴を追加できませんでした。');
            }
            $this->successResponse('ゲーム履歴を追加しました');
        }, 'ゲーム履歴の追加に失敗しました');
    }

    public function updateMasterData(): void
    {
        $this->handlePostAction(function (): void {
            $type = adminReadRequiredString($_POST, 'type', '種別');
            $id = adminReadRequiredString($_POST, 'id', 'ID');
            $payload = adminDecodePostedJson($_POST);
            $result = $this->masterDataService->update($type, $id, $payload);
            $this->successResponse('データを更新しました', $result);
        }, null);
    }

    public function deleteData(): void
    {
        $this->handlePostAction(function (): void {
            $type = adminReadRequiredString($_POST, 'type', '種別');
            $id = adminReadRequiredString($_POST, 'id', 'ID');

            if ($type === 'user') {
                $this->userService->delete((int) $id);
            } elseif ($type === 'game_history') {
                if (!$this->gameHistoryService->delete((int) $id)) {
                    throw new RuntimeException('ゲーム履歴を削除できませんでした。');
                }
            } else {
                $this->masterDataService->delete($type, $id);
            }

            $this->successResponse('データを削除しました');
        }, 'データの削除に失敗しました');
    }

    public function addMasterData(): void
    {
        $this->handlePostAction(function (): void {
            $type = adminReadRequiredString($_POST, 'type', '種別');
            $payload = adminDecodePostedJson($_POST);
            $result = $this->masterDataService->create($type, $payload);
            $this->successResponse('データを追加しました', $result);
        }, null);
    }

    private function handlePostAction(callable $action, ?string $errorPrefix): void
    {
        try {
            adminEnsurePostRequest($_SERVER['REQUEST_METHOD'] ?? '');
            $action();
        } catch (Exception $exception) {
            $message = $errorPrefix === null ? $exception->getMessage() : $errorPrefix . ': ' . $exception->getMessage();
            $this->errorResponse($message);
        }
    }

    private function buildUserPayload(array $postData): array
    {
        return [
            'last_name' => adminReadRequiredString($postData, 'last_name', '姓'),
            'first_name' => adminReadRequiredString($postData, 'first_name', '名'),
            'm_badge_id' => adminReadOptionalInt($postData, 'badge_id', 0),
            'm_tier_id' => adminReadOptionalInt($postData, 'tier_id', 0),
        ];
    }

    private function buildGameHistoryPayload(array $postData): array
    {
        return [
            'play_date' => adminBuildPlayDateTime($postData),
            'game' => adminReadRequiredInt($postData, 'game', '試合番号', 1),
            'u_user_id' => adminReadRequiredInt($postData, 'u_user_id', 'ユーザー', 1),
            'u_table_id' => \App\Support\Constants\AppConstants::AGGREGATE_TABLE_ID,
            'rank' => adminReadRequiredString($postData, 'rank', '順位'),
            'score' => adminReadRequiredString($postData, 'score', 'スコア'),
            'm_direction_id' => adminReadOptionalInt($postData, 'm_direction_id', 0),
            'mistake_count' => adminReadOptionalInt($postData, 'mistake_count', 0),
        ];
    }
}

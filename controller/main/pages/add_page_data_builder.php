<?php
/**
 * 単独登録画面専用の view data builder。
 * 登録フォームに必要な選択肢と初期表示情報をまとめる。
 */
class AddPageDataBuilder extends MainPageDataBuilder
{
    public function build(?string $errorMessage): array
    {
        $userList = $this->statsService->getUserList();
        $todayStatsList = $this->statsService->getTodayStatsList();
        $games = array_column($todayStatsList, 'play_count', 'user_id');

        return $this->withTitle('登録', [
            'pageTabs' => $this->buildRegistrationTabs('single'),
            'error_msg' => $errorMessage,
            'userList' => $userList,
            'mDirectionList' => $this->getDirectionList(),
            'rankConfig' => $this->getRankConfig(),
            'todayStatsList' => $todayStatsList,
            'games' => $games,
        ]);
    }

    public function buildPayload(array $postData): array
    {
        return [
            'userId' => (int)$postData['userId'],
            'tableId' => (int)$postData['tableId'],
            'game' => (int)$postData['game'],
            'direction' => (int)$postData['direction'],
            'rank' => (string)$postData['rank'],
            'score' => (int)$postData['score'],
            'mistakeCount' => mainReadMistakeCount($postData),
            'playDate' => mainBuildPostedDateTime($postData),
        ];
    }

    private function buildRegistrationTabs(string $activeTab): array
    {
        return [
            ['label' => '一括登録', 'href' => 'bulk-add', 'active' => $activeTab === 'bulk'],
            ['label' => '個別登録', 'href' => 'add', 'active' => $activeTab === 'single'],
        ];
    }
}

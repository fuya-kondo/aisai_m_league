<?php
/**
 * 履歴修正画面専用の view data builder。
 * 初期表示値と更新後リダイレクト判定をまとめて扱う。
 */
class UpdatePageDataBuilder extends MainPageDataBuilder
{
    public function build(array $postData, string $requestMethod): array
    {
        $historyId = isset($postData['historyId']) ? (int)$postData['historyId'] : 0;
        $viewData = $this->withTitle('修正', [
            'pageTabs' => $this->buildRegistrationTabs('single'),
            'redirectUrl' => null,
            'error_msg' => null,
            'historyId' => $historyId,
            'formData' => $this->buildEmptyFormData(),
            'userList' => $this->statsService->getUserList(),
            'mDirectionList' => $this->getDirectionList(),
            'rankConfig' => $this->getRankConfig(),
        ]);

        if ($requestMethod !== 'POST' || $historyId < 1) {
            return $viewData;
        }

        if ($this->isUpdateSubmission($postData)) {
            $viewData['formData'] = $this->buildSubmittedFormData($postData);
            $existingHistory = $this->gameHistoryService->find($historyId);
            if ($existingHistory === null) {
                $viewData['error_msg'] = '更新対象の履歴が見つかりません。';
                return $viewData;
            }

            $lockedUserId = (int)($existingHistory['u_user_id'] ?? 0);
            $updated = $this->gameHistoryService->update($historyId, [
                'userId' => $lockedUserId,
                'tableId' => (int)$postData['tableId'],
                'game' => (int)$postData['game'],
                'direction' => (int)$postData['direction'],
                'rank' => (string)$postData['rank'],
                'score' => (int)$postData['score'],
                'mistakeCount' => mainReadMistakeCount($postData),
                'playDate' => mainBuildPostedDateTime($postData),
            ]);

            if ($updated) {
                $viewData['redirectUrl'] = 'history?userId=' . urlencode((string)$lockedUserId);
                return $viewData;
            }

            $viewData['error_msg'] = '更新処理中にエラーが発生しました。';
            return $viewData;
        }

        $history = $this->gameHistoryService->find($historyId);
        if ($history === null) {
            $viewData['error_msg'] = '更新対象の履歴が見つかりません。';
            return $viewData;
        }

        $viewData['formData'] = $this->buildFormDataFromHistory($history);
        return $viewData;
    }

    private function isUpdateSubmission(array $postData): bool
    {
        return isset(
            $postData['userId'],
            $postData['tableId'],
            $postData['game'],
            $postData['direction'],
            $postData['rank'],
            $postData['score'],
            $postData['year'],
            $postData['month'],
            $postData['day']
        );
    }

    private function buildFormDataFromHistory(array $history): array
    {
        $timestamp = strtotime((string)($history['play_date'] ?? '')) ?: time();

        return [
            'userId' => (string)($history['u_user_id'] ?? ''),
            'tableId' => (string)($history['u_table_id'] ?? ''),
            'game' => (string)($history['game'] ?? ''),
            'direction' => (string)($history['m_direction_id'] ?? ''),
            'rank' => (string)($history['rank'] ?? ''),
            'score' => (string)($history['score'] ?? ''),
            'mistakeCount' => (string)($history['mistake_count'] ?? '0'),
            'year' => (string)date('Y', $timestamp),
            'month' => (string)date('n', $timestamp),
            'day' => (string)date('j', $timestamp),
        ];
    }

    private function buildSubmittedFormData(array $postData): array
    {
        return [
            'userId' => (string)($postData['userId'] ?? ''),
            'tableId' => (string)($postData['tableId'] ?? ''),
            'game' => (string)($postData['game'] ?? ''),
            'direction' => (string)($postData['direction'] ?? ''),
            'rank' => (string)($postData['rank'] ?? ''),
            'score' => (string)($postData['score'] ?? ''),
            'mistakeCount' => (string)($postData['mistake_count'] ?? '0'),
            'year' => (string)($postData['year'] ?? date('Y')),
            'month' => (string)($postData['month'] ?? date('n')),
            'day' => (string)($postData['day'] ?? date('j')),
        ];
    }

    private function buildEmptyFormData(): array
    {
        return [
            'userId' => '',
            'tableId' => '',
            'game' => '',
            'direction' => '',
            'rank' => '',
            'score' => '',
            'mistakeCount' => '0',
            'year' => (string)date('Y'),
            'month' => (string)date('n'),
            'day' => (string)date('j'),
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

<?php
/**
 * AI分析履歴詳細ページ用の view data builder。
 * 保存済み分析結果の取得と表示用整形を担当する。
 */
class AnalysisHistoryDetailPageDataBuilder extends MainPageDataBuilder
{
    public function build(int $historyId, ?string $selectedUser): array
    {
        $history = $historyId > 0 ? $this->aiAnalysisHistoryService->findById($historyId) : null;
        $errorMessage = null;

        if ($history === null) {
            $errorMessage = '分析履歴が見つかりません。';
        } elseif ($selectedUser !== null && (int)$selectedUser > 0 && (int)($history['u_user_id'] ?? 0) !== (int)$selectedUser) {
            $errorMessage = '指定された選手の分析履歴ではありません。';
        }

        $userList = $this->statsService->getUserList();
        $historyUserId = (int)($history['u_user_id'] ?? 0);
        $playerName = isset($userList[$historyUserId])
            ? (string)($userList[$historyUserId]['last_name'] ?? '') . (string)($userList[$historyUserId]['first_name'] ?? '')
            : null;

        return $this->withTitle('AI分析履歴', [
            'analysisHistory' => $history,
            'analysisHistoryError' => $errorMessage,
            'analysisHistoryPlayerName' => $playerName,
            'analysisHistoryGeneratedAt' => $this->formatGeneratedAt((string)($history['generated_at'] ?? '')),
            'analysisHistoryBackHref' => $history !== null ? 'analysis?userId=' . urlencode((string)$historyUserId) . '&term=' . urlencode((string)($history['term'] ?? '')) : 'analysis',
        ]);
    }

    private function formatGeneratedAt(string $generatedAt): string
    {
        $timestamp = strtotime($generatedAt);
        if ($timestamp === false) {
            return $generatedAt;
        }

        return date('Y/m/d H:i', $timestamp);
    }
}

<?php
/**
 * AI 分析画面専用の view data builder。
 * 期間・選手選択、分析プロンプト生成、Gemini 応答の整形を担う。
 */
class AnalysisPageDataBuilder extends MainPageDataBuilder
{
    public function build(?string $selectedUser, ?string $selectedTerm, bool $shouldRun): array
    {
        $userList = $this->statsService->getUserList();
        $directionList = $this->getDirectionList();
        $analysisTerms = $this->statsService->getAnalysisTerms();
        $selectedTerm = $this->resolveSelectedTerm($selectedTerm, $analysisTerms);
        $analysisResultHtml = null;
        $analysisError = null;
        $analysisHistoryItems = [];

        if ($selectedUser !== null && isset($userList[$selectedUser])) {
            $analysisHistoryItems = $this->buildAnalysisHistoryItems((int)$selectedUser);
        }

        if ($shouldRun) {
            if ($selectedUser === null || !isset($userList[$selectedUser])) {
                $analysisError = '選手を選択してください。';
            } elseif (!$this->statsService->isValidAnalysisTerm($selectedTerm)) {
                $analysisError = '選択された期間が不正です。';
            } else {
                $analysisDataList = $this->statsService->getAnalysisDataByTerm($selectedTerm);
                $statsNameConfig = $this->statsColumn['statsNameConfig'] ?? [];

                if (!isset($analysisDataList[$selectedUser])) {
                    $analysisError = '選択された選手のデータが見つかりません。';
                } else {
                    $analysisPrompt = $this->buildAnalysisPrompt(
                        (int)$selectedUser,
                        $selectedTerm,
                        $userList,
                        $analysisDataList,
                        $directionList,
                        $statsNameConfig
                    );
                    $analysisResultHtml = $this->requestAnalysisHtml((int)$selectedUser, $selectedTerm, $analysisPrompt, $analysisError);
                    $analysisHistoryItems = $this->buildAnalysisHistoryItems((int)$selectedUser);
                }
            }
        }

        return $this->withTitle('AI成績分析', [
            'selectedUser' => $selectedUser,
            'selectedTerm' => $selectedTerm,
            'shouldRun' => $shouldRun,
            'userList' => $userList,
            'termOptions' => $this->buildTermOptions($analysisTerms, $selectedTerm),
            'userOptions' => $this->buildUserOptions($userList, $selectedUser),
            'analysisResultHtml' => $analysisResultHtml,
            'analysisError' => $analysisError,
            'analysisHistoryItems' => $analysisHistoryItems,
            'selectedTermLabel' => $selectedTerm,
            'selectedUserName' => $selectedUser !== null && isset($userList[$selectedUser])
                ? $userList[$selectedUser]['last_name'] . $userList[$selectedUser]['first_name']
                : null,
        ]);
    }

    private function resolveSelectedTerm(?string $selectedTerm, array $analysisTerms): string
    {
        if ($selectedTerm !== null && in_array($selectedTerm, $analysisTerms, true)) {
            return $selectedTerm;
        }

        return \App\Support\Constants\AppConstants::ALL_TERM_LABEL;
    }

    private function buildTermOptions(array $analysisTerms, string $selectedTerm): array
    {
        $options = [];
        foreach ($analysisTerms as $term) {
            $options[] = [
                'value' => $term,
                'label' => $term,
                'active' => $term === $selectedTerm,
            ];
        }

        return $options;
    }

    private function buildUserOptions(array $userList, ?string $selectedUser): array
    {
        $options = [];
        foreach ($userList as $userId => $userData) {
            $options[] = [
                'value' => (string)$userId,
                'label' => $userData['last_name'] . $userData['first_name'],
                'active' => (string)$userId === (string)$selectedUser,
            ];
        }

        return $options;
    }

    private function buildAnalysisPrompt(
        int $selectedUser,
        string $selectedTerm,
        array $userList,
        array $analysisDataList,
        array $directionList,
        array $statsNameConfig
    ): string {
        $playerName = $userList[$selectedUser]['last_name'] . $userList[$selectedUser]['first_name'];
        $playerAnalysisData = $analysisDataList[$selectedUser];
        $includedHistoryCount = (int)($playerAnalysisData['history_included_count'] ?? 0);
        $totalHistoryCount = (int)($playerAnalysisData['history_total_count'] ?? 0);

        $instruction = <<<EOT
麻雀の成績データを元に、指定された選手の分析を行ってください。
出力はHTMLのみで返してください。Markdownのコードフェンスは不要です。

分析対象:
- 選手名: {$playerName}
- 対象期間: {$selectedTerm}

出力要件:
- 総合評価
- 良い傾向
- 課題
- 改善提案
- 最近の傾向と長期傾向の差

注意点:
- 合計ポイントを最重要指標として扱ってください。
- 各家別成績はサンプル数が少ない場合、断定を避けてください。
- 与えられた過去試合履歴を使って、順位・点数・pt・チョンボの傾向を具体的に見てください。
- 500件を超える履歴がある場合は、直近500件のみを分析対象として扱ってください。
- 400〜700文字程度で簡潔にまとめ、見出しや箇条書きを含む読みやすいHTMLにしてください。
EOT;

        $summaryBlock = "## 概要集計\n";
        foreach ([
            'play_count',
            'sum_point',
            'average_point',
            'average_rank',
            'sum_base_score',
            'average_score',
            'hight_score',
            'over_second_probability',
            'over_third_probability',
            'mistake_count',
        ] as $key) {
            if (isset($playerAnalysisData[$key], $statsNameConfig[$key])) {
                $summaryBlock .= $statsNameConfig[$key] . ': ' . $this->formatAnalysisValue($playerAnalysisData[$key]) . "\n";
            }
        }
        $summaryBlock .= "\n";

        $rankBlock = "## 順位傾向\n";
        foreach (($playerAnalysisData['rank_count'] ?? []) as $rank => $count) {
            $probability = $playerAnalysisData['rank_probability'][$rank] ?? 0;
            $rankBlock .= sprintf(
                '%s着: %s回 / %s',
                $rank,
                $this->formatAnalysisValue($count),
                $this->formatAnalysisValue($probability)
            ) . "\n";
        }
        $rankBlock .= "\n";

        $directionBlock = "## 席別傾向\n";
        foreach ($directionList as $directionId => $directionData) {
            $playCount = $playerAnalysisData['play_count_direction'][$directionId] ?? 0;
            if ((int)$playCount === 0) {
                continue;
            }

            $directionBlock .= '### ' . $directionData['name'] . "\n";
            $directionBlock .= '対局数: ' . $this->formatAnalysisValue($playCount) . "\n";
            $directionBlock .= '平均順位: ' . $this->formatAnalysisValue($playerAnalysisData['average_rank_direction'][$directionId] ?? 0) . "\n";
            $directionBlock .= '平均点数: ' . $this->formatAnalysisValue($playerAnalysisData['average_score_direction'][$directionId] ?? 0) . "\n";
            $directionBlock .= '平均pt: ' . $this->formatAnalysisValue($playerAnalysisData['average_point_direction'][$directionId] ?? 0) . "\n";

            foreach (($playerAnalysisData['rank_count_direction'][$directionId] ?? []) as $rank => $count) {
                $probability = $playerAnalysisData['rank_probability_direction'][$directionId][$rank] ?? 0;
                $directionBlock .= sprintf(
                    '%s着: %s回 / %s',
                    $rank,
                    $this->formatAnalysisValue($count),
                    $this->formatAnalysisValue($probability)
                ) . "\n";
            }
            $directionBlock .= "\n";
        }

        $historyBlock = "## 過去試合履歴\n";
        $historyBlock .= sprintf(
            "分析対象履歴: %d件 / 対象期間全履歴: %d件\n",
            $includedHistoryCount,
            $totalHistoryCount
        );
        if ($totalHistoryCount > $includedHistoryCount) {
            $historyBlock .= "注記: 直近500件のみを使用\n";
        }

        foreach (($playerAnalysisData['recent_games'] ?? []) as $historyRow) {
            $historyBlock .= sprintf(
                "%s %d半荘目 %s %s着 素点:%d pt:%s チョンボ:%d\n",
                $historyRow['play_date'] ?? '',
                (int)($historyRow['game'] ?? 0),
                $historyRow['direction'] ?? '',
                $historyRow['rank'] ?? '',
                (int)($historyRow['score'] ?? 0),
                $this->formatAnalysisValue($historyRow['point'] ?? 0),
                (int)($historyRow['mistake_count'] ?? 0)
            );
        }

        return implode("\n", [
            $instruction,
            $summaryBlock,
            $rankBlock,
            $directionBlock,
            $historyBlock,
        ]);
    }

    private function formatAnalysisValue($value): string
    {
        if (is_array($value)) {
            return implode(', ', array_map([$this, 'formatAnalysisValue'], $value));
        }

        if (is_float($value)) {
            return number_format($value, 1);
        }

        return (string)$value;
    }

    private function sanitizeAnalysisHtml(string $html): string
    {
        $sanitized = str_replace(['```html', '```'], '', $html);
        $sanitized = preg_replace('/<!DOCTYPE[^>]*>/i', '', $sanitized) ?? $sanitized;
        $sanitized = preg_replace('/<\/?(?:html|head|body|title)[^>]*>/i', '', $sanitized) ?? $sanitized;
        $sanitized = preg_replace('/<(?:meta|link)[^>]*>/i', '', $sanitized) ?? $sanitized;
        $sanitized = preg_replace('/<style\b[^>]*>[\s\S]*?<\/style>/i', '', $sanitized) ?? $sanitized;
        $sanitized = preg_replace('/<script\b[^>]*>[\s\S]*?<\/script>/i', '', $sanitized) ?? $sanitized;
        $sanitized = preg_replace('/\sstyle=("[^"]*"|\'[^\']*\')/i', '', $sanitized) ?? $sanitized;

        return trim($sanitized);
    }

    private function requestAnalysisHtml(int $selectedUser, string $selectedTerm, string $analysisPrompt, ?string &$errorMessage): ?string
    {
        $responseText = $this->geminiTextGenerationService->generateText(
            $analysisPrompt,
            'gemini-2.5-flash',
            $errorMessage
        );
        if ($responseText === null) {
            $this->aiAnalysisHistoryService->saveFailure(
                $selectedUser,
                $selectedTerm,
                'gemini-2.5-flash',
                $analysisPrompt,
                $errorMessage ?? 'AI分析に失敗しました。'
            );
            return null;
        }

        $sanitizedHtml = $this->sanitizeAnalysisHtml($responseText);
        $this->aiAnalysisHistoryService->saveSuccess(
            $selectedUser,
            $selectedTerm,
            'gemini-2.5-flash',
            $analysisPrompt,
            $sanitizedHtml
        );

        return $sanitizedHtml;
    }

    private function buildAnalysisHistoryItems(int $selectedUser): array
    {
        $items = [];
        foreach ($this->aiAnalysisHistoryService->findRecentSuccessfulByUser($selectedUser, 3) as $historyRow) {
            $items[] = [
                'historyId' => (int)($historyRow['u_ai_analysis_history_id'] ?? 0),
                'term' => (string)($historyRow['term'] ?? ''),
                'statusLabel' => '成功',
                'generatedAt' => $this->formatGeneratedAt((string)($historyRow['generated_at'] ?? '')),
                'detailHref' => 'analysis-history?historyId=' . urlencode((string)($historyRow['u_ai_analysis_history_id'] ?? 0)) . '&userId=' . urlencode((string)$selectedUser),
            ];
        }

        return $items;
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


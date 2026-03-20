<?php
/**
 * 一括修正画面専用の page data builder。
 * 半荘4件の初期表示、検証、バッチ更新用 payload 生成を担当する。
 */
class BulkUpdatePageDataBuilder extends MainPageDataBuilder
{
    public function build(array $queryParams, array $postData, string $requestMethod): array
    {
        $userList = $this->statsService->getUserList();
        $directionList = $this->getDirectionList();
        $errorMessages = [];
        $redirectUrl = null;

        $tableId = (int)($postData['tableId'] ?? $queryParams['tableId'] ?? 0);
        $game = (int)($postData['game'] ?? $queryParams['game'] ?? 0);
        $playDateYmd = (string)($postData['playDate'] ?? $queryParams['playDate'] ?? '');
        $historyIds = $this->readHistoryIds($postData, $queryParams);
        $timePart = (string)($postData['timePart'] ?? $queryParams['timePart'] ?? date('H:i:s'));

        $batchRows = [];
        if ($tableId > 0 && $game > 0 && $playDateYmd !== '') {
            $batchRows = $this->gameHistoryService->findBatch($tableId, $playDateYmd, $game);
        }

        if ($requestMethod !== 'POST' && count($batchRows) !== \App\Support\Constants\AppConstants::PLAYER_COUNT) {
            $errorMessages[] = '対象半荘の4件データを取得できませんでした。';
        }

        if ($timePart === '' && !empty($batchRows)) {
            $timePart = date('H:i:s', strtotime((string)$batchRows[0]['play_date']));
        }

        $formData = $this->buildFormData($tableId, $playDateYmd, $game, $historyIds, $batchRows, $timePart);
        $scoreTotalUnits = $tableId > 0 ? $this->gameHistoryService->getScoreTotalUnitsByTableId($tableId) : 0;
        $scoreTotal = $tableId > 0 ? $this->gameHistoryService->getScoreTotalByTableId($tableId) : 0;

        if ($requestMethod === 'POST') {
            $errorMessages = $this->validateSubmission($postData, $formData, $userList, $scoreTotalUnits, $scoreTotal);

            if (count($formData['history_ids']) !== \App\Support\Constants\AppConstants::PLAYER_COUNT) {
                $errorMessages[] = '更新対象の履歴IDが不足しています。';
            }

            if (empty($errorMessages)) {
                $playDate = sprintf('%s %s', $formData['play_date'], $formData['time_part']);
                $result = $this->gameHistoryService->updateBatch($this->buildHistoryRecords($formData, $playDate));
                if ($result) {
                    $redirectUrl = 'history?view=overview';
                } else {
                    $errorMessages[] = '更新処理中にエラーが発生しました。';
                }
            }
        }

        return $this->withTitle('一括修正', [
            'userList' => $userList,
            'formData' => $formData,
            'errorMessages' => array_values(array_unique($errorMessages)),
            'directionLabels' => $this->buildDirectionLabels($directionList),
            'redirectUrl' => $redirectUrl,
            'scoreTotalUnits' => $scoreTotalUnits,
            'scoreTotal' => $scoreTotal,
        ]);
    }

    private function readHistoryIds(array $postData, array $queryParams): array
    {
        $rawHistoryIds = $postData['historyIds'] ?? $queryParams['historyIds'] ?? [];
        if (!is_array($rawHistoryIds)) {
            return [];
        }

        return array_values(array_filter(array_map('intval', $rawHistoryIds), static fn(int $id): bool => $id > 0));
    }

    private function buildFormData(int $tableId, string $playDateYmd, int $game, array $historyIds, array $batchRows, string $timePart): array
    {
        $seats = [];
        for ($direction = 1; $direction <= \App\Support\Constants\AppConstants::PLAYER_COUNT; $direction++) {
            $row = $batchRows[$direction - 1] ?? null;
            $seats[$direction] = [
                'history_id' => (int)($row['u_game_history_id'] ?? ($historyIds[$direction - 1] ?? 0)),
                'user_id' => (string)($row['u_user_id'] ?? ''),
                'rank' => (string)($row['rank'] ?? ''),
                'score' => isset($row['score']) ? (string)((int)$row['score'] / \App\Support\Constants\AppConstants::SCORE_INPUT_MULTIPLIER) : '',
                'mistake_count' => (int)($row['mistake_count'] ?? 0),
            ];
        }

        $year = $playDateYmd !== '' ? (int)date('Y', strtotime($playDateYmd)) : (int)date('Y');
        $month = $playDateYmd !== '' ? (int)date('n', strtotime($playDateYmd)) : (int)date('n');
        $day = $playDateYmd !== '' ? (int)date('j', strtotime($playDateYmd)) : (int)date('j');

        return [
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'play_date' => $playDateYmd,
            'time_part' => $timePart,
            'table_id' => $tableId,
            'game' => $game,
            'history_ids' => $historyIds,
            'seats' => $seats,
        ];
    }

    private function validateSubmission(array $postData, array &$formData, array $userList, int $scoreTotalUnits, int $scoreTotal): array
    {
        $errorMessages = [];
        $gameRaw = trim((string)($postData['game'] ?? ''));
        $formData['game'] = $gameRaw === '' ? '' : (int)$gameRaw;
        $formData['play_date'] = sprintf('%04d-%02d-%02d', (int)$formData['year'], (int)$formData['month'], (int)$formData['day']);

        if (!checkdate((int)$formData['month'], (int)$formData['day'], (int)$formData['year'])) {
            $errorMessages[] = '日付が不正です。';
        }
        if ($gameRaw === '' || !preg_match('/^\d+$/', $gameRaw) || (int)$gameRaw < 1) {
            $errorMessages[] = '半荘目は1以上の整数で入力してください。';
        }

        $userIds = [];
        $scoresByDirection = [];
        $scoreSum = 0;
        $scoreFormatError = false;
        $historyIds = [];

        for ($direction = 1; $direction <= \App\Support\Constants\AppConstants::PLAYER_COUNT; $direction++) {
            $userId = trim((string)($postData['userId_' . $direction] ?? ''));
            $score = trim((string)($postData['score_' . $direction] ?? ''));
            $mistakeRaw = trim((string)($postData['mistake_count_' . $direction] ?? '0'));
            $historyId = (int)($postData['historyId_' . $direction] ?? 0);

            $formData['seats'][$direction]['history_id'] = $historyId;
            $formData['seats'][$direction]['user_id'] = $userId;
            $formData['seats'][$direction]['score'] = $score;
            $formData['seats'][$direction]['rank'] = '';
            $formData['seats'][$direction]['mistake_count'] = $mistakeRaw === '' ? 0 : $mistakeRaw;

            if ($historyId < 1) {
                $errorMessages[] = '更新対象の履歴IDが不足しています。';
            }
            $historyIds[] = $historyId;

            if ($userId === '' || $score === '') {
                $errorMessages[] = '東南西北すべてに選手・点数を入力してください。';
                continue;
            }

            if (!preg_match('/^\d+$/', $userId) || !isset($userList[(int)$userId])) {
                $errorMessages[] = '選手の入力値が不正です。';
            }

            if (!preg_match('/^\d+$/', $mistakeRaw)) {
                $errorMessages[] = 'チョンボ回数は0〜99の整数で入力してください。';
            } else {
                $mistakeCount = (int)$mistakeRaw;
                if ($mistakeCount < 0 || $mistakeCount > 99) {
                    $errorMessages[] = 'チョンボ回数は0〜99の整数で入力してください。';
                }
                $formData['seats'][$direction]['mistake_count'] = $mistakeCount;
            }

            $userIds[] = (int)$userId;

            if (!preg_match('/^-?\d+$/', $score)) {
                $scoreFormatError = true;
                $errorMessages[] = '点数は100点単位の整数で入力してください。';
            } else {
                $scoreValue = (int)$score;
                $scoresByDirection[$direction] = $scoreValue;
                $scoreSum += $scoreValue;
            }
        }

        $formData['history_ids'] = $historyIds;

        if (count($userIds) === \App\Support\Constants\AppConstants::PLAYER_COUNT && count(array_unique($userIds)) !== \App\Support\Constants\AppConstants::PLAYER_COUNT) {
            $errorMessages[] = '選手は4席で重複できません。';
        }

        if (count($userIds) === \App\Support\Constants\AppConstants::PLAYER_COUNT && !$scoreFormatError && $scoreSum !== $scoreTotalUnits) {
            $errorMessages[] = sprintf('4人の点数合計は%d（=%d点）である必要があります。', $scoreTotalUnits, $scoreTotal);
        }

        if (count($scoresByDirection) === \App\Support\Constants\AppConstants::PLAYER_COUNT && !$scoreFormatError) {
            $derivedRanks = $this->deriveRanksFromScores($scoresByDirection);
            if ($derivedRanks === null) {
                $errorMessages[] = '順位を自動判定できません。同点は2人までにしてください。';
            } else {
                foreach ($derivedRanks as $direction => $rank) {
                    $formData['seats'][$direction]['rank'] = $rank;
                }
            }
        }

        return $errorMessages;
    }

    private function buildHistoryRecords(array $formData, string $playDate): array
    {
        $records = [];

        for ($direction = 1; $direction <= \App\Support\Constants\AppConstants::PLAYER_COUNT; $direction++) {
            $records[] = [
                'historyId' => (int)$formData['seats'][$direction]['history_id'],
                'userId' => (int)$formData['seats'][$direction]['user_id'],
                'tableId' => (int)$formData['table_id'],
                'game' => (int)$formData['game'],
                'direction' => $direction,
                'rank' => (string)$formData['seats'][$direction]['rank'],
                'score' => (int)$formData['seats'][$direction]['score'] * \App\Support\Constants\AppConstants::SCORE_INPUT_MULTIPLIER,
                'playDate' => $playDate,
                'mistakeCount' => (int)$formData['seats'][$direction]['mistake_count'],
            ];
        }

        return $records;
    }

    private function buildDirectionLabels(array $directionList): array
    {
        $labels = [];
        for ($direction = 1; $direction <= \App\Support\Constants\AppConstants::PLAYER_COUNT; $direction++) {
            $labels[$direction] = $directionList[$direction]['name'] ?? (string)$direction;
        }

        return $labels;
    }

    private function deriveRanksFromScores(array $scoresByDirection): ?array
    {
        if (count($scoresByDirection) !== \App\Support\Constants\AppConstants::PLAYER_COUNT) {
            return null;
        }

        arsort($scoresByDirection, SORT_NUMERIC);
        $groupedDirections = [];
        foreach ($scoresByDirection as $direction => $score) {
            $groupedDirections[$score][] = (int)$direction;
        }

        $currentRank = 1;
        $derivedRanks = [];
        foreach ($groupedDirections as $directions) {
            $groupSize = count($directions);
            if ($groupSize > 2) {
                return null;
            }

            $rankLabel = $groupSize === 2 ? $currentRank . '=' . $currentRank : (string)$currentRank;
            foreach ($directions as $direction) {
                $derivedRanks[$direction] = $rankLabel;
            }

            $currentRank += $groupSize;
        }

        ksort($derivedRanks);
        return $this->isValidRankCombination(array_values($derivedRanks)) ? $derivedRanks : null;
    }

    private function isValidRankCombination(array $ranks): bool
    {
        if (count($ranks) !== \App\Support\Constants\AppConstants::PLAYER_COUNT) {
            return false;
        }

        sort($ranks);
        foreach (\App\Support\Constants\AppConstants::RANK_PATTERNS as $pattern) {
            $sortedPattern = $pattern;
            sort($sortedPattern);
            if ($ranks === $sortedPattern) {
                return true;
            }
        }

        return false;
    }
}

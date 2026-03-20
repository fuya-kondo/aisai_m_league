<?php
/**
 * 一括登録画面専用の page data builder。
 * 入力初期値、検証、バッチ登録用 payload 生成を担当する。
 */
class BulkAddPageDataBuilder extends MainPageDataBuilder
{
    public function build(array $queryParams, array $postData, string $requestMethod): array
    {
        $aggregateTableId = \App\Support\Constants\AppConstants::AGGREGATE_TABLE_ID;
        $userList = $this->statsService->getUserList();
        $directionList = $this->getDirectionList();
        $today = new DateTimeImmutable();
        $selectedYear = (int)($postData['year'] ?? $queryParams['year'] ?? $today->format('Y'));
        $selectedMonth = (int)($postData['month'] ?? $queryParams['month'] ?? $today->format('n'));
        $selectedDay = (int)($postData['day'] ?? $queryParams['day'] ?? $today->format('j'));
        $playDateYmd = sprintf('%04d-%02d-%02d', $selectedYear, $selectedMonth, $selectedDay);
        $nextGame = $this->gameHistoryService->getNextGameNumberByDate($aggregateTableId, $playDateYmd);
        $latestGameRows = $this->gameHistoryService->getLatestGameRowsByDate($aggregateTableId, $playDateYmd);
        $seatDefaultUsers = $this->getRotatedSeatUserDefaults($latestGameRows);
        $formData = $this->buildFormData($aggregateTableId, $selectedYear, $selectedMonth, $selectedDay, $nextGame, $seatDefaultUsers);
        $errorMessages = [];
        $redirectUrl = null;
        $scoreTotalUnits = $this->gameHistoryService->getScoreTotalUnitsByTableId($aggregateTableId);
        $scoreTotal = $this->gameHistoryService->getScoreTotalByTableId($aggregateTableId);

        if ($requestMethod === 'POST') {
            $errorMessages = $this->validateSubmission($postData, $formData, $userList, $scoreTotalUnits, $scoreTotal);

            if (empty($errorMessages) && $this->gameHistoryService->existsGameForDate($aggregateTableId, $playDateYmd, (int)$formData['game'])) {
                $errorMessages[] = '同じ日付・半荘目のデータが既に登録されています。';
            }

            if (empty($errorMessages)) {
                $result = $this->gameHistoryService->createBatch($this->buildHistoryRecords($formData, $playDateYmd));
                if ($result) {
                    $redirectUrl = 'history';
                } else {
                    $errorMessages[] = '登録処理中にエラーが発生しました。';
                }
            }
        }

        return $this->withTitle('一括登録', [
            'pageTabs' => $this->buildRegistrationTabs('bulk'),
            'userList' => $userList,
            'formData' => $formData,
            'errorMessages' => array_values(array_unique($errorMessages)),
            'directionLabels' => $this->buildDirectionLabels($directionList),
            'redirectUrl' => $redirectUrl,
            'scoreTotalUnits' => $scoreTotalUnits,
            'scoreTotal' => $scoreTotal,
        ]);
    }

    private function buildFormData(int $tableId, int $selectedYear, int $selectedMonth, int $selectedDay, int $nextGame, array $seatDefaultUsers): array
    {
        return [
            'year' => $selectedYear,
            'month' => $selectedMonth,
            'day' => $selectedDay,
            'table_id' => $tableId,
            'game' => $nextGame,
            'seats' => [
                1 => ['user_id' => $seatDefaultUsers[1], 'rank' => '', 'score' => '', 'mistake_count' => 0],
                2 => ['user_id' => $seatDefaultUsers[2], 'rank' => '', 'score' => '', 'mistake_count' => 0],
                3 => ['user_id' => $seatDefaultUsers[3], 'rank' => '', 'score' => '', 'mistake_count' => 0],
                4 => ['user_id' => $seatDefaultUsers[4], 'rank' => '', 'score' => '', 'mistake_count' => 0],
            ],
        ];
    }

    private function validateSubmission(array $postData, array &$formData, array $userList, int $scoreTotalUnits, int $scoreTotal): array
    {
        $errorMessages = [];
        $gameRaw = trim((string)($postData['game'] ?? ''));
        $formData['game'] = $gameRaw === '' ? '' : (int)$gameRaw;

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

        for ($direction = 1; $direction <= \App\Support\Constants\AppConstants::PLAYER_COUNT; $direction++) {
            $userId = trim((string)($postData['userId_' . $direction] ?? ''));
            $score = trim((string)($postData['score_' . $direction] ?? ''));
            $mistakeRaw = trim((string)($postData['mistake_count_' . $direction] ?? '0'));

            $formData['seats'][$direction]['user_id'] = $userId;
            $formData['seats'][$direction]['score'] = $score;
            $formData['seats'][$direction]['rank'] = '';
            $formData['seats'][$direction]['mistake_count'] = $mistakeRaw === '' ? 0 : $mistakeRaw;

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

    private function buildHistoryRecords(array $formData, string $playDateYmd): array
    {
        $playDate = sprintf('%s %s', $playDateYmd, date('H:i:s'));
        $records = [];

        for ($direction = 1; $direction <= \App\Support\Constants\AppConstants::PLAYER_COUNT; $direction++) {
            $records[] = [
                'userId' => (int)$formData['seats'][$direction]['user_id'],
                'tableId' => \App\Support\Constants\AppConstants::AGGREGATE_TABLE_ID,
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

    private function buildRegistrationTabs(string $activeTab): array
    {
        return [
            ['label' => '一括登録', 'href' => 'bulk-add', 'active' => $activeTab === 'bulk'],
            ['label' => '個別登録', 'href' => 'add', 'active' => $activeTab === 'single'],
        ];
    }

    private function getRotatedSeatUserDefaults(array $latestGameRows): array
    {
        $defaults = [1 => '', 2 => '', 3 => '', 4 => ''];
        if (empty($latestGameRows)) {
            return $defaults;
        }

        $lastSeatUsers = [1 => '', 2 => '', 3 => '', 4 => ''];
        foreach ($latestGameRows as $row) {
            $direction = (int)($row['m_direction_id'] ?? 0);
            if ($direction >= 1 && $direction <= \App\Support\Constants\AppConstants::PLAYER_COUNT) {
                $lastSeatUsers[$direction] = (string)($row['u_user_id'] ?? '');
            }
        }

        $defaults[1] = $lastSeatUsers[2] ?? '';
        $defaults[2] = $lastSeatUsers[3] ?? '';
        $defaults[3] = $lastSeatUsers[4] ?? '';
        $defaults[4] = $lastSeatUsers[1] ?? '';

        return $defaults;
    }
}

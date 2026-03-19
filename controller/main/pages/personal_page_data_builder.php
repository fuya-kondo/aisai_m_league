<?php
/**
 * 個人成績画面専用の view data builder。
 * 主要成績、各家成績、関係性、ランク履歴を表示専用の配列へ変換する。
 */
class PersonalStatsPageDataBuilder extends MainPageDataBuilder
{
    public function build(string $selectedTerm, ?string $selectedPlayer): array
    {
        $statsByTerm = $this->statsService->getStatsByTermList();
        $userList = $this->statsService->getUserList();
        $directionList = $this->getDirectionList();
        $scoreDisplayFlag = $this->isScoreDisplayEnabled($selectedTerm);
        $playerData = $this->findPlayerStats($statsByTerm, $selectedTerm, $selectedPlayer);
        $directionStats = $this->statsService->getRelativeScoreByDirection();
        $rankHistoryList = $this->statsService->getRankHistory();

        return $this->withTitle('個人成績', [
            'pageTabs' => [
                ['label' => '成績', 'href' => 'stats?term=' . urlencode($selectedTerm), 'active' => false],
                ['label' => '個人成績', 'href' => $this->buildPersonalTabHref($selectedTerm, $selectedPlayer), 'active' => true],
            ],
            'selectedTerm' => $selectedTerm,
            'selectedPlayer' => $selectedPlayer,
            'playerOptions' => $this->buildPlayerOptions($userList, $selectedTerm, $selectedPlayer),
            'termOptions' => $this->buildTermOptions($selectedPlayer, $selectedTerm),
            'playerProfile' => $this->buildPlayerProfile($selectedPlayer, $userList),
            'playerStatsExists' => $playerData !== null,
            'primaryStatsRows' => $playerData ? $this->buildPrimaryStatsRows($playerData, $scoreDisplayFlag) : [],
            'directionHeaders' => $this->buildDirectionHeaders($directionList),
            'directionStatsRows' => $playerData ? $this->buildDirectionStatsRows($playerData, $directionList, $scoreDisplayFlag) : [],
            'relationColumns' => $selectedPlayer ? $this->buildRelationColumns($selectedPlayer, $directionStats, $userList) : [],
            'rankHistoryItems' => $selectedPlayer ? $this->buildRankHistoryItems($selectedPlayer, $rankHistoryList) : [],
            'personalStatsChartData' => $playerData,
        ]);
    }

    private function buildTermOptions(?string $selectedPlayer, string $selectedTerm): array
    {
        $options = [[
            'value' => \App\Support\Constants\AppConstants::ALL_TERM_LABEL,
            'label' => \App\Support\Constants\AppConstants::ALL_TERM_LABEL,
            'href' => $this->buildPersonalTabHref(\App\Support\Constants\AppConstants::ALL_TERM_LABEL, $selectedPlayer),
            'active' => $selectedTerm === \App\Support\Constants\AppConstants::ALL_TERM_LABEL,
        ]];

        if ($this->statsService->hasTodayStats()) {
            $options[] = [
                'value' => \App\Support\Constants\AppConstants::TODAY_TERM,
                'label' => \App\Support\Constants\AppConstants::TODAY_TERM_LABEL,
                'href' => $this->buildPersonalTabHref(\App\Support\Constants\AppConstants::TODAY_TERM, $selectedPlayer),
                'active' => $selectedTerm === \App\Support\Constants\AppConstants::TODAY_TERM,
            ];
        }

        foreach ($this->statsService->getYearTerms() as $yearTerm) {
            $options[] = [
                'value' => $yearTerm,
                'label' => $yearTerm,
                'href' => $this->buildPersonalTabHref($yearTerm, $selectedPlayer),
                'active' => $selectedTerm === $yearTerm,
            ];
        }

        return $options;
    }

    private function buildPersonalTabHref(string $selectedTerm, ?string $selectedPlayer): string
    {
        $query = ['term' => $selectedTerm];
        if ($selectedPlayer !== null && $selectedPlayer !== '') {
            $query['player'] = $selectedPlayer;
        }

        return 'personal?' . http_build_query($query);
    }

    private function buildPlayerOptions(array $userList, string $selectedTerm, ?string $selectedPlayer): array
    {
        $options = [];
        foreach ($userList as $userId => $userData) {
            $options[] = [
                'value' => $userId,
                'label' => $userData['last_name'] . $userData['first_name'],
                'href' => $this->buildPersonalTabHref($selectedTerm, (string)$userId),
                'active' => (string)$userId === (string)$selectedPlayer,
            ];
        }

        return $options;
    }

    private function buildPlayerProfile(?string $selectedPlayer, array $userList): array
    {
        if ($selectedPlayer === null || !isset($userList[$selectedPlayer])) {
            return [];
        }

        $player = $userList[$selectedPlayer];
        return [
            'tierName' => $player['tier']['name'] ?? null,
            'tierColor' => $player['tier']['color'] ?? null,
            'badgeName' => $player['badge']['name'] ?? null,
            'badgeUrl' => 'badge?userId=' . urlencode($selectedPlayer),
            'tierTargetId' => 'tier_history',
        ];
    }

    private function buildPrimaryStatsRows(array $playerData, bool $scoreDisplayFlag): array
    {
        $rows = [];
        foreach (($this->statsColumn['statsColumnAllConfig_1'] ?? []) as $columnKey => $columnName) {
            $rows[] = [
                'labelLines' => is_array($columnName) ? array_values($columnName) : [$columnName],
                'valueLines' => $this->extractStatLines($playerData, $columnKey, $columnName, $scoreDisplayFlag),
            ];
        }

        return $rows;
    }

    private function buildDirectionHeaders(array $directionList): array
    {
        $headers = [];
        foreach ($directionList as $directionId => $directionData) {
            $headers[] = [
                'directionId' => $directionId,
                'label' => $directionData['name'],
            ];
        }

        return $headers;
    }

    private function buildDirectionStatsRows(array $playerData, array $directionList, bool $scoreDisplayFlag): array
    {
        $rows = [];
        foreach (($this->statsColumn['statsColumnAllConfig_2'] ?? []) as $columnKey => $columnName) {
            $valuesByDirection = [];
            foreach ($directionList as $directionId => $directionData) {
                $valuesByDirection[$directionId] = $this->extractDirectionStatLines($playerData, $columnKey, $columnName, $directionId, $scoreDisplayFlag);
            }

            $rows[] = [
                'labelLines' => is_array($columnName) ? array_values($columnName) : [$columnName],
                'valuesByDirection' => $valuesByDirection,
            ];
        }

        return $rows;
    }

    private function buildRelationColumns(string $selectedPlayer, array $directionStats, array $userList): array
    {
        $columns = [];
        foreach (['upper' => '上家', 'lower' => '下家'] as $key => $label) {
            $cards = [];
            if (isset($directionStats[$key][$selectedPlayer]) && is_array($directionStats[$key][$selectedPlayer])) {
                foreach ($directionStats[$key][$selectedPlayer] as $userId => $stats) {
                    $statRows = [];
                    foreach ($stats as $statKey => $value) {
                        $statRows[] = [
                            'label' => $this->statsColumn['displayStatsColumn_2'][$statKey] ?? $statKey,
                            'value' => $value,
                        ];
                    }

                    $cards[] = [
                        'playerName' => $userList[$userId]['last_name'] ?? (string)$userId,
                        'stats' => $statRows,
                    ];
                }
            }

            $columns[$key] = [
                'label' => $label,
                'cards' => $cards,
                'emptyMessage' => $label . 'のデータがありません。',
            ];
        }

        return $columns;
    }

    private function buildRankHistoryItems(string $selectedPlayer, array $rankHistoryList): array
    {
        $items = [];
        if (!isset($rankHistoryList[$selectedPlayer]) || !is_array($rankHistoryList[$selectedPlayer])) {
            return $items;
        }

        foreach ($rankHistoryList[$selectedPlayer] as $year => $tierInfo) {
            $items[] = [
                'year' => $year,
                'beforeName' => $tierInfo['before']['name'],
                'beforeColor' => $tierInfo['before']['color'],
                'afterName' => $tierInfo['after']['name'],
                'afterColor' => $tierInfo['after']['color'],
            ];
        }

        return $items;
    }

    private function extractStatLines(array $playerData, string $columnKey, $columnName, bool $scoreDisplayFlag): array
    {
        $hiddenColumns = ['sum_point', 'sum_base_score', 'average_point', 'average_score'];
        if (!$scoreDisplayFlag && in_array($columnKey, $hiddenColumns, true)) {
            return ['---'];
        }

        if (!is_array($columnName)) {
            return [(string)($playerData[$columnKey] ?? '')];
        }

        $values = [];
        foreach (array_keys($columnName) as $subKey) {
            $values[] = (string)($playerData[$columnKey][$subKey] ?? '');
        }

        return $values;
    }

    private function extractDirectionStatLines(array $playerData, string $columnKey, $columnName, int $directionId, bool $scoreDisplayFlag): array
    {
        $hiddenColumns = ['sum_point_direction', 'sum_base_score_direction', 'average_point_direction', 'average_score_direction'];
        if (!$scoreDisplayFlag && in_array($columnKey, $hiddenColumns, true)) {
            return ['---'];
        }

        if (!is_array($columnName)) {
            return [(string)($playerData[$columnKey][$directionId] ?? '')];
        }

        $values = [];
        foreach (array_keys($columnName) as $subKey) {
            $values[] = (string)($playerData[$columnKey][$directionId][$subKey] ?? '');
        }

        return $values;
    }

    private function findPlayerStats(array $statsByTerm, string $selectedTerm, ?string $selectedPlayer): ?array
    {
        if ($selectedPlayer === null || !isset($statsByTerm[$selectedTerm])) {
            return null;
        }

        foreach ($statsByTerm[$selectedTerm] as $playerStats) {
            if ((string)$playerStats['u_user_id'] === $selectedPlayer) {
                return $playerStats;
            }
        }

        return null;
    }
}

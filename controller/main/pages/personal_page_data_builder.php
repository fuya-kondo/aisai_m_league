<?php
/**
 * 個人成績画面専用の view data builder。
 * 主要成績、各家成績、関係性、ランク履歴を表示専用の配列へ変換する。
 */
class PersonalStatsPageDataBuilder extends MainPageDataBuilder
{
    public function build(string $selectedTerm, ?string $selectedPlayer): array
    {
        $selectedTerm = $this->normalizeSelectedTerm($selectedTerm);
        $statsByTerm = $this->statsService->getStatsByTermList();
        $chartByTerm = $this->statsService->getChartByTermList();
        $userList = $this->statsService->getUserList();
        $directionList = $this->getDirectionList();
        $scoreDisplayFlag = $this->isScoreDisplayEnabled($selectedTerm);
        $playerData = $this->findPlayerStats($statsByTerm, $selectedTerm, $selectedPlayer);
        $directionStats = $this->statsService->getRelativeScoreByDirection();
        $rankHistoryList = $this->statsService->getRankHistory();
        $selectedTermLabel = $this->findSelectedTermLabel($selectedTerm);

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
            'scoreDisplayFlag' => $scoreDisplayFlag,
            'playerStatsExists' => $playerData !== null,
            'primaryStatsRows' => $playerData ? $this->buildPrimaryStatsRows($playerData, $scoreDisplayFlag) : [],
            'directionHeaders' => $this->buildDirectionHeaders($directionList),
            'directionStatsRows' => $playerData ? $this->buildDirectionStatsRows($playerData, $directionList, $scoreDisplayFlag) : [],
            'relationColumns' => $selectedPlayer ? $this->buildRelationColumns($selectedPlayer, $directionStats, $userList) : [],
            'rankHistoryItems' => $selectedPlayer ? $this->buildRankHistoryItems($selectedPlayer, $rankHistoryList) : [],
            'personalStatsChartData' => $playerData,
            'personalPointChartData' => ($scoreDisplayFlag && $selectedPlayer !== null)
                ? $this->buildPersonalPointChartData($chartByTerm, $userList, $selectedTerm, $selectedTermLabel, $selectedPlayer)
                : $this->buildEmptyPointChartData($selectedTerm, $selectedTermLabel),
        ]);
    }

    private function normalizeSelectedTerm(string $selectedTerm): string
    {
        if ($selectedTerm === \App\Support\Constants\AppConstants::TODAY_TERM) {
            return \App\Support\Constants\AppConstants::ALL_TERM_LABEL;
        }

        return $selectedTerm;
    }

    private function buildTermOptions(?string $selectedPlayer, string $selectedTerm): array
    {
        $options = [[
            'value' => \App\Support\Constants\AppConstants::ALL_TERM_LABEL,
            'label' => \App\Support\Constants\AppConstants::ALL_TERM_LABEL,
            'href' => $this->buildPersonalTabHref(\App\Support\Constants\AppConstants::ALL_TERM_LABEL, $selectedPlayer),
            'active' => $selectedTerm === \App\Support\Constants\AppConstants::ALL_TERM_LABEL,
        ]];

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

    private function findSelectedTermLabel(string $selectedTerm): string
    {
        if ($selectedTerm === \App\Support\Constants\AppConstants::TODAY_TERM) {
            return \App\Support\Constants\AppConstants::TODAY_TERM_LABEL;
        }

        return $selectedTerm;
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
        $displayName = (string)($player['last_name'] ?? '') . (string)($player['first_name'] ?? '');

        return [
            'displayName' => $displayName,
            'avatarUrl' => $this->resolvePlayerAvatarUrl((string)$selectedPlayer),
            'tierName' => $player['tier']['name'] ?? null,
            'tierColor' => $player['tier']['color'] ?? null,
            'badgeName' => $player['badge']['name'] ?? null,
            'badgeUrl' => 'badge?userId=' . urlencode($selectedPlayer),
            'tierTargetId' => 'tier_history',
        ];
    }

    private function resolvePlayerAvatarUrl(string $playerId): ?string
    {
        if ($playerId === '') {
            return null;
        }

        $relativePath = 'resources/image/player_' . $playerId . '_avatar.png';
        $absolutePath = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

        if (!is_file($absolutePath)) {
            return null;
        }

        return \App\Support\AppConfig::assetUrl($relativePath);
    }

    private function buildPersonalPointChartData(
        array $chartByTerm,
        array $userList,
        string $selectedTerm,
        string $selectedTermLabel,
        string $selectedPlayer
    ): array {
        if (empty($chartByTerm[$selectedTerm][$selectedPlayer])) {
            return $this->buildEmptyPointChartData($selectedTerm, $selectedTermLabel);
        }

        if ($selectedTerm === \App\Support\Constants\AppConstants::TODAY_TERM) {
            return $this->buildTodayPersonalPointChartData(
                $chartByTerm[$selectedTerm][$selectedPlayer],
                $userList,
                $selectedTerm,
                $selectedTermLabel,
                $selectedPlayer
            );
        }

        return $this->buildDateBasedPersonalPointChartData(
            $chartByTerm[$selectedTerm][$selectedPlayer],
            $userList,
            $selectedTerm,
            $selectedTermLabel,
            $selectedPlayer
        );
    }

    private function buildDateBasedPersonalPointChartData(
        array $historyRows,
        array $userList,
        string $selectedTerm,
        string $selectedTermLabel,
        string $selectedPlayer
    ): array {
        $pointsByDate = [];
        foreach ($historyRows as $historyRow) {
            $playDate = date('Y-m-d', strtotime((string)($historyRow['play_date'] ?? '')));
            $point = filter_var($historyRow['point'] ?? null, FILTER_VALIDATE_FLOAT);
            if ($playDate === '1970-01-01') {
                continue;
            }
            if ($point === false) {
                $point = 0;
            }

            $pointsByDate[$playDate] = ($pointsByDate[$playDate] ?? 0) + $point;
        }

        if (empty($pointsByDate)) {
            return $this->buildEmptyPointChartData($selectedTerm, $selectedTermLabel);
        }

        ksort($pointsByDate);
        $dates = array_keys($pointsByDate);
        $totalPoint = 0;
        $dataPoints = [];
        foreach ($dates as $date) {
            $totalPoint += $pointsByDate[$date];
            $dataPoints[] = ['x' => $date, 'y' => round($totalPoint, 1)];
        }

        return [
            'datasets' => [[
                'label' => '',
                'data' => $dataPoints,
                'borderColor' => '#009944',
                'fill' => false,
            ]],
            'dates' => $dates,
            'labels' => [],
            'xAxisType' => 'date',
            'selectedTerm' => $selectedTerm,
            'selectedTermLabel' => $selectedTermLabel,
        ];
    }

    private function buildTodayPersonalPointChartData(
        array $historyRows,
        array $userList,
        string $selectedTerm,
        string $selectedTermLabel,
        string $selectedPlayer
    ): array {
        usort($historyRows, static function (array $left, array $right): int {
            $leftGame = (int)($left['game'] ?? 0);
            $rightGame = (int)($right['game'] ?? 0);
            if ($leftGame !== $rightGame) {
                return $leftGame <=> $rightGame;
            }

            $leftTimestamp = strtotime((string)($left['play_date'] ?? '')) ?: 0;
            $rightTimestamp = strtotime((string)($right['play_date'] ?? '')) ?: 0;
            if ($leftTimestamp !== $rightTimestamp) {
                return $leftTimestamp <=> $rightTimestamp;
            }

            return (int)($left['u_game_history_id'] ?? 0) <=> (int)($right['u_game_history_id'] ?? 0);
        });

        $pointsByGame = [];
        foreach ($historyRows as $historyRow) {
            $gameNumber = (int)($historyRow['game'] ?? 0);
            if ($gameNumber <= 0) {
                continue;
            }

            $point = filter_var($historyRow['point'] ?? null, FILTER_VALIDATE_FLOAT);
            if ($point === false) {
                $point = 0;
            }

            $pointsByGame[$gameNumber] = ($pointsByGame[$gameNumber] ?? 0) + $point;
        }

        if (empty($pointsByGame)) {
            return $this->buildEmptyPointChartData($selectedTerm, $selectedTermLabel);
        }

        ksort($pointsByGame);
        $gameNumbers = array_keys($pointsByGame);
        $dataPoints = [0];
        $totalPoint = 0;
        foreach ($gameNumbers as $gameNumber) {
            $totalPoint += $pointsByGame[$gameNumber];
            $dataPoints[] = round($totalPoint, 1);
        }

        return [
            'datasets' => [[
                'label' => '',
                'data' => $dataPoints,
                'borderColor' => '#009944',
                'fill' => false,
            ]],
            'dates' => [],
            'labels' => array_merge(['0'], array_map('strval', $gameNumbers)),
            'xAxisType' => 'game',
            'selectedTerm' => $selectedTerm,
            'selectedTermLabel' => $selectedTermLabel,
        ];
    }

    private function buildEmptyPointChartData(string $selectedTerm, string $selectedTermLabel): array
    {
        return [
            'datasets' => [],
            'dates' => [],
            'labels' => [],
            'xAxisType' => 'game',
            'selectedTerm' => $selectedTerm,
            'selectedTermLabel' => $selectedTermLabel,
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

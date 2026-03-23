<?php
/**
 * 全体成績画面専用の view data builder。
 * 成績表、ポイントバー、タイトル履歴、グラフ用データを描画向けに整形する。
 */
class StatsPageDataBuilder extends MainPageDataBuilder
{
    public function build(string $selectedTerm): array
    {
        $userList = $this->statsService->getUserList();
        $yearTerms = $this->statsService->getYearTerms();
        $titleHolderList = $this->statsService->getTitleHolder();
        $statsByTerm = $this->statsService->getStatsByTermList();
        $chartByTerm = $this->statsService->getChartByTermList();
        $scoreDisplayFlag = $this->isScoreDisplayEnabled($selectedTerm);
        $tableColumns = $this->buildStatsTableColumns($this->statsColumn['displayStatsColumn_1'] ?? []);
        $selectedTermStats = $statsByTerm[$selectedTerm] ?? [];
        $termOptions = $this->buildTermOptions($yearTerms, isset($statsByTerm[\App\Support\Constants\AppConstants::TODAY_TERM]));
        $selectedTermLabel = $this->findSelectedTermLabel($termOptions, $selectedTerm);
        $selectedYear = ctype_digit($selectedTerm) ? $selectedTerm : null;

        return $this->withTitle('成績', [
            'pageTabs' => [
                ['label' => '成績', 'href' => 'stats?term=' . urlencode($selectedTerm), 'active' => true],
                ['label' => '個人成績', 'href' => 'personal?term=' . urlencode($selectedTerm), 'active' => false],
            ],
            'selectedTerm' => $selectedTerm,
            'selectedTermLabel' => $selectedTermLabel,
            'termOptions' => $termOptions,
            'selectedTermStatsTable' => [
                'columns' => $tableColumns,
                'rows' => $this->buildStatsTableRows($selectedTermStats, $tableColumns, $selectedTerm, $scoreDisplayFlag),
                'playCount' => $this->extractPlayCount($selectedTermStats),
            ],
            'scoreDisplayFlag' => $scoreDisplayFlag,
            'statsChartData' => $this->buildStatsChartData($userList, $chartByTerm, $selectedTerm, $selectedTermLabel),
            'titleHistoryItems' => $selectedYear !== null ? ($titleHolderList[$selectedYear] ?? []) : [],
        ]);
    }

    private function buildTermOptions(array $yearTerms, bool $hasTodayStats): array
    {
        $options = [[
            'value' => \App\Support\Constants\AppConstants::ALL_TERM_LABEL,
            'label' => \App\Support\Constants\AppConstants::ALL_TERM_LABEL,
            'href' => '?term=' . urlencode(\App\Support\Constants\AppConstants::ALL_TERM_LABEL),
        ]];

        if ($hasTodayStats) {
            $options[] = [
                'value' => \App\Support\Constants\AppConstants::TODAY_TERM,
                'label' => \App\Support\Constants\AppConstants::TODAY_TERM_LABEL,
                'href' => '?term=' . urlencode(\App\Support\Constants\AppConstants::TODAY_TERM),
            ];
        }

        foreach ($yearTerms as $yearTerm) {
            $options[] = [
                'value' => $yearTerm,
                'label' => $yearTerm,
                'href' => '?term=' . urlencode($yearTerm),
            ];
        }

        return $options;
    }

    private function findSelectedTermLabel(array $termOptions, string $selectedTerm): string
    {
        foreach ($termOptions as $termOption) {
            if ((string)$termOption['value'] === $selectedTerm) {
                return (string)$termOption['label'];
            }
        }

        return $selectedTerm;
    }

    private function buildStatsTableColumns(array $columnConfig): array
    {
        $columns = [];
        foreach ($columnConfig as $key => $label) {
            if (is_array($label)) {
                foreach ($label as $subKey => $subLabel) {
                    $columns[] = [
                        'key' => $key,
                        'subKey' => $subKey,
                        'label' => $subLabel,
                    ];
                }
                continue;
            }

            $columns[] = [
                'key' => $key,
                'subKey' => null,
                'label' => $label,
            ];
        }

        return $columns;
    }

    private function buildStatsTableRows(array $statsList, array $tableColumns, string $selectedTerm, bool $showPointBars): array
    {
        $barValues = $this->buildPointBarValues($statsList);
        $rows = [];

        foreach ($statsList as $statsRow) {
            $cells = [];
            foreach ($tableColumns as $column) {
                $key = $column['key'];
                $subKey = $column['subKey'];
                $rawValue = $subKey !== null && isset($statsRow[$key][$subKey])
                    ? $statsRow[$key][$subKey]
                    : ($statsRow[$key] ?? '');

                if ($key === 'ranking') {
                    $cells[] = [
                        'type' => 'ranking',
                        'value' => $statsRow['ranking'] ?? '',
                    ];
                    continue;
                }

                if ($key === 'name') {
                    $cells[] = [
                        'type' => 'player',
                        'value' => $rawValue,
                        'href' => 'personal?term=' . urlencode($selectedTerm) . '&player=' . urlencode((string)($statsRow['u_user_id'] ?? '')),
                        'barDirection' => ($statsRow['sum_point'] ?? 0) >= 0 ? 'right' : 'left',
                        'barValue' => $showPointBars ? ($barValues[$statsRow['u_user_id']] ?? 0) : null,
                    ];
                    continue;
                }

                if ($key === 'sum_point' && !$showPointBars) {
                    $cells[] = [
                        'type' => 'text',
                        'value' => '---',
                    ];
                    continue;
                }

                $cells[] = [
                    'type' => 'text',
                    'value' => $rawValue,
                ];
            }

            $rows[] = ['cells' => $cells];
        }

        return $rows;
    }

    private function buildPointBarValues(array $statsList): array
    {
        $maxPoint = 0;
        $minPoint = 0;
        $values = [];

        foreach ($statsList as $statsRow) {
            $sumPoint = (float)($statsRow['sum_point'] ?? 0);
            $maxPoint = max($sumPoint, $maxPoint);
            $minPoint = min($sumPoint, $minPoint);
        }

        foreach ($statsList as $statsRow) {
            $sumPoint = (float)($statsRow['sum_point'] ?? 0);
            $userId = $statsRow['u_user_id'] ?? null;
            if ($userId === null) {
                continue;
            }

            if ($sumPoint === $maxPoint || $sumPoint === $minPoint) {
                $values[$userId] = 90;
                continue;
            }

            if ($maxPoint == 0.0) {
                $values[$userId] = 50;
                continue;
            }

            $values[$userId] = max((int)($sumPoint / $maxPoint * 80), 50);
        }

        return $values;
    }

    private function extractPlayCount(array $statsList): int
    {
        $firstRow = reset($statsList);
        return (int)($firstRow['play_count'] ?? 0);
    }

    private function buildStatsChartData(array $userList, array $chartByTerm, string $selectedTerm, string $selectedTermLabel): array
    {
        if (empty($chartByTerm[$selectedTerm])) {
            return [
                'datasets' => [],
                'dates' => [],
                'labels' => [],
                'xAxisType' => 'date',
                'selectedTerm' => $selectedTerm,
                'selectedTermLabel' => $selectedTermLabel,
            ];
        }

        if ($selectedTerm === \App\Support\Constants\AppConstants::TODAY_TERM) {
            return $this->buildTodayStatsChartData($userList, $chartByTerm[$selectedTerm], $selectedTerm, $selectedTermLabel);
        }

        $playerPointHistory = [];
        $allDates = [];
        foreach ($userList as $userId => $userData) {
            foreach (($chartByTerm[$selectedTerm][$userId] ?? []) as $historyRow) {
                $playDate = date('Y-m-d', strtotime($historyRow['play_date']));
                $point = filter_var($historyRow['point'], FILTER_VALIDATE_FLOAT);
                if ($point === false) {
                    $point = 0;
                }

                if (!isset($playerPointHistory[$userId])) {
                    $playerPointHistory[$userId] = [];
                }

                $playerPointHistory[$userId][$playDate] = ($playerPointHistory[$userId][$playDate] ?? 0) + $point;
                $allDates[$playDate] = true;
            }
        }

        ksort($allDates);
        $dates = array_keys($allDates);
        $datasets = [];
        $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'];

        foreach ($playerPointHistory as $userId => $pointsByDate) {
            $dataPoints = [];
            $totalPoint = 0;
            foreach ($dates as $date) {
                if (isset($pointsByDate[$date])) {
                    $totalPoint += $pointsByDate[$date];
                }

                $dataPoints[] = ['x' => $date, 'y' => $totalPoint];
            }

            $datasets[] = [
                'label' => $userList[$userId]['last_name'] ?? (string)$userId,
                'data' => $dataPoints,
                'borderColor' => $colors[$userId % count($colors)],
                'fill' => false,
            ];
        }

        return [
            'datasets' => $datasets,
            'dates' => $dates,
            'labels' => [],
            'xAxisType' => 'date',
            'selectedTerm' => $selectedTerm,
            'selectedTermLabel' => $selectedTermLabel,
        ];
    }

    private function buildTodayStatsChartData(array $userList, array $todayChartByUser, string $selectedTerm, string $selectedTermLabel): array
    {
        $playerPointHistory = [];
        $allGames = [];
        $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'];

        foreach ($userList as $userId => $userData) {
            $historyRows = $todayChartByUser[$userId] ?? [];
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

            foreach ($historyRows as $historyRow) {
                $gameNumber = (int)($historyRow['game'] ?? 0);
                if ($gameNumber <= 0) {
                    continue;
                }

                $point = filter_var($historyRow['point'], FILTER_VALIDATE_FLOAT);
                if ($point === false) {
                    $point = 0;
                }

                $playerPointHistory[$userId][$gameNumber] = ($playerPointHistory[$userId][$gameNumber] ?? 0) + $point;
                $allGames[$gameNumber] = true;
            }
        }

        ksort($allGames);
        $gameNumbers = array_keys($allGames);
        $datasets = [];

        foreach ($playerPointHistory as $userId => $pointsByGame) {
            $dataPoints = [0];
            $totalPoint = 0;
            foreach ($gameNumbers as $gameNumber) {
                if (isset($pointsByGame[$gameNumber])) {
                    $totalPoint += $pointsByGame[$gameNumber];
                }

                $dataPoints[] = $totalPoint;
            }

            $datasets[] = [
                'label' => $userList[$userId]['last_name'] ?? (string)$userId,
                'data' => $dataPoints,
                'borderColor' => $colors[$userId % count($colors)],
                'fill' => false,
            ];
        }

        return [
            'datasets' => $datasets,
            'dates' => [],
            'labels' => array_merge(['0'], array_map('strval', $gameNumbers)),
            'xAxisType' => 'game',
            'selectedTerm' => $selectedTerm,
            'selectedTermLabel' => $selectedTermLabel,
        ];
    }
}

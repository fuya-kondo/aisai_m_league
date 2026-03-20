<?php
/**
 * 成績集計・分析向けのドメインロジックをまとめるサービスクラス。
 * 集計・履歴再編成・スケジュール抽出を専用 service へ分離し、このクラスは orchestrator として振る舞う。
 */
class StatsService
{
    private array $userList = [];
    private array $tableData = [];
    private array $groupData = [];
    private array $ruleData = [];
    private array $titleDefinitions = [];
    private array $userTitles = [];
    private array $directionDefinitions = [];
    private array $scheduledGameDays = [];
    private array $gameHistoryByUser = [];
    private array $tierDefinitions = [];
    private array $tierHistoryRecords = [];
    private array $badgeDefinitions = [];
    private array $availableTerms = [];
    private int $baseScore;
    private StatsPresenter $statsPresenter;
    private \App\Services\StatsScoreAggregator $scoreAggregator;
    private \App\Services\StatsHistoryOrganizer $historyOrganizer;
    private \App\Services\ScheduleService $scheduleService;

    public function __construct(StatsSourceSet $sourceSet)
    {
        $this->userList = $sourceSet->userList;
        $this->tableData = $sourceSet->tableData;
        $this->groupData = $sourceSet->groupData;
        $this->ruleData = $sourceSet->ruleData;
        $this->titleDefinitions = $sourceSet->titleDefinitions;
        $this->userTitles = $sourceSet->userTitles;
        $this->directionDefinitions = $sourceSet->directionList;
        $this->scheduledGameDays = $sourceSet->gameDayList;
        $this->gameHistoryByUser = $sourceSet->gameHistoryByUser;
        $this->tierDefinitions = $sourceSet->tierDefinitions;
        $this->tierHistoryRecords = $sourceSet->tierHistoryRecords;
        $this->badgeDefinitions = $sourceSet->badgeDefinitions;
        $this->baseScore = isset($sourceSet->ruleData['start_score']) ? (int) $sourceSet->ruleData['start_score'] : 25000;
        $this->statsPresenter = \App\Support\StatsComponentFactory::createPresenter();
        $this->scoreAggregator = \App\Support\StatsComponentFactory::createScoreAggregator($this->baseScore);
        $this->historyOrganizer = \App\Support\StatsComponentFactory::createHistoryOrganizer();
        $this->scheduleService = \App\Support\StatsComponentFactory::createScheduleService();
        $this->initializeAvailableTerms();
    }

    public function getUserList(): array
    {
        $userList = $this->userList;
        foreach ($userList as $userId => &$userData) {
            if (isset($this->tierDefinitions[$userData['m_tier_id']])) {
                $userData['tier'] = $this->tierDefinitions[$userData['m_tier_id']];
            }
            if (isset($this->badgeDefinitions[$userData['m_badge_id']])) {
                $userData['badge'] = $this->badgeDefinitions[$userData['m_badge_id']];
            }
        }
        unset($userData);

        return $userList;
    }

    public function getYears(): array
    {
        return $this->availableTerms;
    }

    public function getYearTerms(): array
    {
        return array_values(array_filter(
            $this->availableTerms,
            static fn(string $term): bool => $term !== \App\Support\Constants\AppConstants::ALL_TERM_LABEL
        ));
    }

    public function getTitleHolder(): array
    {
        $titleNameMap = [];
        foreach ($this->titleDefinitions as $titleDefinition) {
            $titleNameMap[$titleDefinition['m_title_id']] = $titleDefinition['name'];
        }

        $userNameMap = [];
        foreach ($this->userList as $userData) {
            $userNameMap[$userData['u_user_id']] = $userData['last_name'] . $userData['first_name'];
        }

        $groupedTitles = [];
        foreach ($this->userTitles as $userTitle) {
            $year = $userTitle['year'];
            $groupedTitles[$year][] = [
                'u_title_id' => $userTitle['u_title_id'],
                'title_name' => $titleNameMap[$userTitle['m_title_id']] ?? '不明なタイトル',
                'u_user_id' => $userNameMap[$userTitle['u_user_id']] ?? '不明なユーザー',
                'value' => $userTitle['value'],
            ];
        }
        krsort($groupedTitles);

        return $groupedTitles;
    }

    public function getRankHistory(): array
    {
        $tierHistoryByUser = [];
        $rankHistory = [];

        foreach ($this->tierHistoryRecords as $tierHistoryRecord) {
            $userId = $tierHistoryRecord['u_user_id'] ?? 0;
            $year = $tierHistoryRecord['year'] ?? '';
            $tierId = $tierHistoryRecord['m_tier_id'] ?? 0;

            if ($year && $tierId && isset($this->tierDefinitions[$tierId])) {
                $tierHistoryByUser[$userId][$year] = [
                    'tier' => $tierId,
                    'name' => $this->tierDefinitions[$tierId]['name'],
                    'color' => $this->tierDefinitions[$tierId]['color'],
                ];
            }
        }

        foreach ($tierHistoryByUser as $userId => $yearList) {
            foreach ($yearList as $year => $tierData) {
                $previousYear = (int) $year - 1;
                $previousTierData = $yearList[$previousYear] ?? [
                    'tier' => 0,
                    'name' => 'なし',
                    'color' => \App\Support\Constants\AppConstants::DEFAULT_TIER_COLOR,
                ];

                $rankHistory[$userId][$year] = [
                    'before' => $previousTierData,
                    'after' => $tierData,
                ];
            }
        }

        return $rankHistory;
    }

    public function getTodayStatsList(): array
    {
        return $this->statsPresenter->addRankings(
            $this->statsPresenter->formatUserStats(
                $this->calculateScoreStats(\App\Support\Constants\AppConstants::ALL_TERM_LABEL, true)
            )
        );
    }

    public function hasTodayStats(): bool
    {
        $todayStatsList = $this->getTodayStatsList();
        $firstRow = reset($todayStatsList);

        return (int)($firstRow['play_count'] ?? 0) > 0;
    }

    public function getYearlyStatsList(): array
    {
        $statsByTerm = [];
        foreach ($this->availableTerms as $term) {
            $statsByTerm[$term] = $this->statsPresenter->addRankings(
                $this->statsPresenter->formatUserStats($this->calculateScoreStats($term))
            );
        }

        return $statsByTerm;
    }

    public function getStatsByTermList(): array
    {
        $statsByTerm = $this->getYearlyStatsList();
        if ($this->hasTodayStats()) {
            $statsByTerm[\App\Support\Constants\AppConstants::TODAY_TERM] = $this->getTodayStatsList();
        }

        return $statsByTerm;
    }


    public function getAnalysisTerms(): array
    {
        return array_merge(
            [\App\Support\Constants\AppConstants::ALL_TERM_LABEL],
            $this->getYearTerms()
        );
    }

    public function isValidAnalysisTerm(string $term): bool
    {
        return in_array($term, $this->getAnalysisTerms(), true);
    }

    public function getAnalysisDataByTerm(string $term): array
    {
        $statsByUser = $this->calculateScoreStats($term);
        $analysisDataByUser = [];

        foreach ($this->userList as $userId => $userData) {
            $filteredHistoryRows = $this->filterGameHistoryRowsByTerm($this->gameHistoryByUser[$userId] ?? [], $term);
            usort($filteredHistoryRows, static function (array $left, array $right): int {
                $leftTimestamp = strtotime((string)($left['play_date'] ?? '')) ?: 0;
                $rightTimestamp = strtotime((string)($right['play_date'] ?? '')) ?: 0;

                if ($leftTimestamp !== $rightTimestamp) {
                    return $rightTimestamp <=> $leftTimestamp;
                }

                $leftGame = (int)($left['game'] ?? 0);
                $rightGame = (int)($right['game'] ?? 0);
                if ($leftGame !== $rightGame) {
                    return $rightGame <=> $leftGame;
                }

                return (int)($right['u_game_history_id'] ?? 0) <=> (int)($left['u_game_history_id'] ?? 0);
            });

            $trimmedHistoryRows = array_slice($filteredHistoryRows, 0, 500);
            $analysisDataByUser[$userId] = array_merge(
                $statsByUser[$userId] ?? [],
                [
                    'history_total_count' => count($filteredHistoryRows),
                    'history_included_count' => count($trimmedHistoryRows),
                    'recent_games' => array_map(
                        fn(array $historyRow): array => $this->normalizeAnalysisHistoryRow($historyRow),
                        $trimmedHistoryRows
                    ),
                ]
            );
        }

        return $analysisDataByUser;
    }

    public function getDayStats(): array
    {
        return $this->historyOrganizer->buildDayStats($this->userList, $this->gameHistoryByUser);
    }

    public function getGameHistoryList(): array
    {
        return $this->historyOrganizer->buildGameHistoryList($this->userList, $this->gameHistoryByUser);
    }

    public function getYearlyChartList(): array
    {
        return $this->historyOrganizer->buildYearlyChartList($this->availableTerms, $this->userList, $this->gameHistoryByUser);
    }

    public function getChartByTermList(): array
    {
        $terms = $this->availableTerms;
        if ($this->hasTodayStats()) {
            $terms[] = \App\Support\Constants\AppConstants::TODAY_TERM;
        }

        return $this->historyOrganizer->buildYearlyChartList($terms, $this->userList, $this->gameHistoryByUser);
    }

    public function getRelativeScoreByDirection(): array
    {
        return $this->historyOrganizer->buildRelativeScoreByDirection($this->gameHistoryByUser);
    }

    public function getNextTwoGameDays(): array
    {
        return $this->scheduleService->nextTwoGameDays($this->scheduledGameDays);
    }

    public function getDefaultStatsTerm(): string
    {
        if ($this->hasTodayStats()) {
            return \App\Support\Constants\AppConstants::TODAY_TERM;
        }

        $yearTerms = $this->getYearTerms();
        if (!empty($yearTerms)) {
            return (string)$yearTerms[0];
        }

        return \App\Support\Constants\AppConstants::ALL_TERM_LABEL;
    }

    public function getScoreTotalUnits(): int
    {
        return (int)(($this->baseScore * \App\Support\Constants\AppConstants::PLAYER_COUNT) / \App\Support\Constants\AppConstants::SCORE_INPUT_MULTIPLIER);
    }

    public function getScoreTotal(): int
    {
        return $this->baseScore * \App\Support\Constants\AppConstants::PLAYER_COUNT;
    }

    public function isValidStatsTerm(string $term): bool
    {
        if ($term === \App\Support\Constants\AppConstants::TODAY_TERM) {
            return $this->hasTodayStats();
        }

        return in_array($term, $this->availableTerms, true);
    }

    private function calculateScoreStats(string $term, bool $today = false): array
    {
        return $this->scoreAggregator->calculate($this->userList, $this->gameHistoryByUser, $term, $today);
    }

    private function filterGameHistoryRowsByTerm(array $historyRows, string $term): array
    {
        if ($term === \App\Support\Constants\AppConstants::ALL_TERM_LABEL) {
            return $historyRows;
        }

        return array_values(array_filter($historyRows, static function (array $historyRow) use ($term): bool {
            $historyTimestamp = strtotime((string)($historyRow['play_date'] ?? ''));
            if ($historyTimestamp === false) {
                return false;
            }

            return date('Y', $historyTimestamp) === $term;
        }));
    }

    private function normalizeAnalysisHistoryRow(array $historyRow): array
    {
        return [
            'play_date' => date('Y-m-d', strtotime((string)($historyRow['play_date'] ?? ''))),
            'game' => (int)($historyRow['game'] ?? 0),
            'direction' => $this->directionDefinitions[(int)($historyRow['m_direction_id'] ?? 0)]['name'] ?? '',
            'rank' => (string)($historyRow['rank'] ?? ''),
            'score' => (int)($historyRow['score'] ?? 0),
            'point' => round((float)($historyRow['point'] ?? 0), 1),
            'mistake_count' => (int)($historyRow['mistake_count'] ?? 0),
        ];
    }

    private function initializeAvailableTerms(): void
    {
        $yearSet = [];
        foreach ($this->gameHistoryByUser as $historyRows) {
            foreach ($historyRows as $historyRow) {
                $yearSet[(string)date('Y', strtotime($historyRow['play_date']))] = true;
            }
        }

        $this->availableTerms = array_map('strval', array_keys($yearSet));
        rsort($this->availableTerms);
        $this->availableTerms[] = \App\Support\Constants\AppConstants::ALL_TERM_LABEL;
    }
}



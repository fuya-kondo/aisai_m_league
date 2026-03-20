<?php
/**
 * 履歴画面専用の page data builder。
 * 対局日別の表示用構造と、個人履歴一覧のページネーション情報を生成する。
 */
class HistoryPageDataBuilder extends MainPageDataBuilder
{
    public function handleDeleteRequest(array $postData, string $requestMethod): array
    {
        if ($requestMethod !== 'POST') {
            return ['redirectUrl' => null, 'errorMessage' => null];
        }

        if (isset($postData['bulkDelete'], $postData['tableId'], $postData['playDate'], $postData['game'])) {
            $tableId = (int)$postData['tableId'];
            $playDate = (string)$postData['playDate'];
            $game = (int)$postData['game'];
            $batchRows = $this->gameHistoryService->findBatch($tableId, $playDate, $game);

            if (count($batchRows) !== \App\Support\Constants\AppConstants::PLAYER_COUNT) {
                return ['redirectUrl' => null, 'errorMessage' => '対象半荘の4件データを取得できませんでした。'];
            }

            if ($this->gameHistoryService->deleteBatch($tableId, $playDate, $game)) {
                return ['redirectUrl' => 'history?view=overview', 'errorMessage' => null];
            }

            return ['redirectUrl' => null, 'errorMessage' => '一括削除処理中にエラーが発生しました。'];
        }

        if (!isset($postData['historyId'], $postData['userId'])) {
            return ['redirectUrl' => null, 'errorMessage' => null];
        }

        $historyId = (int)$postData['historyId'];
        $userId = (string)$postData['userId'];
        if ($this->gameHistoryService->delete($historyId)) {
            return ['redirectUrl' => 'history?userId=' . urlencode($userId), 'errorMessage' => null];
        }

        return ['redirectUrl' => null, 'errorMessage' => '削除処理中にエラーが発生しました。'];
    }

    public function build(?string $selectedUser, string $selectedView, string $selectedYear, int $currentPage, ?string $errorMessage): array
    {
        $userList = $this->statsService->getUserList();
        $directionList = $this->getDirectionList();
        $userHistoryList = $this->masterData['uGameHistoryList'] ?? [];
        $gameHistoryList = $this->statsService->getGameHistoryList();
        $dayStatsList = $this->statsService->getDayStats();

        $recordsPerPage = \App\Support\Constants\AppConstants::HISTORY_USER_RECORDS_PER_PAGE;
        $offset = ($currentPage - \App\Support\Constants\AppConstants::FIRST_PAGE) * $recordsPerPage;
        $selectedUserRows = [];
        $selectedUserPagination = ['links' => [], 'currentPage' => $currentPage, 'totalPages' => 0];
        $selectedUserInfoText = '';

        if ($selectedUser !== null && isset($userHistoryList[$selectedUser])) {
            $filteredRows = [];
            foreach ($userHistoryList[$selectedUser] as $historyRow) {
                if ($this->isHistoryWithinSelectedRange($historyRow, $selectedYear)) {
                    $filteredRows[] = $historyRow;
                }
            }

            usort($filteredRows, static function (array $left, array $right): int {
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

            $totalRecords = count($filteredRows);
            $totalPages = (int)ceil($totalRecords / $recordsPerPage);
            $selectedUserRows = $this->buildSelectedUserRows(array_slice($filteredRows, $offset, $recordsPerPage));
            $selectedUserPagination = $this->buildPagination($currentPage, $totalPages, [
                'year' => $selectedYear,
                'userId' => $selectedUser,
            ]);
            $selectedUserInfoText = $this->buildSelectedUserInfoText($totalRecords, $offset, $recordsPerPage, $currentPage, $totalPages);
        }

        $dateGroups = $this->buildDateGroups($gameHistoryList, $dayStatsList, $userList, $directionList, $currentPage);

        return $this->withTitle('履歴', [
            'pageTabs' => $this->buildHistoryTabs($selectedView, $selectedYear, $selectedUser),
            'errorMessage' => $errorMessage,
            'selectedUser' => $selectedUser,
            'selectedView' => $selectedView,
            'selectedYear' => $selectedYear,
            'userButtons' => $this->buildUserButtons($userList),
            'overviewDateGroups' => $dateGroups['items'],
            'overviewPagination' => $dateGroups['pagination'],
            'overviewInfoText' => $dateGroups['infoText'],
            'selectedUserName' => $selectedUser !== null && isset($userList[$selectedUser])
                ? $userList[$selectedUser]['last_name'] . $userList[$selectedUser]['first_name']
                : null,
            'selectedUserRows' => $selectedUserRows,
            'selectedUserPagination' => $selectedUserPagination,
            'selectedUserInfoText' => $selectedUserInfoText,
        ]);
    }

    private function buildHistoryTabs(string $selectedView, string $selectedYear, ?string $selectedUser): array
    {
        $personalQuery = ['view' => 'personal', 'year' => $selectedYear];
        if ($selectedUser !== null && $selectedUser !== '') {
            $personalQuery['userId'] = $selectedUser;
        }

        return [
            ['label' => '対局履歴', 'href' => 'history?view=overview', 'active' => $selectedView !== 'personal'],
            ['label' => '個人履歴', 'href' => 'history?' . http_build_query($personalQuery), 'active' => $selectedView === 'personal'],
        ];
    }

    private function buildUserButtons(array $userList): array
    {
        $buttons = [];
        foreach ($userList as $userId => $userData) {
            $buttons[] = [
                'userId' => $userId,
                'label' => $userData['last_name'] . $userData['first_name'],
            ];
        }

        return $buttons;
    }

    private function buildDateGroups(array $gameHistoryList, array $dayStatsList, array $userList, array $directionList, int $currentPage): array
    {
        $datesPerPage = \App\Support\Constants\AppConstants::HISTORY_OVERVIEW_DATES_PER_PAGE;
        $allDates = array_keys($gameHistoryList);
        $totalDates = count($allDates);
        $totalPages = (int)ceil($totalDates / $datesPerPage);
        $offset = ($currentPage - \App\Support\Constants\AppConstants::FIRST_PAGE) * $datesPerPage;
        $currentDates = array_slice($allDates, $offset, $datesPerPage);
        $items = [];

        foreach ($currentDates as $date) {
            $items[] = [
                'date' => $date,
                'dayStats' => $this->buildDayStatsRows($dayStatsList[$date] ?? [], $userList),
                'games' => $this->buildGameSessions($gameHistoryList[$date] ?? [], $userList, $directionList),
            ];
        }

        return [
            'items' => $items,
            'pagination' => $this->buildPagination($currentPage, $totalPages),
            'infoText' => $this->buildOverviewInfoText($totalDates, $offset, $datesPerPage, $currentPage, $totalPages),
        ];
    }

    private function buildDayStatsRows(array $dayStats, array $userList): array
    {
        $rows = [];
        foreach ($dayStats as $userId => $value) {
            $rows[] = [
                'userName' => $userList[$userId]['last_name'] ?? (string)$userId,
                'value' => $value,
            ];
        }

        return $rows;
    }

    private function buildGameSessions(array $gamesByDate, array $userList, array $directionList): array
    {
        uksort($gamesByDate, static function ($left, $right): int {
            return (int)$right <=> (int)$left;
        });
        $sessions = [];

        foreach ($gamesByDate as $gameNumber => $gameRows) {
            $sortedRows = $gameRows;
            usort($sortedRows, function (array $left, array $right): int {
                return $this->normalizeRankValue((string)($left['rank'] ?? '')) <=> $this->normalizeRankValue((string)($right['rank'] ?? ''));
            });

            $sumScore = 0;
            $sumRank = 0;
            $sumDirection = 0;
            $canValidateGame = true;
            $rows = [];
            $tableIds = [];
            $historyIds = [];
            $playDate = '';

            foreach ($sortedRows as $historyRow) {
                $rank = (string)($historyRow['rank'] ?? '');
                if (mb_strlen($rank) === 3) {
                    $canValidateGame = false;
                }

                if ($canValidateGame) {
                    $sumScore += (int)($historyRow['score'] ?? 0);
                    $sumRank += (int)$rank;
                    $sumDirection += (int)($historyRow['m_direction_id'] ?? 0);
                }

                $tableIds[] = (int)($historyRow['u_table_id'] ?? 0);
                $historyIds[] = (int)($historyRow['u_game_history_id'] ?? 0);
                if ($playDate === '' && !empty($historyRow['play_date'])) {
                    $playDate = date('Y-m-d', strtotime((string)$historyRow['play_date']));
                }

                $rows[] = [
                    'rankClass' => $this->resolveOverviewRankClass((string)($historyRow['rank'] ?? '')),
                    'directionName' => $directionList[$historyRow['m_direction_id']]['name'] ?? '',
                    'rank' => $historyRow['rank'] ?? '',
                    'playerName' => $userList[$historyRow['u_user_id']]['last_name'] ?? '',
                    'score' => number_format((int)($historyRow['score'] ?? 0)),
                    'point' => $historyRow['point'] ?? '',
                ];
            }

            $warnings = [];
            if ($canValidateGame) {
                if ($sumScore !== $this->statsService->getScoreTotal()) {
                    $warnings[] = '点数が正しくないです';
                }
                if ($sumRank !== 10) {
                    $warnings[] = '順位が正しくないです';
                }
                if ($sumDirection !== 10 && $sumDirection !== 0) {
                    $warnings[] = '席が正しくないです';
                }
            }

            $uniqueTableIds = array_values(array_unique(array_filter($tableIds, static fn(int $tableId): bool => $tableId > 0)));
            $canBatchOperate = count($sortedRows) === \App\Support\Constants\AppConstants::PLAYER_COUNT
                && count($uniqueTableIds) === 1
                && $playDate !== '';

            $sessions[] = [
                'gameNumber' => $gameNumber,
                'rows' => $rows,
                'warnings' => $warnings,
                'tableId' => $uniqueTableIds[0] ?? 0,
                'playDate' => $playDate,
                'historyIds' => $historyIds,
                'canBatchOperate' => $canBatchOperate,
            ];
        }

        return $sessions;
    }

    private function buildSelectedUserRows(array $historyRows): array
    {
        $rows = [];
        foreach ($historyRows as $historyRow) {
            $rank = (string)($historyRow['rank'] ?? '');
            $rankDisplay = mb_strlen($rank) === 3 ? '同率' . substr($rank, 0, 1) : $rank;
            $rows[] = [
                'historyId' => $historyRow['u_game_history_id'],
                'userId' => $historyRow['u_user_id'],
                'rank' => $rank,
                'rankDisplay' => $rankDisplay,
                'rankClass' => $this->resolveSelectedUserRankClass($rank),
                'score' => $historyRow['score'],
                'scoreClass' => (int)$historyRow['score'] < 0 ? 'red-text' : '',
                'point' => $historyRow['point'],
                'pointClass' => (float)$historyRow['point'] < 0 ? 'red-text' : '',
                'playDate' => date('Y/m/d', strtotime($historyRow['play_date'])),
                'game' => $historyRow['game'],
                'direction' => $historyRow['m_direction_id'],
            ];
        }

        return $rows;
    }

    private function resolveSelectedUserRankClass(string $rank): string
    {
        return match ($rank) {
            '1', '1=1' => 'green-text',
            '4', '3=3' => 'red-text',
            default => '',
        };
    }

    private function resolveOverviewRankClass(string $rank): string
    {
        return match ($rank) {
            '1', '1=1' => 'rank-1',
            '4', '3=3' => 'rank-4',
            default => 'rank-' . $rank,
        };
    }

    private function buildOverviewInfoText(int $totalDates, int $offset, int $datesPerPage, int $currentPage, int $totalPages): string
    {
        if ($totalDates === 0) {
            return '対局日の履歴はありません。';
        }

        $start = $offset + 1;
        $end = min($offset + $datesPerPage, $totalDates);
        return sprintf('%d日分中 %d～%d日目表示 (%d/%dページ)', $totalDates, $start, $end, $currentPage, max($totalPages, 1));
    }

    private function buildSelectedUserInfoText(int $totalRecords, int $offset, int $recordsPerPage, int $currentPage, int $totalPages): string
    {
        if ($totalRecords === 0) {
            return '該当する履歴はありません。';
        }

        $start = $offset + 1;
        $end = min($offset + $recordsPerPage, $totalRecords);
        return sprintf('%d件中 %d-%d件表示 (%d/%dページ)', $totalRecords, $start, $end, $currentPage, max($totalPages, 1));
    }

    private function isHistoryWithinSelectedRange(array $historyRow, string $selectedYear): bool
    {
        $historyYear = (int)date('Y', strtotime($historyRow['play_date']));
        $targetYear = (int)$selectedYear;
        return $historyYear === $targetYear || $historyYear === $targetYear - 1;
    }

    private function normalizeRankValue(string $rank): int
    {
        if ($rank === '') {
            return 99;
        }

        if (mb_strlen($rank) === 3) {
            return (int)mb_substr($rank, 0, 1);
        }

        return (int)$rank;
    }
}

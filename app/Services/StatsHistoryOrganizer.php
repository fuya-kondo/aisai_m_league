<?php
namespace App\Services;

use App\Support\Constants\AppConstants;
use DateTime;

/**
 * 履歴の並べ替え・日別再編成・席順比較など、履歴構造の整形を担当する service。
 */
final class StatsHistoryOrganizer
{
    public function buildDayStats(array $userList, array $gameHistoryByUser): array
    {
        $dailyStats = [];
        foreach ($userList as $userId => $userData) {
            foreach ($gameHistoryByUser[$userId] ?? [] as $historyRow) {
                if (empty($historyRow['game'])) {
                    continue;
                }
                $dateKey = (new DateTime($historyRow['play_date']))->format('Y-m-d');
                $dailyStats[$dateKey][$userId] = ($dailyStats[$dateKey][$userId] ?? 0) + $historyRow['point'];
            }
        }
        return $dailyStats;
    }

    public function buildGameHistoryList(array $userList, array $gameHistoryByUser): array
    {
        $gameHistoryList = [];
        foreach ($userList as $userId => $userData) {
            foreach ($gameHistoryByUser[$userId] ?? [] as $historyRow) {
                if (empty($historyRow['game'])) {
                    continue;
                }
                $dateKey = (new DateTime($historyRow['play_date']))->format('Y-m-d');
                $gameHistoryList[$dateKey][$historyRow['game']][$userId] = $historyRow;
            }
        }

        foreach ($gameHistoryList as &$gamesByDate) {
            uksort($gamesByDate, static function ($left, $right): int {
                return intval($right) <=> intval($left);
            });
        }
        unset($gamesByDate);

        return $gameHistoryList;
    }

    public function buildYearlyChartList(array $availableTerms, array $userList, array $gameHistoryByUser): array
    {
        $yearlyChartList = [];
        foreach ($availableTerms as $term) {
            $statsByUser = [];
            foreach ($userList as $userId => $userData) {
                foreach ($gameHistoryByUser[$userId] ?? [] as $historyRow) {
                    if ($this->matchesTerm($historyRow, $term)) {
                        $statsByUser[$userId][] = $historyRow;
                    }
                }
            }
            $yearlyChartList[$term] = $statsByUser;
        }
        return $yearlyChartList;
    }

    public function buildRelativeScoreByDirection(array $gameHistoryByUser): array
    {
        $historyByDateAndDirection = [];
        foreach ($gameHistoryByUser as $userId => $historyRows) {
            foreach ($historyRows as $historyRow) {
                if ((int) $historyRow['m_direction_id'] === 0) {
                    continue;
                }
                $dateKey = (new DateTime($historyRow['play_date']))->format('Y-m-d');
                $historyByDateAndDirection[$dateKey][$historyRow['game']][$historyRow['m_direction_id']] = $historyRow;
            }
        }

        $relativeStats = ['upper' => [], 'lower' => []];
        foreach ($historyByDateAndDirection as $dateKey => $gamesByDate) {
            foreach ($gamesByDate as $gameNumber => $rowsByDirection) {
                if (count($rowsByDirection) !== AppConstants::PLAYER_COUNT) {
                    continue;
                }
                foreach ($rowsByDirection as $directionId => $historyRow) {
                    $currentUserId = $historyRow['u_user_id'];
                    $upperDirectionId = $directionId === 1 ? AppConstants::PLAYER_COUNT : $directionId - 1;
                    $lowerDirectionId = $directionId === AppConstants::PLAYER_COUNT ? 1 : $directionId + 1;
                    $upperUserId = $rowsByDirection[$upperDirectionId]['u_user_id'];
                    $lowerUserId = $rowsByDirection[$lowerDirectionId]['u_user_id'];

                    if (!isset($relativeStats['upper'][$currentUserId][$upperUserId])) {
                        $relativeStats['upper'][$currentUserId][$upperUserId] = ['sub_score' => 0, 'sub_point' => 0];
                    }
                    if (!isset($relativeStats['lower'][$currentUserId][$lowerUserId])) {
                        $relativeStats['lower'][$currentUserId][$lowerUserId] = ['sub_score' => 0, 'sub_point' => 0];
                    }

                    $relativeStats['upper'][$currentUserId][$upperUserId]['sub_score'] += $historyRow['score'] - $rowsByDirection[$upperDirectionId]['score'];
                    $relativeStats['upper'][$currentUserId][$upperUserId]['sub_point'] = round(
                        $relativeStats['upper'][$currentUserId][$upperUserId]['sub_point'] + ($historyRow['point'] - $rowsByDirection[$upperDirectionId]['point']),
                        1
                    );

                    $relativeStats['lower'][$currentUserId][$lowerUserId]['sub_score'] += $historyRow['score'] - $rowsByDirection[$lowerDirectionId]['score'];
                    $relativeStats['lower'][$currentUserId][$lowerUserId]['sub_point'] = round(
                        $relativeStats['lower'][$currentUserId][$lowerUserId]['sub_point'] + ($historyRow['point'] - $rowsByDirection[$lowerDirectionId]['point']),
                        1
                    );
                }
            }
        }

        return $relativeStats;
    }

    private function matchesTerm(array $historyRow, string $term): bool
    {
        $historyTimestamp = strtotime((string)($historyRow['play_date'] ?? ''));
        if ($historyTimestamp === false) {
            return false;
        }

        if ($term === AppConstants::TODAY_TERM) {
            return date('Y-m-d', $historyTimestamp) === date('Y-m-d');
        }

        if ($term === AppConstants::ALL_TERM_LABEL) {
            return true;
        }

        return date('Y', $historyTimestamp) === $term;
    }
}

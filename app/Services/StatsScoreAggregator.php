<?php
namespace App\Services;

use App\Support\Constants\AppConstants;

/**
 * プレイヤー別成績の集計専用 service。
 * 年や当日条件で履歴を絞り、順位率や平均点などの数値を算出する。
 */
final class StatsScoreAggregator
{
    public function __construct(private readonly int $baseScore)
    {
    }

    public function calculate(array $userList, array $gameHistoryByUser, string $term, bool $today = false): array
    {
        $statsByUser = [];
        foreach ($userList as $userId => $userData) {
            $statsByUser[$userId] = $this->createInitialUserStats($userData);
            $filteredHistoryRows = $this->filterGameHistoryByTerm($gameHistoryByUser[$userId] ?? [], $term, $today);
            $statsByUser[$userId] = $this->accumulateUserStats($statsByUser[$userId], $filteredHistoryRows);
        }

        return $statsByUser;
    }

    private function createInitialUserStats(array $userData): array
    {
        return [
            'u_user_id' => $userData['u_user_id'],
            'name' => $userData['last_name'] . $userData['first_name'],
            'play_count' => 0,
            'rank_count' => array_fill(1, AppConstants::PLAYER_COUNT, 0),
            'rank_probability' => array_fill(1, AppConstants::PLAYER_COUNT, 0),
            'average_rank' => 0,
            'sum_base_score' => 0,
            'average_score' => 0,
            'sum_point' => 0,
            'average_point' => 0,
            'hight_score' => 0,
            'play_count_direction' => $this->buildDirectionalTotals(),
            'rank_count_direction' => $this->buildDirectionalRankMatrix(),
            'rank_probability_direction' => $this->buildDirectionalRankMatrix(),
            'average_rank_direction' => $this->buildDirectionalTotals(),
            'sum_base_score_direction' => $this->buildDirectionalTotals(),
            'average_score_direction' => $this->buildDirectionalTotals(),
            'sum_point_direction' => $this->buildDirectionalTotals(),
            'average_point_direction' => $this->buildDirectionalTotals(),
            'over_second_probability' => 0,
            'over_third_probability' => 0,
            'mistake_count' => 0,
        ];
    }

    private function filterGameHistoryByTerm(array $gameHistoryRows, string $term, bool $today): array
    {
        $filteredRows = [];
        foreach ($gameHistoryRows as $historyRow) {
            $historyTimestamp = strtotime($historyRow['play_date']);
            $isMatched = false;

            if ($today && date('Y-m-d', $historyTimestamp) === date('Y-m-d')) {
                $isMatched = true;
            } elseif (!$today && $term === AppConstants::ALL_TERM_LABEL) {
                $isMatched = true;
            } elseif (!$today && date('Y', $historyTimestamp) === (string) $term) {
                $isMatched = true;
            }

            if ($isMatched) {
                $filteredRows[] = $historyRow;
            }
        }

        return $filteredRows;
    }

    private function accumulateUserStats(array $userStats, array $gameHistoryRows): array
    {
        $rankTotal = 0;
        $scoreTotal = 0;
        $rankTotalsByDirection = $this->buildDirectionalTotals();
        $scoreTotalsByDirection = $this->buildDirectionalTotals();

        foreach ($gameHistoryRows as $historyRow) {
            $userStats['play_count']++;
            $userStats['sum_point'] = round($userStats['sum_point'] + (float)$historyRow['point'], 1);
            $scoreTotal += $historyRow['score'];
            $userStats['sum_base_score'] += ($historyRow['score'] - $this->baseScore) / 1000;
            $userStats['mistake_count'] += $historyRow['mistake_count'];
            $userStats['hight_score'] = max($historyRow['score'], $userStats['hight_score']);

            $rank = (int) substr((string) $historyRow['rank'], 0, 1);
            $rankTotal += $rank;
            $userStats['rank_count'][$rank]++;

            $directionId = (int) ($historyRow['m_direction_id'] ?? 0);
            if ($directionId >= 1 && $directionId <= AppConstants::PLAYER_COUNT) {
                $userStats['play_count_direction'][$directionId]++;
                $userStats['rank_count_direction'][$directionId][$rank]++;
                $rankTotalsByDirection[$directionId] += $rank;
                $userStats['sum_base_score_direction'][$directionId] += ($historyRow['score'] - $this->baseScore) / 1000;
                $scoreTotalsByDirection[$directionId] += $historyRow['score'];
                $userStats['sum_point_direction'][$directionId] = round($userStats['sum_point_direction'][$directionId] + (float)$historyRow['point'], 1);
            }
        }

        if ($userStats['play_count'] > 0) {
            foreach ($userStats['rank_count'] as $rank => $count) {
                $userStats['rank_probability'][$rank] = $count / $userStats['play_count'] * 100;
            }
            $userStats['average_rank'] = $rankTotal / $userStats['play_count'];
            $userStats['average_score'] = $scoreTotal / $userStats['play_count'];
            $userStats['average_point'] = $userStats['sum_point'] / $userStats['play_count'];
            $userStats['over_second_probability'] = ($userStats['rank_count'][1] + $userStats['rank_count'][2]) / $userStats['play_count'] * 100;
            $userStats['over_third_probability'] = 100 - ($userStats['rank_count'][AppConstants::PLAYER_COUNT] / $userStats['play_count'] * 100);
        }

        foreach ($userStats['play_count_direction'] as $directionId => $playCount) {
            if ($playCount <= 0) {
                continue;
            }

            $userStats['average_rank_direction'][$directionId] = $rankTotalsByDirection[$directionId] / $playCount;
            foreach ($userStats['rank_count_direction'][$directionId] as $rank => $count) {
                $userStats['rank_probability_direction'][$directionId][$rank] = $count / $playCount * 100;
            }
            $userStats['average_score_direction'][$directionId] = $scoreTotalsByDirection[$directionId] / $playCount;
            $userStats['average_point_direction'][$directionId] = $userStats['sum_point_direction'][$directionId] / $playCount;
        }

        if ($userStats['mistake_count'] > 0) {
            $userStats['sum_point'] = round($userStats['sum_point'] - $userStats['mistake_count'] * AppConstants::MISTAKE_POINT_PENALTY, 1);
        }

        return $userStats;
    }

    private function buildDirectionalTotals(): array
    {
        return array_fill(1, AppConstants::PLAYER_COUNT, 0);
    }

    private function buildDirectionalRankMatrix(): array
    {
        $rankMatrix = [];
        for ($directionId = 1; $directionId <= AppConstants::PLAYER_COUNT; $directionId++) {
            $rankMatrix[$directionId] = array_fill(1, AppConstants::PLAYER_COUNT, 0);
        }

        return $rankMatrix;
    }
}

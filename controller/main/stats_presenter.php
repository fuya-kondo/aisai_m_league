<?php
/**
 * StatsService が集計した値を画面表示向けに整形するプレゼンター。
 * 数値フォーマットとランキング付与だけを担当し、集計ロジックから分離する。
 */

class StatsPresenter
{
    /**
     * 画面表示向けの文字列表現へ変換する。
     */
    public function formatUserStats(array $statsData): array
    {
        foreach ($statsData as &$userData) {
            if (isset($userData['play_count']) && $userData['play_count'] > 0) {
                $userData['sum_point'] = number_format($userData['sum_point'], 1);
                foreach ($userData['rank_probability'] as $rank => $probability) {
                    $userData['rank_probability'][$rank] = number_format($probability, 2) . '%';
                }
                $userData['average_point'] = number_format($userData['average_point'], 1);
                $userData['sum_base_score'] = number_format($userData['sum_base_score'], 1);
                $userData['average_score'] = str_replace(',', '', number_format($userData['average_score'], 0));
                $userData['average_rank'] = number_format($userData['average_rank'], 2);
                $userData['over_second_probability'] = number_format($userData['over_second_probability'], 2) . '%';
                $userData['over_third_probability'] = number_format($userData['over_third_probability'], 2) . '%';
            }

            if (isset($userData['play_count']) && $userData['play_count'] > 0) {
                foreach ($userData['average_rank_direction'] as $directionId => $value) {
                    $userData['average_rank_direction'][$directionId] = number_format($value, 2);
                }
                foreach ($userData['rank_probability_direction'] as $directionId => $probabilities) {
                    foreach ($probabilities as $rank => $value) {
                        $userData['rank_probability_direction'][$directionId][$rank] = number_format($value, 2) . '%';
                    }
                }
                foreach ($userData['average_score_direction'] as $directionId => $value) {
                    $userData['average_score_direction'][$directionId] = number_format($value, 0);
                }
                foreach ($userData['average_point_direction'] as $directionId => $value) {
                    $userData['average_point_direction'][$directionId] = number_format($value, 1);
                }
            }
        }
        unset($userData);

        return $statsData;
    }

    /**
     * 合計ポイント順の表示順位を付与する。
     */
    public function addRankings(array $statsData): array
    {
        uasort($statsData, static function ($left, $right) {
            $leftPoint = (float)str_replace(['%', ','], '', (string)$left['sum_point']);
            $rightPoint = (float)str_replace(['%', ','], '', (string)$right['sum_point']);

            if ($leftPoint === $rightPoint) {
                return 0;
            }

            return $leftPoint > $rightPoint ? -1 : 1;
        });

        $ranking = 1;
        foreach ($statsData as &$userData) {
            $userData['ranking'] = $ranking++;
        }
        unset($userData);

        return $statsData;
    }
}

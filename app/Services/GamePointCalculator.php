<?php
namespace App\Services;

/**
 * 順位と点数、ルール設定から league point を計算する純粋ロジック。
 */
final class GamePointCalculator
{
    public function calculate(string $rank, int $score, array $ruleConfig = []): float|int
    {
        $targetScore = isset($ruleConfig['end_score']) ? (int)$ruleConfig['end_score'] : 30000;
        $points = $this->buildRankPointMap($ruleConfig);
        $rankPoint = $points[$rank] ?? 0.0;
        $point = ($score - $targetScore) / 1000 + $rankPoint;

        return round((float)$point, 1);
    }

    private function buildRankPointMap(array $ruleConfig): array
    {
        $point1 = isset($ruleConfig['point_1']) ? (float)$ruleConfig['point_1'] : 50.0;
        $point2 = isset($ruleConfig['point_2']) ? (float)$ruleConfig['point_2'] : 10.0;
        $point3 = isset($ruleConfig['point_3']) ? (float)$ruleConfig['point_3'] : -10.0;
        $point4 = isset($ruleConfig['point_4']) ? (float)$ruleConfig['point_4'] : -30.0;

        return [
            '1' => $point1,
            '1=1' => ($point1 + $point2) / 2,
            '2' => $point2,
            '2=2' => ($point2 + $point3) / 2,
            '3' => $point3,
            '3=3' => ($point3 + $point4) / 2,
            '4' => $point4,
        ];
    }
}

<?php
namespace App\Services;

use DateTimeImmutable;
use Throwable;

/**
 * 開催予定日の抽出と表示整形を担当する service。
 */
final class ScheduleService
{
    public function nextTwoGameDays(array $gameDayList): array
    {
        $todayStart = (new DateTimeImmutable())->setTime(0, 0, 0);
        $futureDates = [];

        foreach ($gameDayList as $gameDayData) {
            if (!isset($gameDayData['game_day']) || !is_string($gameDayData['game_day'])) {
                continue;
            }

            try {
                $gameDateTime = new DateTimeImmutable($gameDayData['game_day']);
                if ($gameDateTime->setTime(0, 0, 0) >= $todayStart) {
                    $futureDates[] = $gameDateTime;
                }
            } catch (Throwable $throwable) {
                error_log('Failed to parse game_day date string: ' . $throwable->getMessage());
            }
        }

        if (empty($futureDates)) {
            return [];
        }

        usort($futureDates, static fn(DateTimeImmutable $left, DateTimeImmutable $right): int => $left <=> $right);
        $weekdays = ['日', '月', '火', '水', '木', '金', '土'];

        $result = [];
        foreach ($futureDates as $index => $gameDateTime) {
            if ($index >= 2) {
                break;
            }
            $result[] = $gameDateTime->format('n/j') . '(' . $weekdays[(int)$gameDateTime->format('w')] . ')';
        }

        return $result;
    }
}

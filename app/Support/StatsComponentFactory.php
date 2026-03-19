<?php
namespace App\Support;

use App\Services\ScheduleService;
use App\Services\StatsHistoryOrganizer;
use App\Services\StatsScoreAggregator;

/**
 * StatsService 周辺の補助 service をまとめて生成する factory。
 */
final class StatsComponentFactory
{
    public static function createPresenter(): \StatsPresenter
    {
        return new \StatsPresenter();
    }

    public static function createScoreAggregator(int $baseScore): StatsScoreAggregator
    {
        return new StatsScoreAggregator($baseScore);
    }

    public static function createHistoryOrganizer(): StatsHistoryOrganizer
    {
        return new StatsHistoryOrganizer();
    }

    public static function createScheduleService(): ScheduleService
    {
        return new ScheduleService();
    }
}

<?php
namespace App\Support\Constants;

final class AppConstants
{
    public const AGGREGATE_TABLE_ID = 1;
    public const PLAYER_COUNT = 4;
    public const FIRST_PAGE = 1;
    public const HISTORY_OVERVIEW_DATES_PER_PAGE = 1;
    public const HISTORY_USER_RECORDS_PER_PAGE = 15;
    public const SCORE_INPUT_MULTIPLIER = 100;
    public const MISTAKE_POINT_PENALTY = 10;
    public const ALL_TERM_LABEL = '全期間';
    public const TODAY_TERM = 'today';
    public const TODAY_TERM_LABEL = '本日';
    public const DEFAULT_TIER_COLOR = '#999999';
    public const RANK_PATTERNS = [
        ['1', '2', '3', '4'],
        ['1=1', '1=1', '3', '4'],
        ['1=1', '1=1', '3=3', '3=3'],
        ['1', '2=2', '2=2', '4'],
        ['1', '2', '3=3', '3=3'],
    ];
}



<?php

// クラスのインスタンス化
$statsService = new StatsService( $uTableList, $mGroupList, $mRuleList, $mDirectionList, $mGameDayList, $uUserList, $uGameHistoryList );

// 対局日時の取得
$formattedDate = $statsService->getNextGameDay();

// 年毎の総合成績の取得
$overallStatsData = $statsService->getAllStatsData();
// 本日の成績の取得
//$todayStatsData = $statsService->getTodayStatsData();
// グラフ用データの取得
//$overallChartData = $statsService->getAllChartData();
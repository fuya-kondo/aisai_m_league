
<?php

// クラスのインスタンス化
$statsService = new StatsService( $uTableList, $mGroupList, $mRuleList, $mDirectionList, $mGameDayList, $uUserList, $uGameHistoryList );

// 対局日時の取得
$formattedDate = $statsService->getNextGameDay();

// ユーザーの取得
$userList = $statsService->getUserList();
// 年毎の総合成績の取得
$overallStatsData = $statsService->getAllStatsData();
// グラフ用データの取得
$overallChartData = $statsService->getAllChartData();
// 本日の成績の取得
$todayStatsData = $statsService->getTodayStatsData();
// 対局履歴の取得
$gameHistoryDataList = $statsService->getGameHistory();
// AI分析用のデータ取得
$analysisData = $statsService->getAnalysisData();
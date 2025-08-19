<?php
const AGGREGATE_TABLE_ID = 1; // 集計対象のテーブルID

/* --- 集計対象のテーブル情報の取得 -- */
$userMap         = indexByKey($uUserList, 'u_user_id');
// 集計対象のテーブル情報を取得
$targetTableData = findFirstByKey($uTableList, 'u_table_id', AGGREGATE_TABLE_ID);
// 対象グループ情報を取得
$targetGroupData = findFirstByKey($mGroupList, 'm_group_id', $targetTableData['m_group_id']);
// 対象ルール情報を取得
$targetRuleData  = findFirstByKey($mRuleList, 'm_rule_id', $targetGroupData['m_rule_id']);
// 対象ユーザー情報を取得
$targetUserData  = [];
for ($i = 1; $i <= 4; $i++) {
    $userIdKey = 'u_user_id_' . $i;
    if (!empty($targetTableData[$userIdKey])) {
        $userId = $targetTableData[$userIdKey];
        if (isset($userMap[$userId])) {
            $targetUserData[$userId] = $userMap[$userId];
        }
    }
}

/* --- クラスのインスタンス化 -- */
$statsService = new StatsService(
    $targetUserData,        // 対象ユーザー
    $targetTableData,       // 対象テーブル
    $targetGroupData,       // 対象グループ
    $targetRuleData,        // 対象ルール
    $mDirectionList,        // m_direction
    $mGameDayList,          // m_game_day
    $mTitleList,            // m_title
    $uGameHistoryList,      // u_game_history
    $uTitleList,            // u_title
    $mTierList,             // m_tier
    $uTierHistoryList,      // u_tier_history
    $mBadgeList,            // m_badge
);


/* --- 表示用データの取得 -- */
$nextGameDay        = $statsService->getNextGameDay();      // 対局日時の取得
$nextTwoGameDays    = $statsService->getNextTwoGameDays();  // 対局日時の取得
$userList           = $statsService->getUserList();         // ユーザーの取得
$years              = $statsService->getYears();            // 対象の年を取得
$titleHolderList    = $statsService->getTitleHolder();      // タイトル保持者の取得
$rankHistoryList    = $statsService->getRankHistory();      // ランク履歴の取得
$todayStatsList     = $statsService->getTodayStatsList();   // 本日の成績の取得
$yearlyStatsList    = $statsService->getYearlyStatsList();  // 年毎の成績の取得
$yearlyChartList    = $statsService->getYearlyChartList();  // 年毎のグラフ用データの取得
$directionStats     = $statsService->getRelativeScoreByDirection(); // 各家の対戦結果の取得
$gameHistoryList    = $statsService->getGameHistoryList();  // 対局履歴の取得
$dayStatsList       = $statsService->getDayStats();         // 日ごとの対局履歴の取得
$analysisDataList   = $statsService->getAnalysisDataList(); // AI分析用のデータ取得

/* --- 以降のヘルパーは共通化（lib/helpers.php）を利用 --- */
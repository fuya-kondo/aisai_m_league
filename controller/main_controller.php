<?php
const AGGREGATE_TABLE_ID = 1; // 集計対象のテーブルID

/* --- 集計対象のテーブル情報の取得 -- */
$userMap = array_column($uUserList, null, 'u_user_id');
// 集計対象のテーブル情報を取得
$targetTableData = _findFirstItem($uTableList, 'u_table_id', AGGREGATE_TABLE_ID);
// 対象グループ情報を取得
$targetGroupData = _findFirstItem($mGroupList, 'm_group_id', $targetTableData['m_group_id']);
// 対象ルール情報を取得
$targetRuleData = _findFirstItem($mRuleList, 'm_rule_id', $targetGroupData['m_rule_id']);
// 対象ユーザー情報を取得
$targetUserData = [];
for ($i = 1; $i <= 4; $i++) {
    $userIdKey = 'u_user_id_' . $i;
    if (isset($targetTableData[$userIdKey])) {
        $userId = $targetTableData[$userIdKey];
        // $userMap から直接ユーザー情報を取得
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
);


/* --- 表示用データの取得 -- */
$formattedDate      = $statsService->getNextGameDay();      // 対局日時の取得
$userList           = $targetUserData;                      // ユーザーの取得
$years              = $statsService->getYears();            // 対象の年を取得
$titleHolderList    = $statsService->getTitleHolder();      // タイトル保持者の取得
$todayStatsList     = $statsService->getTodayStatsList();   // 本日の成績の取得
$yearlyStatsList    = $statsService->getYearlyStatsList();  // 年毎の成績の取得
$yearlyChartList    = $statsService->getYearlyChartList();  // 年毎のグラフ用データの取得
$gameHistoryList    = $statsService->getGameHistoryList();  // 対局履歴の取得
$analysisDataList   = $statsService->getAnalysisDataList(); // AI分析用のデータ取得
$scoreHiddenMode    = $mSettingList[1]['enable_flag'];

/* --- ヘルパー関数 -- */

/**
 * 配列から特定のキーと値を持つ最初の要素を見つける
 */
function _findFirstItem(array $list, string $key, $value): ?array
{
    foreach ($list as $item) {
        if (isset($item[$key]) && $item[$key] === $value) {
            return $item;
        }
    }
    return null;
}
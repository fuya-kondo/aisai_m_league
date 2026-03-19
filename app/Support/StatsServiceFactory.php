<?php
namespace App\Support;

/**
 * StatsService に必要な source set 組み立てをまとめる factory。
 * MainController から集計対象の抽出ロジックを分離する。
 */
final class StatsServiceFactory
{
    public static function create(array $masterData): \StatsService
    {
        $userMap = indexByKey($masterData['uUserList'], 'u_user_id');
        $aggregateTable = findFirstByKey($masterData['uTableList'], 'u_table_id', \App\Support\Constants\AppConstants::AGGREGATE_TABLE_ID);
        $groupData = findFirstByKey($masterData['mGroupList'], 'm_group_id', $aggregateTable['m_group_id']);
        $ruleData = findFirstByKey($masterData['mRuleList'], 'm_rule_id', $groupData['m_rule_id']);
        $tableUsers = self::collectAggregateTableUsers($aggregateTable, $userMap);

        $sourceSet = new \StatsSourceSet(
            $tableUsers,
            $aggregateTable,
            $groupData,
            $ruleData,
            $masterData['mDirectionList'],
            $masterData['mGameDayList'],
            $masterData['mTitleList'],
            $masterData['uGameHistoryList'],
            $masterData['uTitleList'],
            $masterData['mTierList'],
            $masterData['uTierHistoryList'],
            $masterData['mBadgeList']
        );

        return new \StatsService($sourceSet);
    }

    private static function collectAggregateTableUsers(array $aggregateTable, array $userMap): array
    {
        $tableUsers = [];
        for ($index = 1; $index <= \App\Support\Constants\AppConstants::PLAYER_COUNT; $index++) {
            $userIdKey = 'u_user_id_' . $index;
            if (empty($aggregateTable[$userIdKey])) {
                continue;
            }

            $userId = $aggregateTable[$userIdKey];
            if (isset($userMap[$userId])) {
                $tableUsers[$userId] = $userMap[$userId];
            }
        }

        return $tableUsers;
    }
}

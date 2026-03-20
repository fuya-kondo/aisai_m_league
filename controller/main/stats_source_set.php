<?php
/**
 * StatsService が必要とする集計前提データを束ねるソースセット。
 * ユーザー、卓、ルール、履歴などの関連配列を 1 つのオブジェクトとして受け渡す。
 */
class StatsSourceSet
{
    public array $userList;
    public array $tableData;
    public array $groupData;
    public array $ruleData;
    public array $directionList;
    public array $gameDayList;
    public array $titleDefinitions;
    public array $gameHistoryByUser;
    public array $userTitles;
    public array $tierDefinitions;
    public array $tierHistoryRecords;
    public array $badgeDefinitions;

    public function __construct(
        array $userList,
        array $tableData,
        array $groupData,
        array $ruleData,
        array $directionList,
        array $gameDayList,
        array $titleDefinitions,
        array $gameHistoryByUser,
        array $userTitles,
        array $tierDefinitions,
        array $tierHistoryRecords,
        array $badgeDefinitions
    ) {
        $this->userList = $userList;
        $this->tableData = $tableData;
        $this->groupData = $groupData;
        $this->ruleData = $ruleData;
        $this->directionList = $directionList;
        $this->gameDayList = $gameDayList;
        $this->titleDefinitions = $titleDefinitions;
        $this->gameHistoryByUser = $gameHistoryByUser;
        $this->userTitles = $userTitles;
        $this->tierDefinitions = $tierDefinitions;
        $this->tierHistoryRecords = $tierHistoryRecords;
        $this->badgeDefinitions = $badgeDefinitions;
    }
}

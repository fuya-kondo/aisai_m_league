
<?php
/**
 * 麻雀成績出力サービス
 *
 * このクラスは麻雀プレイヤーの成績を計算し、分析データを生成します。
 */
class StatsService {
    private $tableId = 1; // 任意の値
    private $tableDataList = [];
    private $tableData = [];
    private $groupId = 0;
    private $groupDataList = [];
    private $groupData = [];
    private $ruleId = 0;
    private $ruleDataList = [];
    private $ruleData = [];
    private $directionDataList = [];
    private $gameDayDataList = [];
    private $userDataList = [];
    private $tableUserList = [];
    private $gameHistoryDataList = [];

    private $years = [ 2022, 2023, 2024, 2025, 2026, "全期間"];
    private const ALL_TERM = '全期間';
    private $baseScore = 0;

    /**
     * コンストラクタ
     */
    public function __construct( $table, $group, $rule, $direction, $gameday, $user, $history ) {
        $this->tableDataList        = $table;
        $this->tableData            = array_filter($table, function($item) {
                                        return isset($item['u_table_id']) && $item['u_table_id'] === $this->tableId;
                                    });
        $this->groupId              = $this->tableData[0]['m_group_id'];
        $this->groupDataList        = $group;
        $this->groupData            = array_filter($this->groupDataList, function($item) {
                                        return isset($item['m_group_id']) && $item['m_group_id'] === $this->groupId;
                                    });
        $this->ruleId               = $this->groupData[0]['m_rule_id'];
        $this->ruleDataList         = $rule;
        $this->ruleData             = array_filter($this->ruleDataList, function($item) {
                                        return isset($item['m_rule_id']) && $item['m_rule_id'] === $this->ruleId;
                                    });
        $this->ruleDataList         = $rule;
        $this->directionDataList    = $direction;
        $this->gameDayDataList      = $gameday;
        $this->userDataList         = $user;
        for ($i = 1; $i <= 4; $i++) {
            $key = 'u_user_id_' . $i;
            if (isset($this->tableData[0][$key])) {
                $currentUserId = $this->tableData[0][$key];
                $filteredUsers = array_filter($this->userDataList, function($item) use ($currentUserId) {
                    return isset($item['u_user_id']) && $item['u_user_id'] === $currentUserId;
                });
                $this->tableUserList[$currentUserId] = array_values($filteredUsers);
            }
        }
        $this->gameHistoryDataList  = $history;
        $this->baseScore            = $this->ruleData[0]['start_score'];
    }

    /**
     * 本日の成績の取得
     * @return array 成績データ
     */
    public function getTodayStatsData() {
        $todayScore = $this->_getScore(null, true);
        $todayScore = $this->addRankings($todayScore);
        return $todayScore;
    }

    /**
     * 年毎の総合成績の取得
     * @return array 成績データ
     */
    public function getAllStatsData() {
        $result = [];
        foreach ($this->years as $year) {
            $result[$year] = $this->addRankings($this->_getScore($year));
        }
        return $result;
    }

    /**
     * グラフ用データ
     */
    public function getAllChartData() {
        $result = [];
        foreach ($this->years as $year) {
            $statsData = []; // 年ごとに初期化
            foreach ($this->userDataList as $userData) {
                foreach ($this->gameHistoryDataList[$userData['u_user_id']] as $data) {
                    // 年の条件をチェック
                    if ($year == self::ALL_TERM || date('Y', strtotime($data['play_date'])) == $year) {
                        // 該当する年のデータを追加
                        $statsData[$userData['u_user_id']][] = $data;
                    }
                }
            }
            // 年ごとの結果を格納
            $result[$year] = $statsData;
        }
        return $result;
    }

    /**
     * AI分析用
     */
    public function getAnalysisData() {
        return $this->_getScore();
    }

    /**
     * 個別成績用データ
     */
    public function getOverallHistoryData() {
        $result = [];
        foreach ($this->years as $year) {
            foreach ($this->gameHistoryDataList as $userId => $userData) {
                foreach ($userData as $data) {
                $isGet = false;
                if ( $year == self::ALL_TERM ) {
                    $isGet = true;
                } elseif ( date('Y', strtotime($data['play_date'])) == $year ) {
                    $isGet = true;
                }
                if ($isGet) $result[$userId][$year][] = $data;
                }
            }
        }
        return $result;
    }

    /**
     * 指定年度の成績を取得する
     *
     * @param int|string $year 年度
     * @return array 成績データ
     */
    private function _getScore( $year, $today = false ) {
        $result = [];
        foreach ($this->tableUserList as $userId => $userData) {
            $result[$userId] = $this->_initializeUserStats($userData[0]);
            $result[$userId] = $this->_calculateUserStats($result[$userId], $this->gameHistoryDataList[$userId], $year, $today);
        }
        return $this->formatUserStats($result);
    }

    /**
     * ユーザー統計の初期値を設定する
     *
     * @param string $name ユーザー名
     * @return array 初期化された統計データ
     */
    private function _initializeUserStats($userData) {
        return array(
            'user_id' => $userData['u_user_id'],
            'name' => $userData['last_name'],
            'play_count' => 0,
            'sum_point' => 0,
            'sum_score' => 0,
            'average_rank' => 0,
            'first_rank_count' => 0,
            'second_rank_count' => 0,
            'third_rank_count' => 0,
            'fourth_rank_count' => 0,
            'top_probability' => 0,
            'first_rank_probability' => 0,
            'second_rank_probability' => 0,
            'third_rank_probability' => 0,
            'fourth_rank_probability' => 0,
            'over_second_probability' => 0,
            'over_third_probability' => 0,
            'average_score' => 0,
        );
    }

    /**
     * ユーザーの統計を計算する
     *
     * @param array $stats 初期化された統計データ
     * @param array $userHistory ユーザーの対局履歴
     * @param int|string $year 計算対象年度
     * @return array 計算された統計データ
     */
    private function _calculateUserStats( $stats, $userHistory, $year, $today = false ) {
        $sum_rank = 0;
        foreach ($userHistory as $historyData) {
            $isGet = false;
            if ( $today && date('Y-m-d', strtotime($historyData['play_date'])) == date('Y-m-d') ) {
                $isGet = true;
            } elseif ( !$today && $year === '全期間' ) {
                $isGet = true;
            } elseif ( !$today && date('Y', strtotime($historyData['play_date'])) == $year ) {
                $isGet = true;
            }
            if ($isGet) {
                $stats['sum_point'] += $historyData['point'];
                $stats['sum_score'] += $historyData['score'];
                $mistake_count = $historyData['mistake_count'];
                for ($i = 1; $i <= $mistake_count; $i++) {
                    $stats['sum_point'] -= 20;
                }
                $stats['play_count']++;
                $rank = substr($historyData['rank'], 0, 1);
                $sum_rank += $rank;
                $stats[$this->getRankCountKey($rank)]++;
            }
        }

        if ($stats['play_count'] > 0) {
            $stats['average_rank'] = $sum_rank / $stats['play_count'];
            $stats['top_probability'] = $stats['first_rank_count'] / $stats['play_count'] * 100;
            $stats['first_rank_probability'] = $stats['first_rank_count'] / $stats['play_count'] * 100;
            $stats['second_rank_probability'] = $stats['second_rank_count'] / $stats['play_count'] * 100;
            $stats['third_rank_probability'] = $stats['third_rank_count'] / $stats['play_count'] * 100;
            $stats['fourth_rank_probability'] = $stats['fourth_rank_count'] / $stats['play_count'] * 100;
            $stats['over_second_probability'] = ($stats['first_rank_count'] + $stats['second_rank_count']) / $stats['play_count'] * 100;
            $stats['over_third_probability'] = 100 - ($stats['fourth_rank_count'] / $stats['play_count'] * 100);
            $stats['average_score'] = $stats['sum_score'] / $stats['play_count'];
        }

        return $stats;
    }

    /**
     * ユーザーの統計を計算する
     *
     * @return array 計算された統計データ
     */
    private function _getGameHistory() {
        $result = [];
        foreach ($this->gameHistoryDataList as $gameHistoryData) {
        }
        return $result;
    }

    /**
     * 順位に対応するカウントキーを取得する
     *
     * @param int $rank 順位
     * @return string カウントキー
     */
    private function getRankCountKey($rank) {
        $rankMap = array(1 => 'first', 2 => 'second', 3 => 'third', 4 => 'fourth');
        return $rankMap[$rank] . '_rank_count';
    }

    /**
     * ユーザー統計データをフォーマットする
     *
     * @param array $statsData 統計データ
     * @return array フォーマットされた統計データ
     */
    private function formatUserStats($statsData) {
        foreach ($statsData as &$userData) {
            $userData['sum_score'] = number_format(round(($userData['sum_score'] - ($userData['play_count'] * $this->baseScore)) / 1000, 2), 1);
            $userData['average_rank'] = number_format(round($userData['average_rank'], 2), 2);
            $userData['top_probability'] = number_format(round($userData['top_probability'], 2), 2).'%';
            $userData['first_rank_probability'] = number_format(round($userData['first_rank_probability'], 2), 2).'%';
            $userData['second_rank_probability'] = number_format(round($userData['second_rank_probability'], 2), 2).'%';
            $userData['third_rank_probability'] = number_format(round($userData['third_rank_probability'], 2), 2).'%';
            $userData['fourth_rank_probability'] = number_format(round($userData['fourth_rank_probability'], 2), 2).'%';
            $userData['over_second_probability'] = number_format(round($userData['over_second_probability'], 2), 2).'%';
            $userData['over_third_probability'] = number_format(round($userData['over_third_probability'], 2), 2).'%';
            $userData['average_score'] = number_format($userData['average_score'], 0);
        }
        unset($userData);
        return $statsData;
    }

    /**
     * 統計データにランキングを追加する
     *
     * @param array $statsData 統計データ
     * @return array $statsData ランキング付きの統計データ
     */
    private function addRankings($statsData) {
        $sumPointsForSort = [];
        foreach ($statsData as $key => $userData) {
            $sumPointsForSort[$key] = floatval(str_replace(['%', ','], '', $userData['sum_point']));
        }
        uasort($statsData, function ($a, $b) {
            $valA = floatval(str_replace(['%', ','], '', $a['sum_point']));
            $valB = floatval(str_replace(['%', ','], '', $b['sum_point']));
            if ($valA == $valB) {
                return 0;
            }
            return ($valA > $valB) ? -1 : 1; // 降順
        });
        $rank = 1;
        foreach ($statsData as &$userData) {
            $userData['ranking'] = $rank++;
        }
        return $statsData;
    }

    /**
     * 次の対局日時を取得します。
     *
     * 現在の日時以降で最も近い対局日時を検索し、フォーマットされた文字列で返します。
     * 対局予定がない場合は「予定なし」を返します。
     *
     * @return string $nextGameDay フォーマットされた次の対局日時（例: '6/15(日)'）、または「予定なし」
     */
    public function getNextGameDay(): string {
        $todayStart = (new DateTimeImmutable())->setTime(0, 0, 0);
        $futureGameDateObjects = [];

        // 未来の対局日時のみを抽出
        foreach ($this->gameDayDataList as $gameDayData) {
            if (!isset($gameDayData['game_day']) || !is_string($gameDayData['game_day'])) {
                error_log("Invalid game_day data structure: " . json_encode($gameDayData));
                continue;
            }
            $dateString = $gameDayData['game_day'];
            try {
                $gameDateTime = new DateTimeImmutable($dateString);

                if ($gameDateTime->setTime(0, 0, 0) >= $todayStart) {
                    $futureGameDateObjects[] = $gameDateTime;
                }
            } catch (Exception $e) {
                error_log("Failed to parse game_day date string: '{$dateString}' - " . $e->getMessage());
            }
        }

        // 未来の対局日時が一件もない場合
        if (empty($futureGameDateObjects)) {
            return '予定なし';
        }

        // 最も近い対局日時を特定
        usort($futureGameDateObjects, function (DateTimeImmutable $a, DateTimeImmutable $b) {
            return $a <=> $b;
        });
        $closestDateTime = $futureGameDateObjects[0];

        // 日時と曜日のフォーマット
        $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
        $weekday = $weekdays[(int)$closestDateTime->format('w')]; // wは0(日)から6(土)
        $nextGameDay = $closestDateTime->format('n/j') . "(" . $weekday . ")";

        return $nextGameDay;
    }

    /*
    * 対局履歴の取得
    */
    public function getGameHistory() {
        $result = [];
        foreach ($this->groupAUserIds as $userId) {
            foreach ($this->gameHistoryDataForTable[$userId] as $historyData) {
                if (!empty($historyData['game'])) {
                    $playDate = new DateTime($historyData['play_date']);
                    $dateKey = $playDate->format('Y-m-d');
                    $result[$dateKey][$historyData['game']][$userId] = $historyData;
                }
            }
        }
        foreach ($result as $dateKey => &$games) {
            uksort($games, function ($a, $b) {
                $numA = intval($a);
                $numB = intval($b);
                if ($numA == $numB) return 0;
                return ($numA > $numB) ? -1 : 1;
            });
        }
        unset($games);
        return $result;
    }
}
?>

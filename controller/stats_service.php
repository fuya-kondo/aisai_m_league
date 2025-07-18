<?php
/**
 * 麻雀成績出力サービス
 *
 * このクラスは麻雀プレイヤーの成績を計算し、分析データを生成します。
 */
class StatsService {
    /* --- プロパティの定義 --- */
    private $userList               = [];
    private $tableData              = [];
    private $groupData              = [];
    private $ruleData               = [];
    private $mTitle                 = [];
    private $uTitle                 = [];
    private $mDirectionDataList     = [];
    private $mGameDayDataList       = [];
    private $uGameHistoryDataList   = [];
    private $years                  = [];
    private $baseScore              = null;
    private const START_YEAR        = 2022;
    private const ALL_TERM          = '全期間';

    /**
     * コンストラクタ
     */
    public function __construct( $user, $table, $group, $rule, $mDirection, $mGameDay, $mTitle, $uGameHistory, $uTitle ) {
        $this->userList             = $user;
        $this->tableData            = $table;
        $this->groupData            = $group;
        $this->ruleData             = $rule;
        $this->mTitle               = $mTitle;
        $this->uTitle               = $uTitle;
        $this->mDirectionDataList   = $mDirection;
        $this->mGameDayDataList     = $mGameDay;
        $this->uGameHistoryDataList = $uGameHistory;
        $this->baseScore            = $rule['start_score'];
        $this->_setYears();
    }

    /* -------- GETメソッド -------- */

    /**
     * 集計対象の年を取得
     *
     * @return array 集計対象の年リスト
     */
    public function getYears(): array
    {
        return $this->years;
    }

    /**
     * タイトル保持者の取得
     *
     * @return array タイトル保持者リスト
     */
    public function getTitleHolder(): array
    {
        $mTitleMap = [];
        foreach ($this->mTitle as $title) {
            $mTitleMap[$title['m_title_id']] = $title['name'];
        }

        $uUserMap = [];
        foreach ($this->userList as $user) {
            $uUserMap[$user['u_user_id']] = $user['last_name'].$user['first_name'];
        }

        $groupedTitles = [];
        foreach ($this->uTitle as $uTitleItem) {
            $year = $uTitleItem['year'];
            $mTitleId = $uTitleItem['m_title_id'];
            $uUserId = $uTitleItem['u_user_id'];
            $titleName = isset($mTitleMap[$mTitleId]) ? $mTitleMap[$mTitleId] : '不明なタイトル';
            $name = isset($uUserMap[$uUserId]) ? $uUserMap[$uUserId] : '不明なユーザー';

            // 年ごとに配列を初期化
            if (!isset($groupedTitles[$year])) {
                $groupedTitles[$year] = [];
            }

            $groupedTitles[$year][] = [
                'u_title_id' => $uTitleItem['u_title_id'],
                'title_name' => $titleName,
                'u_user_id' => $name,
                'value' => $uTitleItem['value']
            ];
        }
        krsort($groupedTitles);

        return $groupedTitles;
    }

    /**
     * 本日の成績の取得
     *
     * @return array 本日の成績リスト
     */
    public function getTodayStatsList(): array
    {
        return $this->_addRankings($this->_getScore(self::ALL_TERM, true));
    }

    /**
     * 年毎の成績の取得
     *
     * @return array 年毎の成績リスト
     */
    public function getYearlyStatsList(): array
    {
        $result = [];
        foreach ($this->years as $year) {
            $result[$year] = $this->_addRankings($this->_getScore($year));
        }
        return $result;
    }

    /**
     * AI分析用データの取得
     *
     * @return array AI分析用データリスト
     */
    public function getAnalysisDataList(): array
    {
        return $this->_getScore(self::ALL_TERM);
    }

    /**
     * 年毎のグラフ用データの取得
     *
     * @return array 年毎のグラフ用データリスト
     */
    public function getYearlyChartList(): array
    {
        $result = [];
        foreach ($this->years as $year) {
            $statsData = []; // 年ごとに初期化
            foreach ($this->userList as $userId => $userData) {
                foreach ($this->uGameHistoryDataList[$userId] as $data) {
                    // 年の条件をチェック
                    if ($year == self::ALL_TERM || date('Y', strtotime($data['play_date'])) == $year) {
                        // 該当する年のデータを追加
                        $statsData[$userId][] = $data;
                    }
                }
            }
            // 年ごとの結果を格納
            $result[$year] = $statsData;
        }
        return $result;
    }

    /**
     * 次の対局日時を取得します。
     *
     * @return string 次の対局日時
     */
    public function getNextGameDay(): string
    {
        $todayStart = (new DateTimeImmutable())->setTime(0, 0, 0);
        $futureGameDateObjects = [];

        // 未来の対局日時のみを抽出
        foreach ($this->mGameDayDataList as $gameDayData) {
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

    /**
     * 対局履歴の取得
     *
     * @return array 対局履歴リスト
     */
    public function getGameHistoryList(): array
    {
        $result = [];
        foreach ($this->userList as $userId => $userData) {
            foreach ($this->uGameHistoryDataList[$userId] as $uGameHistoryData) {
                if (!empty($uGameHistoryData['game'])) {
                    $playDate = new DateTime($uGameHistoryData['play_date']);
                    $dateKey = $playDate->format('Y-m-d');
                    $result[$dateKey][$uGameHistoryData['game']][$userId] = $uGameHistoryData;
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

    /**
     * 指定年度の成績を取得する
     *
     * @param string $year 指定年度
     * @param boolean $today 本日のデータを取得するかどうか
     * @return array 指定年度の成績データ
     */
    private function _getScore( $year, $today = false ): array
    {
        $result = [];
        foreach ( $this->userList as $userId => $userData ) {
            $result[$userId] = $this->_initializeUserStats($userData);
            $result[$userId] = $this->_calculateUserStats($result[$userId], $this->uGameHistoryDataList[$userId], $year, $today);
        }
        return $this->_formatUserStats($result);
    }

    /**
     * ユーザー統計の初期値を設定する
     *
     * @param string $userData ユーザーデータ
     * @return array 初期化された統計データ
     */
    private function _initializeUserStats($userData): array
    {
        return [
            'u_user_id'                 => $userData['u_user_id'],
            'name'                      => $userData['last_name'].$userData['first_name'],
            'play_count'                => 0, // 対局数
            'sum_point'                 => 0, // 合計ポイント
            'sum_score'                 => 0, // 合計スコア
            'average_rank'              => 0, // 平均順位
            'first_rank_count'          => 0, // 1位数
            'second_rank_count'         => 0, // 2位数
            'third_rank_count'          => 0, // 3位数
            'fourth_rank_count'         => 0, // 4位数
            'first_rank_probability'    => 0, // 1位率
            'second_rank_probability'   => 0, // 2位率
            'third_rank_probability'    => 0, // 3位率
            'fourth_rank_probability'   => 0, // 4位率
            'over_second_probability'   => 0, // 連対率
            'over_third_probability'    => 0, // 4着回避率
            'average_score'             => 0, // 平均スコア
            'direction_1_count'         => 0, // 東家の数
            'direction_2_count'         => 0, // 南家の数
            'direction_3_count'         => 0, // 西家の数
            'direction_4_count'         => 0, // 北家の数
            'direction_1_sum_point'     => 0, // 東家の合計ポイント
            'direction_2_sum_point'     => 0, // 南家の合計ポイント
            'direction_3_sum_point'     => 0, // 西家の合計ポイント
            'direction_4_sum_point'     => 0, // 北家の合計ポイント
            'direction_1_average_point' => 0, // 東家での平均ポイント
            'direction_2_average_point' => 0, // 南家での平均ポイント
            'direction_3_average_point' => 0, // 西家での平均ポイント
            'direction_4_average_point' => 0, // 北家での平均ポイント
            'mistake_count'             => 0, // ミス数
        ];
    }

    /**
     * ユーザーの統計を計算する
     *
     * @param array $stats 初期化された統計データ
     * @param array $uGameHistoryDataList ユーザーの対局履歴
     * @param string $year 計算対象年度
     * @param boolean $today 本日のデータを取得するかどうか
     * @return array 計算された統計データ
     */
    private function _calculateUserStats( $stats, $uGameHistoryDataList, $year, $today = false ): array
    {
        $sum_rank = 0;
        foreach ($uGameHistoryDataList as $uGameHistoryData) {
            $isGet = false;
            if ( $today && date('Y-m-d', strtotime($uGameHistoryData['play_date'])) == date('Y-m-d') ) {
                $isGet = true;
            } elseif ( !$today && $year === '全期間' ) {
                $isGet = true;
            } elseif ( !$today && date('Y', strtotime($uGameHistoryData['play_date'])) == $year ) {
                $isGet = true;
            }
            if ($isGet) {
                $stats['play_count']++;
                $stats['sum_point'] += $uGameHistoryData['point'];
                $stats['sum_score'] += $uGameHistoryData['score'];
                // チョンボの統計
                $stats['mistake_count'] += $uGameHistoryData['mistake_count'];
                $mistake_count = $uGameHistoryData['mistake_count'];
                for ($i = 1; $i <= $mistake_count; $i++) {
                    $stats['sum_point'] -= 20;
                }
                // 順位の統計
                $rank = substr($uGameHistoryData['rank'], 0, 1); // 同率の場合は上位でカウントする
                $sum_rank += $rank;
                $stats[$this->_getRankCountKey($rank)]++;
                // 家の統計
                if ( !empty($uGameHistoryData['m_direction_id']) ) {
                    $stats['direction_'.$uGameHistoryData['m_direction_id'].'_count']++;
                    $stats['direction_'.$uGameHistoryData['m_direction_id'].'_sum_point'] += $uGameHistoryData['point'];
                }
            }
        }

        if ( isset($stats['play_count']) && $stats['play_count'] > 0 ) {
            $stats['average_rank']              = $sum_rank / $stats['play_count'];
            $stats['first_rank_probability']    = !empty($stats['first_rank_count'])  ? $stats['first_rank_count']  / $stats['play_count'] * 100 : 0;
            $stats['second_rank_probability']   = !empty($stats['second_rank_count']) ? $stats['second_rank_count'] / $stats['play_count'] * 100 : 0;
            $stats['third_rank_probability']    = !empty($stats['third_rank_count'])  ? $stats['third_rank_count']  / $stats['play_count'] * 100 : 0;
            $stats['fourth_rank_probability']   = !empty($stats['fourth_rank_count']) ? $stats['fourth_rank_count'] / $stats['play_count'] * 100 : 0;
            $stats['over_second_probability']   = !empty($stats['first_rank_count'])  ? ($stats['first_rank_count'] + $stats['second_rank_count']) / $stats['play_count'] * 100 : 0;
            $stats['over_third_probability']    = !empty($stats['fourth_rank_count']) ? 100 - ($stats['fourth_rank_count'] / $stats['play_count'] * 100) : 0;
            $stats['direction_1_average_point']    = !empty($stats['direction_1_sum_point']) ? $stats['direction_1_sum_point'] / $stats['direction_1_count'] : 0;
            $stats['direction_2_average_point']    = !empty($stats['direction_2_sum_point']) ? $stats['direction_2_sum_point'] / $stats['direction_2_count'] : 0;
            $stats['direction_3_average_point']    = !empty($stats['direction_3_sum_point']) ? $stats['direction_3_sum_point'] / $stats['direction_3_count'] : 0;
            $stats['direction_4_average_point']    = !empty($stats['direction_4_sum_point']) ? $stats['direction_4_sum_point'] / $stats['direction_4_count'] : 0;
        }

        return $stats;
    }

    /**
     * ユーザー統計データをフォーマットする
     *
     * @param array $statsData 統計データ
     * @return array フォーマットされた統計データ
     */
    private function _formatUserStats( $statsData ): array
    {
        foreach ($statsData as &$userData) {
            if ( isset($userData['play_count']) && $userData['play_count'] > 0 ) {
                $userData['sum_score']                  = number_format(($userData['sum_score'] - ($userData['play_count'] * $this->baseScore)) / 1000, 1);
                $userData['average_rank']               = number_format($userData['average_rank'], 2);
                $userData['first_rank_probability']     = number_format($userData['first_rank_probability'], 2).'%';
                $userData['second_rank_probability']    = number_format($userData['second_rank_probability'], 2).'%';
                $userData['third_rank_probability']     = number_format($userData['third_rank_probability'], 2).'%';
                $userData['fourth_rank_probability']    = number_format($userData['fourth_rank_probability'], 2).'%';
                $userData['over_second_probability']    = number_format($userData['over_second_probability'], 2).'%';
                $userData['over_third_probability']     = number_format($userData['over_third_probability'], 2).'%';
                $userData['average_score']              = number_format($userData['average_score'], 0);
                $userData['direction_1_average_point']  = number_format($userData['direction_1_average_point'], 2);
                $userData['direction_2_average_point']  = number_format($userData['direction_2_average_point'], 2);
                $userData['direction_3_average_point']  = number_format($userData['direction_3_average_point'], 2);
                $userData['direction_4_average_point']  = number_format($userData['direction_4_average_point'], 2);
            }
        }
        unset($userData);
        return $statsData;
    }

    /**
     * 順位に対応するカウントキーを取得する
     *
     * @param int $rank 順位
     * @return string カウントキー
     */
    private function _getRankCountKey( $rank ): string
    {
        $rankMap = array(1 => 'first', 2 => 'second', 3 => 'third', 4 => 'fourth');
        return $rankMap[$rank] . '_rank_count';
    }

    /**
     * 統計データにランキングを追加する
     *
     * @param array $statsData 統計データ
     * @return array $statsData ランキング付きの統計データ
     */
    private function _addRankings( $statsData ): array
    {
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
     * START_YEARから現在の年までの期間と、全期間を$yearsプロパティに設定
     */
    private function _setYears(): void
    {
        $currentYear = (int)date('Y');
        for ($year = self::START_YEAR; $year <= $currentYear; $year++) {
            $this->years[] = $year;
        }
        $this->years[] = self::ALL_TERM;
    }
}
?>

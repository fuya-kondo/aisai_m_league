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
     * 各家の対戦結果（上家とのスコア・ポイント差を集計）
     *
     * @return array
     */
    public function getRelativeScoreByDirection(): array
    {
        foreach ($this->uGameHistoryDataList as $userId => $gameHistoryDataList) {
            foreach ($gameHistoryDataList as $gameHistoryData) {
                if ($gameHistoryData['m_direction_id'] != 0) {
                    $playDate = new DateTime($gameHistoryData['play_date']);
                    $playDateYMD = $playDate->format('Y-m-d');
                    $uDirectionGameHistoryDataList[$playDateYMD][$gameHistoryData['game']][$gameHistoryData['m_direction_id']] = $gameHistoryData;
                }
            }
        }

        $directionStats = [
            'upper' => [],
            'lower' => []
        ];

        foreach ($uDirectionGameHistoryDataList as $playDate => $directionGameHistoryDataList) {
            foreach ($directionGameHistoryDataList as $game => $directionGameHistoryData) {
                if (count($directionGameHistoryData) === 4) {
                    foreach ($directionGameHistoryData as $directionId => $gameHistoryData) {
                        $currentUserId = $gameHistoryData['u_user_id'];

                        // --- 上家処理 ---
                        $upperDirectionId = $this->_getUpperDirection($directionId);
                        $upperUserId = $directionGameHistoryData[$upperDirectionId]['u_user_id'];

                        if (!isset($directionStats['upper'][$currentUserId][$upperUserId])) {
                            $directionStats['upper'][$currentUserId][$upperUserId] = ['sub_score' => 0, 'sub_point' => 0];
                        }

                        $directionStats['upper'][$currentUserId][$upperUserId]['sub_score'] += $gameHistoryData['score'] - $directionGameHistoryData[$upperDirectionId]['score'];
                        $directionStats['upper'][$currentUserId][$upperUserId]['sub_point'] = round(
                            $directionStats['upper'][$currentUserId][$upperUserId]['sub_point'] + ($gameHistoryData['point'] - $directionGameHistoryData[$upperDirectionId]['point']),
                            1
                        );

                        // --- 下家処理 ---
                        $lowerDirectionId = $this->_getLowerDirection($directionId);
                        $lowerUserId = $directionGameHistoryData[$lowerDirectionId]['u_user_id'];

                        if (!isset($directionStats['lower'][$currentUserId][$lowerUserId])) {
                            $directionStats['lower'][$currentUserId][$lowerUserId] = ['sub_score' => 0, 'sub_point' => 0];
                        }

                        $directionStats['lower'][$currentUserId][$lowerUserId]['sub_score'] += $gameHistoryData['score'] - $directionGameHistoryData[$lowerDirectionId]['score'];
                        $directionStats['lower'][$currentUserId][$lowerUserId]['sub_point'] = round(
                            $directionStats['lower'][$currentUserId][$lowerUserId]['sub_point'] + ($gameHistoryData['point'] - $directionGameHistoryData[$lowerDirectionId]['point']),
                            1
                        );
                    }
                }
            }
        }

        return $directionStats;
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
     * 日ごとの対局履歴の取得
     *
     * @return array 対局履歴リスト
     */
    public function getDayStats(): array
    {
        $result = [];
        foreach ($this->userList as $userId => $userData) {
            foreach ($this->uGameHistoryDataList[$userId] as $uGameHistoryData) {
                if (!empty($uGameHistoryData['game'])) {
                    $playDate = new DateTime($uGameHistoryData['play_date']);
                    $dateKey = $playDate->format('Y-m-d');
                    if ( !isset($result[$dateKey][$userId]) ) {
                        $result[$dateKey][$userId] = 0;
                    }
                    $result[$dateKey][$userId] += $uGameHistoryData['point'];
                }
            }
        }
        return $result;
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
        $userGameHistoryData = [];
        foreach ( $this->userList as $userId => $userData ) {
            $result[$userId] = $this->_initializeUserStats($userData);
            $userGameHistoryData = $this->_getGameHistory($this->uGameHistoryDataList[$userId], $year, $today);
            $result[$userId] = $this->_calculateUserStats($result[$userId], $userGameHistoryData);
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
            'rank_count'                => [ 1 => 0, 2 => 0, 3 => 0, 4 => 0 ], // 順位カウント
            'rank_probability'          => [ 1 => 0, 2 => 0, 3 => 0, 4 => 0 ], // 順位率
            'average_rank'              => 0, // 平均順位
            'sum_base_score'            => 0, // 素点
            'average_score'             => 0, // 平均点
            'sum_point'                 => 0, // 合計ポイント
            'average_point'             => 0, // 平均ポイント
            'hight_score'               => 0, // 最高点
            'play_count_direction'      => [ 1 => 0, 2 => 0, 3 => 0, 4 => 0 ], // 対局数(各家)
            'rank_count_direction'      => [ 1 => [ 1 => 0, 2 => 0, 3 => 0, 4 => 0 ], 2 => [ 1 => 0, 2 => 0, 3 => 0, 4 => 0 ], 3 => [ 1 => 0, 2 => 0, 3 => 0, 4 => 0 ], 4 => [ 1 => 0, 2 => 0, 3 => 0, 4 => 0 ] ], // 順位カウント(各家)
            'rank_probability_direction'=> [ 1 => [ 1 => 0, 2 => 0, 3 => 0, 4 => 0 ], 2 => [ 1 => 0, 2 => 0, 3 => 0, 4 => 0 ], 3 => [ 1 => 0, 2 => 0, 3 => 0, 4 => 0 ], 4 => [ 1 => 0, 2 => 0, 3 => 0, 4 => 0 ] ], // 順位率(各家)
            'average_rank_direction'    => [ 1 => 0, 2 => 0, 3 => 0, 4 => 0 ], // 平均順位(各家)
            'sum_base_score_direction'  => [ 1 => 0, 2 => 0, 3 => 0, 4 => 0 ], // 素点(各家)
            'average_score_direction'   => [ 1 => 0, 2 => 0, 3 => 0, 4 => 0 ], // 平均点(各家)
            'sum_point_direction'       => [ 1 => 0, 2 => 0, 3 => 0, 4 => 0 ], // 合計ポイント(各家)
            'average_point_direction'   => [ 1 => 0, 2 => 0, 3 => 0, 4 => 0 ], // 平均ポイント(各家)
            'over_second_probability'   => 0, // 連対率
            'over_third_probability'    => 0, // 4着回避率
            'mistake_count'             => 0, // チョンボ数
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
    private function _getGameHistory( $uGameHistoryDataList, $year, $today = false ): array
    {
        $result = [];
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
                $result[] = $uGameHistoryData;
            }
        }
        return $result;
    }

    /**
     * ユーザーの統計を計算する
     *
     * @param array $uGameHistoryDataList ユーザーの対局履歴
     * @return array 計算された統計データ
     */
    private function _calculateUserStats( $stats, $uGameHistoryDataList ): array
    {
        $sum_rank = 0; // 平均順位の算出に使用
        $sum_score = 0; // 平均点の算出に使用
        $sum_rank_direction = [ 1 => 0, 2 => 0, 3 => 0, 4 => 0 ]; // 各家の平均順位の算出に使用
        $sum_score_direction = [ 1 => 0, 2 => 0, 3 => 0, 4 => 0 ]; // 各家の平均点の算出に使用
        foreach ($uGameHistoryDataList as $uGameHistoryData) {
            $stats['play_count']        ++;
            $stats['sum_point']         += $uGameHistoryData['point'];
            $sum_score                  += $uGameHistoryData['score'];
            $stats['sum_base_score']    += ($uGameHistoryData['score'] - $this->baseScore) / 1000;
            $stats['mistake_count']     += $uGameHistoryData['mistake_count'];
            $stats['hight_score']        = max($uGameHistoryData['score'], $stats['hight_score']);
            // 順位の統計
            $rank       = substr($uGameHistoryData['rank'], 0, 1); // 同率の場合は上位でカウントする
            $sum_rank   += $rank;
            $stats['rank_count'][$rank] ++;
            // 各家の統計
            if ( !empty($uGameHistoryData['m_direction_id']) ) {
                $stats['play_count_direction'][$uGameHistoryData['m_direction_id']]         ++; // 対局数(各家)
                $stats['rank_count_direction'][$uGameHistoryData['m_direction_id']][$rank]  ++; // 順位(各家)
                $sum_rank_direction[$uGameHistoryData['m_direction_id']]                    += $rank; // 順位(各家)
                $stats['sum_base_score_direction'][$uGameHistoryData['m_direction_id']]     += ($uGameHistoryData['score'] - $this->baseScore) / 1000; // 素点(各家)
                $sum_score_direction[$uGameHistoryData['m_direction_id']]                   += $uGameHistoryData['score']; // 平均点(各家)
                $stats['sum_point_direction'][$uGameHistoryData['m_direction_id']]          += $uGameHistoryData['point']; // 合計ポイント(各家)
            }
        }
        if ( isset($stats['play_count']) && $stats['play_count'] > 0 ) {
             // 各順位率
            foreach ($stats['rank_count'] as $rank => $count) {
                $stats['rank_probability'][$rank] = !empty($count) ? $count / $stats['play_count'] * 100 : 0;
            }
            $stats['average_rank']                  = !empty($sum_rank)                                         ? $sum_rank / $stats['play_count'] : 0; // 平均順位
            $stats['average_score']                 = !empty($sum_score)                                        ? $sum_score / $stats['play_count'] : 0; // 平均点
            $stats['average_point']                 = !empty($stats['sum_point'])                               ? $stats['sum_point'] / $stats['play_count'] : 0; // 平均ポイント
            $stats['over_second_probability']       = !empty($stats['rank_count'][1] + $stats['rank_count'][2]) ? ($stats['rank_count'][1] + $stats['rank_count'][2]) / $stats['play_count'] * 100 : 0; // 連対率
            $stats['over_third_probability']        = !empty($stats['rank_count'][4])                           ? 100 - ($stats['rank_count'][4] / $stats['play_count'] * 100) : 0; // 4着回避率
        }
        // 各家の統計がある場合
        if ( isset($stats['play_count_direction']) && $stats['play_count_direction'][1] > 0 ) {
            // 平均順位(各家)
            foreach ($sum_rank_direction as $directionId => $data) {
                $stats['average_rank_direction'][$directionId] = !empty($data) ? $data / $stats['play_count_direction'][$directionId] : 0;
            }
            // 順位率(各家)
            foreach ($stats['rank_count_direction'] as $directionId => $data) {
                foreach ($data as $rank => $value) {
                    $stats['rank_probability_direction'][$directionId][$rank] = !empty($value) ? $value / $stats['play_count_direction'][$directionId] * 100 : 0;
                }
            }
            // 平均点(各家)
            foreach ($sum_score_direction as $directionId => $data) {
                $stats['average_score_direction'][$directionId] = !empty($data) ? $data / $stats['play_count_direction'][$directionId] : 0;
            }
            // 平均ポイント(各家)
            foreach ($stats['sum_point_direction'] as $directionId => $data) {
                $stats['average_point_direction'][$directionId] = !empty($data) ? $data / $stats['play_count_direction'][$directionId] : 0;
            }
        }

        // チョンボの精算
        for ($i = 1; $i <= $stats['mistake_count']; $i++) {
            $stats['sum_point'] -= 20;
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
                $userData['sum_point']                  = number_format($userData['sum_point'], 1);
                $userData['sum_point']                  = floatval(str_replace(',', '', $userData['sum_point']));
                foreach ($userData['rank_probability'] as $rank => $probability) {
                    $userData['rank_probability'][$rank]= number_format($probability, 2).'%';
                }
                $userData['average_point']              = number_format($userData['average_point'], 1);
                $userData['sum_base_score']             = number_format($userData['sum_base_score'], 1);
                $userData['average_score']              = str_replace(',', '', number_format($userData['average_score'], 0));
                $userData['average_rank']               = number_format($userData['average_rank'], 2);
                $userData['over_second_probability']    = number_format($userData['over_second_probability'], 2).'%';
                $userData['over_third_probability']     = number_format($userData['over_third_probability'], 2).'%';
            }
            // 各家の統計がある場合
            if ( isset($userData['play_count']) && $userData['play_count'] > 0 ) {
                // 平均順位(各家)
                foreach ($userData['average_rank_direction'] as $directionId => $value) {
                    $userData['average_rank_direction'][$directionId] = number_format($value, 2);
                }
                // 順位率(各家)
                foreach ($userData['rank_probability_direction'] as $directionId => $data) {
                    foreach ($data as $rank => $value) {
                        $userData['rank_probability_direction'][$directionId][$rank] = number_format($value, 2).'%';
                    }
                }
                // 平均点(各家)
                foreach ($userData['average_score_direction'] as $directionId => $value) {
                    $userData['average_score_direction'][$directionId] = number_format($value, 0);
                }
                // 平均順位(各家)
                foreach ($userData['average_point_direction'] as $directionId => $value) {
                    $userData['average_point_direction'][$directionId] = number_format($value, 1);
                }
            }
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

    /**
     * 上家の方向IDを取得（1のときは4）
     */
    private function _getUpperDirection(int $directionId): int
    {
        return $directionId === 1 ? 4 : $directionId - 1;
    }

    private function _getLowerDirection(int $directionId): int
    {
        return $directionId === 4 ? 1 : $directionId + 1;
    }
}
?>

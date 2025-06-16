<?php
/**
 * 麻雀成績出力サービス
 *
 * このクラスは麻雀プレイヤーの成績を計算し、分析データを生成します。
 */
class MahjongStatsService {
    private $userDataList;
    private $historyDataList;
    private $groupAUserIds;
    private $yearsToCalculate;
    private const BASE_SCORE = 25000;
    private const ALL_TERM = '全期間';

    /**
     * コンストラクタ
     *
     * @param array $groupAUserIds グループAのユーザーID
     * @param array $yearsToCalculate 計算対象年度
     */
    public function __construct($userDataList, $historyDataList, $groupAUserIds, $yearsToCalculate) {
        $this->userDataList = $userDataList;
        $this->historyDataList = $historyDataList;
        $this->groupAUserIds = $groupAUserIds;
        $this->yearsToCalculate = $yearsToCalculate;
    }

    /**
     * 本日の成績の取得
     * @return array 成績データ
     */
    public function getTodayStatsData() {
        $todayScore = $this->getScore(null, true);
        $todayScore = $this->addRankings($todayScore);
        return $todayScore;
    }

    /**
     * 総合成績の取得
     * @return array 成績データ
     */
    public function getOverallStatsData() {
        $result = array();
        foreach ($this->yearsToCalculate as $year) {
            $result[$year] = $this->addRankings($this->getScore($year));
        }
        return $result;
    }

    /**
     * グラフ用データ
     */
    public function getOverallChartData() {
        $result = array();
        foreach ($this->yearsToCalculate as $year) {
            $statsData = array(); // 年ごとに初期化
            foreach ($this->userDataList as $userId => $userData) {
                foreach ($this->historyDataList[$userId] as $data) {
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
     * AI分析用
     */
    public function getAnalysisData() {
        return $this->getScore();
    }

    /**
     * 個別成績用データ
     */
    public function getOverallHistoryData() {
        $result = array();
        foreach ($this->yearsToCalculate as $year) {
            foreach ($this->historyDataList as $userId => $userData) {
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
     * @param int|string $year 年度（
     * @return array 成績データ
     */
    private function getScore($year = self::ALL_TERM, $today = false) {
        $year = $year ?? self::ALL_TERM;
        $result = array();
        foreach ($this->userDataList as $userId => $userData) {
            if (in_array($userId, $this->groupAUserIds)) {
                $result[$userId] = $this->initializeUserStats($userData);
                $result[$userId] = $this->calculateUserStats($result[$userId], $this->historyDataList[$userData['u_mahjong_user_id']], $year, $today);
            }
        }
        return $this->formatUserStats($result);
    }

    /**
     * ユーザー統計の初期値を設定する
     *
     * @param string $name ユーザー名
     * @return array 初期化された統計データ
     */
    private function initializeUserStats($userData) {
        return array(
            'user_id' => $userData['u_mahjong_user_id'],
            'name' => $userData['name'],
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
    private function calculateUserStats($stats, $userHistory, $year, $today = false) {
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
            $userData['sum_score'] = number_format(round(($userData['sum_score'] - ($userData['play_count'] * self::BASE_SCORE)) / 1000, 2), 1);
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
        $sumPoints = array_column($statsData, 'sum_point');
        array_multisort($sumPoints, SORT_DESC, $statsData);
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
     * @param array $dateList 対局日時を表す文字列の配列（例: ['2025-06-15 10:00:00', '2025-06-20 14:30:00']）
     * @return string $nextGameDay フォーマットされた次の対局日時（例: '6/15(日)'）、または「予定なし」
     */
    public function getNextGameDay(array $dateList): string {
        // 未来の対局日時のみを抽出
        // 今日の日付（0時0分0秒）以降の日付を対象とします。
        $currentDate = new DateTime();
        $currentDate->setTime(0, 0, 0);
        $futureGameDates = array_filter($dateList, function (string $dateString) use ($currentDate): bool {
            try {
                $gameDateTime = new DateTime($dateString);
                // 厳密な比較のために、対局日時も時間を00:00:00に設定してから比較します。
                // これにより、本日中の対局も「次の対局」として扱われます。
                $gameDateTime->setTime(0, 0, 0);
                return $gameDateTime >= $currentDate;
            } catch (Exception $e) {
                // 日付文字列のパースに失敗した場合はスキップ
                error_log("Invalid date string in dateList: " . $dateString . " - " . $e->getMessage());
                return false;
            }
        });

        // 未来の対局日時が一件もない場合
        if (empty($futureGameDates)) {
            return '予定なし';
        }

        // 最も近い対局日時を特定
        $closestDateString = min($futureGameDates);

        // 日時と曜日のフォーマット
        $dateTime = new DateTime($closestDateString);
        $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
        $weekday = $weekdays[$dateTime->format('w')];
        $nextGameDay = $dateTime->format('n/j') . "(" . $weekday . ")";

        return $nextGameDay;
    }

    /*
    * 対局履歴の取得
    */
    public function getGameHistory() {
        $result = [];
        foreach ($this->groupAUserIds as $userId) {
            foreach ($this->historyDataList[$userId] as $historyData) {
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

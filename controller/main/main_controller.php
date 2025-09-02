<?php

class MainController extends BaseController
{
    const AGGREGATE_TABLE_ID = 1; // 集計対象のテーブルID
    
    private $masterData = [];
    private $statsService = null;
    private $statsColumn = null;



    public function __construct()
    {
        parent::__construct();
        
        // マスターデータを取得
        $this->masterData = $this->getAllMasterData();
        
        $this->initialize();
    }

    private function initialize()
    {

        $userMap         = indexByKey($this->masterData['uUserList'], 'u_user_id');
        // 集計対象のテーブル情報を取得
        $targetTableData = findFirstByKey($this->masterData['uTableList'], 'u_table_id', self::AGGREGATE_TABLE_ID);
        // 対象グループ情報を取得
        $targetGroupData = findFirstByKey($this->masterData['mGroupList'], 'm_group_id', $targetTableData['m_group_id']);
        // 対象ルール情報を取得
        $targetRuleData  = findFirstByKey($this->masterData['mRuleList'], 'm_rule_id', $targetGroupData['m_rule_id']);
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

        $this->statsService = new StatsService(
            $targetUserData,
            $targetTableData,    
            $targetGroupData,
            $targetRuleData,
            $this->masterData['mDirectionList'],
            $this->masterData['mGameDayList'],
            $this->masterData['mTitleList'],
            $this->masterData['uGameHistoryList'],
            $this->masterData['uTitleList'],
            $this->masterData['mTierList'],
            $this->masterData['uTierHistoryList'],
            $this->masterData['mBadgeList']
        );

        // カラム設定を取得
        $statsColumn = new StatsColumn();
        $this->statsColumn = $statsColumn->getColumn();
    }

    public function top()
    {
        // 対局日時の取得
        $nextTwoGameDays = $this->statsService->getNextTwoGameDays();

        // ビューファイルをインクルード
        include __DIR__ . '/../../view/main/top.php';
    }

    /**
     * 統計ページを表示
     */
    public function stats()
    {
        /* --- 表示用データの取得 -- */
        $userList = $this->statsService->getUserList();
        $years = $this->statsService->getYears();
        $titleHolderList = $this->statsService->getTitleHolder();
        $todayStatsList = $this->statsService->getTodayStatsList();
        $yearlyStatsList = $this->statsService->getYearlyStatsList();
        $yearlyChartList = $this->statsService->getYearlyChartList();
        
        // 年選択の処理
        $selectedYear = $_GET['year'] ?? date('Y');
        
        // 統計データの取得
        $mSettingList = $this->masterData['mSettingList'];
        
        // 表示用カラム定義を外部ファイルから読み込み
        $displayStatsColumn = $this->statsColumn;
        $displayStatsColumn_1 = $this->statsColumn['displayStatsColumn_1'] ?? [];
        $displayStatsColumn_2 = $this->statsColumn['displayStatsColumn_2'] ?? [];
        $statsColumnAllConfig_1 = $this->statsColumn['statsColumnAllConfig_1'] ?? [];
        $statsColumnAllConfig_2 = $this->statsColumn['statsColumnAllConfig_2'] ?? [];
        $displayStatsData = $todayStatsList ?? [];
        
        // スコア表示フラグの設定
        $scoreDisplayFlag = !(isset($mSettingList[1]['value']) && $mSettingList[1]['value'] && $selectedYear == date("Y"));
        
        // タイトルの設定
        $title = '成績';

        include __DIR__ . '/../../view/main/stats.php';
    }

    /**
     * 履歴ページを表示
     */
    public function history()
    {
        // POST処理（削除）
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['historyId']) && isset($_POST['userId'])) {
            $historyId = $_POST['historyId'];
            $userId = $_POST['userId'];
            
            // データ削除処理
            require_once __DIR__ . '/../../model/u_game_history.php';
            $uGameHistoryModel = new UGameHistory();
            $result = $uGameHistoryModel->deleteData($historyId);
            
            if ($result) {
                header("Location: history?userId=" . urlencode($userId));
                exit();
            } else {
                // 削除に失敗した場合のエラーハンドリング
                $error_msg = '削除処理中にエラーが発生しました。';
            }
        }
        
        // パラメータの取得
        $selectUser = $_GET['userId'] ?? null;
        $selectYear = $_GET['year'] ?? date("Y");
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        // ユーザーリストの取得
        $userList = $this->statsService->getUserList();
        
        // 方向リストの取得
        $mDirectionList = $this->masterData['mDirectionList'] ?? [];
        
        // 対局履歴データの取得
        $uGameHistoryList = $this->masterData['uGameHistoryList'] ?? [];
        $gameHistoryList = $this->statsService->getGameHistoryList();
        $dayStatsList = $this->statsService->getDayStats();
        
        // ページネーションの設定（個人履歴）
        $records_per_page = 15; // 1ページあたりの表示件数
        $offset = ($current_page - 1) * $records_per_page; // オフセット計算
        
        $total_records = 0;
        $filtered_data = [];
        $paginated_data = [];
        
        if ($selectUser && isset($uGameHistoryList[$selectUser])) {
            // 対象レコードの合計数を計算
            foreach ($uGameHistoryList[$selectUser] as $data) {
                if (date('Y', strtotime($data['play_date'])) == $selectYear || date('Y', strtotime($data['play_date'])) == $selectYear - 1) {
                    $total_records++;
                }
            }
            // 総ページ数を計算
            $total_pages = ceil($total_records / $records_per_page);
            // 表示するレコードをフィルタリングして配列に格納
            foreach ($uGameHistoryList[$selectUser] as $data) {
                if (date('Y', strtotime($data['play_date'])) == $selectYear || date('Y', strtotime($data['play_date'])) == $selectYear - 1) {
                    $filtered_data[] = $data;
                }
            }
            // 現在のページに表示するレコードのみを抽出
            $paginated_data = array_slice($filtered_data, $offset, $records_per_page);
        }
        
        // ページネーションの設定（対局履歴）
        $dates_per_page = 1; // 1ページあたりの表示日数
        
        // 日付の配列を取得
        $all_dates = array_keys($gameHistoryList);
        $total_dates = count($all_dates);
        $total_pages_game = ceil($total_dates / $dates_per_page);
        
        // 現在のページに表示する日付の範囲を計算
        $start_index = ($current_page - 1) * $dates_per_page;
        $current_dates = array_slice($all_dates, $start_index, $dates_per_page);
        
        // タイトルの設定
        $title = '履歴';
        
        include __DIR__ . '/../../view/main/history.php';
    }

    /**
     * 個人統計ページを表示
     */
    public function personalStats()
    {
        // パラメータの取得
        $selectedYear = $_GET['year'] ?? date("Y");
        $selectedPlayer = $_GET['player'] ?? null;
         
        // 年選択の処理
        $years = $this->statsService->getYears();
        
        // 年別統計データの取得
        $yearlyStatsList = $this->statsService->getYearlyStatsList();
        
        // ユーザーリストの取得
        $userList = $this->statsService->getUserList();
        
        // 方向リストの取得
        $mDirectionList = $this->masterData['mDirectionList'] ?? [];
        
        // 設定データの取得
        $mSettingList = $this->masterData['mSettingList'] ?? [];
        
        // スコア表示フラグの設定
        $scoreDisplayFlag = !(isset($mSettingList[1]['value']) && $mSettingList[1]['value'] && $selectedYear == date("Y"));
        
        // 選択された選手のデータを取得
        $playerData = null;
        if (isset($selectedYear) && isset($selectedPlayer) && isset($yearlyStatsList[$selectedYear])) {
            foreach ($yearlyStatsList[$selectedYear] as $data) {
                if ($data['u_user_id'] == $selectedPlayer) {
                    $playerData = $data;
                    break;
                }
            }
        }
        
        // 方向別統計データの取得（選手が選択されている場合のみ）
        $directionStats = $this->statsService->getRelativeScoreByDirection();
        
        // ランク履歴の取得（選手が選択されている場合のみ）
        $rankHistoryList = $this->statsService->getRankHistory();
        
        // 表示用カラム定義を外部ファイルから読み込み
        $displayStatsColumn = $this->statsColumn;
        $displayStatsColumn_1 = $this->statsColumn['displayStatsColumn_1'] ?? [];
        $displayStatsColumn_2 = $this->statsColumn['displayStatsColumn_2'] ?? [];
        $statsColumnAllConfig_1 = $this->statsColumn['statsColumnAllConfig_1'] ?? [];
        $statsColumnAllConfig_2 = $this->statsColumn['statsColumnAllConfig_2'] ?? [];
        
        // タイトルの設定
        $title = '個人成績';
        
        include __DIR__ . '/../../view/main/personal_stats.php';
    }

    /**
     * 分析ページを表示
     */
    public function analysis()
    {
        // パラメータの取得
        $selectUser = $_GET['userId'] ?? null;
    
        // ユーザーリストの取得
        $userList = $this->statsService->getUserList();
        
        // 方向リストの取得
        $mDirectionList = $this->masterData['mDirectionList'] ?? [];
        
        // 分析データの取得
        $analysisDataList = $this->statsService->getAnalysisDataList();
        
        // 表示用カラム定義を外部ファイルから読み込み
        $displayStatsColumn = $this->statsColumn;
        $statsNameConfig = $this->statsColumn['statsNameConfig'] ?? [];
        
        // 分析メッセージの設定
        $analysisMsg = <<<EOT
            麻雀の成績データを元に、指定された選手の分析をお願いします。
            出力はHTML形式で見やすくしてください。

            Mリーグルールを適用した成績データです。分析の際は以下の指標を参考にしてください。

            -   **平均順位**: 平均は **2.50** です。数値が低いほど良い成績です。
            -   **トップ率**: 平均は **25%** です。数値が高いほど良い成績です。
            -   **ラス回避率**: 平均は **75%** です。数値が高いほど良い成績です。
            -   **合計点 (素点 + 順位点)**: これが最も重要な指標です。高いほど総合的な成績が良いことを示します。素点との開きが大きい場合、順位取りが上手い傾向があります。
            -   **連対率 (1位・2位率)**: 平均は **50%** です。数値が高いほど良い成績です。
            -   **平均点 (対局終了時)**: 25000点から開始し、終了時の平均点数です。数値が高いほど良い成績です。
            -   **チョンボ数**: 対局中に罰則に該当する行為をした回数です。1回で合計ポイントから20ポイント引かれてしまうため影響が大きいです。0回が理想的です。

            提供される成績データは、これらの指標を基にしたものです。
        EOT;
        
        // 分析データの処理
        $set_msg = null;
        $score_msg = null;
        $userName = null;
        
        if ($selectUser && isset($userList[$selectUser])) {
            $userName = $userList[$selectUser]['last_name'] . $userList[$selectUser]['first_name'];
            
            // AIへの指示文を作成
            $set_msg = $analysisMsg;
            $set_msg .= "麻雀の成績データに基づいて、{$userName}選手の成績まとめ、特徴、改善点を300文字前後で出力してください。各家別成績はまだデータ数が少ないのであまり参考にしないでください。他にも気づいたことがあれば追加しても構いません。";
            
            $score_msg = "以下に{$userName}選手の麻雀成績の詳細データを示します。\n\n";
            
            // 主要な個人成績
            $score_msg .= "## 個人主要成績\n";
            if (isset($analysisDataList[$selectUser])) {
                foreach ($analysisDataList[$selectUser] as $key => $value) {
                    // statsNameConfig に定義されている主要な単一値のみを抽出
                    // rank_count, rank_probability, direction系の配列はここでは除外
                    if (isset($statsNameConfig[$key]) && !is_array($value)) {
                        $score_msg .= htmlspecialchars($statsNameConfig[$key]) . ": " . htmlspecialchars($value) . "\n";
                    }
                }
                $score_msg .= "\n";
                
                // 順位ごとの回数と確率
                if (isset($analysisDataList[$selectUser]['rank_count']) && is_array($analysisDataList[$selectUser]['rank_count'])) {
                    $score_msg .= "## 順位別回数と確率\n";
                    $rankCounts = [];
                    $rankProbs = [];
                    foreach ($analysisDataList[$selectUser]['rank_count'] as $rank => $count) {
                        $rankCounts[] = "{$rank}着: {$count}回";
                        // rank_probabilityも同時に取得できるなら
                        if (isset($analysisDataList[$selectUser]['rank_probability'][$rank])) {
                            $rankProbs[] = "{$rank}着率: " . htmlspecialchars($analysisDataList[$selectUser]['rank_probability'][$rank]);
                        }
                    }
                    $score_msg .= implode(", ", $rankCounts) . "\n";
                    if (!empty($rankProbs)) {
                        $score_msg .= implode(", ", $rankProbs) . "\n";
                    }
                    $score_msg .= "\n";
                }
            }
        }
        
        // タイトルの設定
        $title = 'AI成績分析';
        
        // Gemini APIの設定
        $api_key = 'AIzaSyBPPLGL-RgI732EuVylSSFNaIGr2NRQYJ4';
        $model = 'gemini-1.5-flash';
        $api_version = 'v1';
        $url = "https://generativelanguage.googleapis.com/{$api_version}/models/{$model}:generateContent?key={$api_key}";
        
        include __DIR__ . '/../../view/main/analysis.php';
    }

    /**
     * 設定ページを表示
     */
    public function setting()
    {
        // POST処理
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $settingId = $_POST['settingId'] ?? null;

            if ($settingId !== null) {
                try {
                    require_once __DIR__ . '/../../model/m_setting.php';
                    $mSettingModel = new MSetting();
                    $mSettingModel->switchMode($settingId);
                    header("Location: setting");
                    exit();
                } catch (Exception $e) {
                    $error_msg = '処理中にエラーが発生しました: ' . $e->getMessage();
                }
            } else {
                $error_msg = '必要なパラメータが不足しています';
            }
        }
        
        // データ取得
        $mSettingList = $this->masterData['mSettingList'] ?? [];
        
        // タイトルの設定
        $title = '設定';
        
        include __DIR__ . '/../../view/main/setting.php';
    }

    /**
     * ルールページを表示
     */
    public function rule()
    {
        // タイトルの設定
        $title = 'AISAI.M.LEAGUE 競技ルール規定';
        
        include __DIR__ . '/../../view/main/rule.php';
    }

    /**
     * バッジページを表示
     */
    public function badge()
    {
        // パラメータの取得
        $userId = $_GET['userId'] ?? null;
        
        // ユーザーリストの取得
        $userList = $this->statsService->getUserList();
        
        // バッジリストの取得
        $mBadgeList = $this->masterData['mBadgeList'] ?? [];
        
        // ユーザーが所持しているバッジIDの取得
        $userPossessionBadgeIds = array_keys($mBadgeList);
        
        // 現在のユーザーバッジIDの取得
        $currentUserBadgeId = $userList[$userId]['badge']['m_badge_id'] ?? 0;
        
        // POST処理（バッジ変更）
        $successMessage = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['m_badge_id'])) {
            $selectedBadgeId = (int)$_POST['m_badge_id'];
            
            if (in_array($selectedBadgeId, $userPossessionBadgeIds)) {
                // バッジ更新処理（モデルを使用）
                $uUserModel = new UUser();
                $uUserModel->updateUserBadge($userId, $selectedBadgeId);
                
                $currentUserBadgeId = $selectedBadgeId;
                
                $successMessage = "称号を「" . htmlspecialchars($mBadgeList[$selectedBadgeId]['name']) . "」に変更しました。";
            }
        }
        
        // 現在の称号データを取得
        $currentBadgeData = $mBadgeList[$currentUserBadgeId] ?? null;
        
        // タイトルの設定
        $title = '称号';
        
        include __DIR__ . '/../../view/main/badge.php';
    }

    /**
     * サウンドページを表示
     */
    public function sound()
    {
        // タイトルの設定
        $title = 'サウンド';

        include __DIR__ . '/../../view/main/sound.php';
    }

    /**
     * 追加ページを表示
     */
    public function add()
    {
        // POST処理
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $requiredFields = ['userId', 'tableId', 'game', 'direction', 'rank', 'score', 'year', 'month', 'day'];
            $missingFields = array_filter($requiredFields, fn($field) => !isset($_POST[$field]));
            if (empty($missingFields)) {
                $userId    = $_POST['userId'];
                $tableId   = $_POST['tableId'];
                $game      = $_POST['game'];
                $direction = $_POST['direction'];
                $rank      = $_POST['rank'];
                $score     = $_POST['score'];
                $mistakeCount = isset($_POST['mistake_count']) ? (int)$_POST['mistake_count'] : 0;
                $playDate  = sprintf(
                    '%04d-%02d-%02d %s',
                    $_POST['year'],
                    $_POST['month'],
                    $_POST['day'],
                    date('H:i:s')
                );
                
                // データ追加処理
                require_once __DIR__ . '/../../model/u_game_history.php';
                $uGameHistoryModel = new UGameHistory();
                $uGameHistoryModel->addData($userId, $tableId, $game, $direction, $rank, $score, $playDate, $mistakeCount);
                header("Location: history?userId=" . urlencode($userId));
                exit();
            } else {
                $error_msg = '登録に失敗しました。以下のフィールドが不足しています: ' . implode(', ', $missingFields);
            }
        }
        
        // データ取得
        $userList = $this->statsService->getUserList();
        $mDirectionList = $this->masterData['mDirectionList'] ?? [];
        $rankConfig = $this->statsColumn['rankConfig'] ?? [];
        $todayStatsList = $this->statsService->getTodayStatsList();
        
        // プレイ回数を取得
        $games = array_column($todayStatsList, 'play_count', 'user_id');
        
        // タイトルの設定
        $title = '登録';
        
        include __DIR__ . '/../../view/main/add.php';
    }

    /**
     * 更新ページを表示
     */
    public function update()
    {
        // POST処理
        $isFix = false;
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (isset($_POST['historyId']) && isset($_POST['rank']) && isset($_POST['score']) && isset($_POST['game']) && isset($_POST['direction']) && isset($_POST['userId'])) {
                $isFix = true;
                $historyId  = $_POST['historyId'];
                $rank       = $_POST['rank'];
                $score      = $_POST['score'];
                $game       = $_POST['game'];
                $direction  = $_POST['direction'];
                $userId     = $_POST['userId'];
            }
            if (isset($_POST['historyId']) && isset($_POST['new_rank']) && isset($_POST['new_score']) && isset($_POST['new_game']) && isset($_POST['new_direction']) && isset($_POST['userId'])) {
                $historyId  = $_POST['historyId'];
                $rank       = $_POST['new_rank'];
                $score      = $_POST['new_score'];
                $game       = $_POST['new_game'];
                $direction  = $_POST['new_direction'];
                $userId     = $_POST['userId'];
                
                // データ更新処理
                require_once __DIR__ . '/../../model/u_game_history.php';
                $uGameHistoryModel = new UGameHistory();
                $uGameHistoryModel->updateData((int)$historyId, $rank, (int)$score, (int)$game, (int)$direction);
                header("Location: history?userId=" . $userId);
                exit();
            }
        }
        
        // 方向リストの取得
        $mDirectionList = $this->masterData['mDirectionList'] ?? [];
        
        // 順位設定の取得
        $rankConfig = $this->statsColumn['rankConfig'] ?? [];
        
        // タイトルの設定
        $title = '修正';
        
        // ベースURLの設定
        require_once __DIR__ . '/../../config/environment.php';
        $baseUrl = getBaseUrl();
        
        include __DIR__ . '/../../view/main/update.php';
    }
}
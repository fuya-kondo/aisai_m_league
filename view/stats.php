<?php

// Include necessary files
require_once __DIR__ . '/../config/import_file.php';
// Include header
include '../webroot/common/header.php';

// Get parameter
$selectedYear = isset($_GET['year']) ? $_GET['year'] : date("Y");
$selectedPlayer = isset($_GET['player']) ? $_GET['player'] : 1;

$scoreDisplayFlag = !( $mSettingList[1]['value'] && $selectedYear == date("Y") );

// Set title
$title = '成績';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <link rel="apple-touch-icon" href="../favicon.png">
    <link rel="icon" href="../favicon.ico" sizes="64x64" type="image/x-icon">
    <link rel="stylesheet" href="../webroot/css/master.css">
    <link rel="stylesheet" href="../webroot/css/header.css">
    <link rel="stylesheet" href="../webroot/css/app.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.min.js"></script>
    <title><?= $title ?></title>
</head>
<body>
<main>
    <?php /* 本日の成績 */?>
        <?php if(!empty($todayStatsList[1]['play_count'])):?>
            <div class="page-title">本日の<?= $title ?></div>
            <div class="table-container">
                <div class="table-wrapper">
                        <table class="score-table">
                            <tr>
                                <?php foreach($displayStatsColumn_1 as $column => $data):?>
                                    <?php if (is_array($data)): ?>
                                        <?php foreach($data as $key => $value):?>
                                            <th><?=$value?></th>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <th><?=$data?></th>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                            <?php foreach($todayStatsList as $todayStatsData): ?>
                                <tr align="center">
                                    <?php foreach ($displayStatsColumn_1 as $column => $data): ?>
                                        <?php if ($column == 'ranking'): ?>
                                            <td>
                                                <span class="rank-column">
                                                    <span class="rank-icon rank-<?= $todayStatsData[$column] ?>">
                                                        <span class="stats-value"><?= $todayStatsData[$column] ?></span>
                                                    </span>
                                                </span>
                                            </td>
                                        <?php elseif ($column == 'name'): ?>
                                            <td>
                                                <a href="personal_stats?year=<?= $selectedYear ?>&player=<?= $todayStatsData['u_user_id'] ?>">
                                                    <span class="stats-value"><?= $todayStatsData[$column] ?></span>
                                                </a>
                                            </td>
                                        <?php else: // その他のカラムの場合 ?>
                                            <?php if (is_array($data) && isset($todayStatsData[$column]) && is_array($todayStatsData[$column])): ?>
                                                <?php foreach ($todayStatsData[$column] as $key => $value): ?>
                                                    <td><span class="stats-value"><?= $value ?></span></td>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <td><span class="stats-value"><?= $todayStatsData[$column] ?></span></td>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
            </div>
        <?php endif;?>
    <?php /* 本日の成績 */?>

    <?php /* 総合成績 */?>
    <div class="page-title"><?= $title ?></div>
    <?php foreach($yearlyStatsList as $year => $displayStatsData): ?>
        <?php if ($year == $selectedYear): // 選択された年のみ表示 ?>
            <div class="table-container">
                <form class="year-title" action="" method="get" class="year-selection-form">
                    <select name="year" id="year" onchange="this.form.submit()" class="year-select">
                        <?php foreach($years as $year): ?>
                            <option value="<?= $year ?>" <?= ($year == $selectedYear) ? 'selected' : '' ?>><?= $year ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="play-count">対局数：<?=$displayStatsData[1]['play_count'] ?><br></span>
                </form>
                <div class="table-wrapper">
                    <table class="score-table">
                        <tr>
                            <?php foreach($displayStatsColumn_1 as $column => $data):?>
                                <?php if (is_array($data)): ?>
                                    <?php foreach($data as $key => $value):?>
                                        <th><?=$value?></th>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <th><?=$data?></th>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                        <?php
                            $maxPoint = 0;
                            $minPoint = 0;
                            foreach($displayStatsData as $statusData) {
                                $maxPoint = max($statusData['sum_point'], $maxPoint);
                                $minPoint = min($statusData['sum_point'], $minPoint);
                            }
                        ?>
                        <?php foreach($displayStatsData as $statusData): ?>
                            <tr align="center">
                                <?php foreach ($displayStatsColumn_1 as $column => $data): ?>
                                    <?php if ($column == 'ranking'): ?>
                                        <?php
                                            if ( $statusData['sum_point'] == $maxPoint || $statusData['sum_point'] == $minPoint) {
                                                $rgbaValue[$statusData[$column]] = 90;
                                            } else {
                                                $rgbaValue[$statusData[$column]] = max((int)$statusData['sum_point'] / $maxPoint * 80, 50); // 最大点数に基づく透明度の計算
                                            }
                                        ?>
                                        <td>
                                            <span class="rank-column">
                                                <span class="rank-icon rank-<?= $statusData[$column] ?>">
                                                    <span class="stats-value"><?= $statusData[$column] ?></span>
                                                </span>
                                            </span>
                                        </td>
                                    <?php elseif ($column == 'name'): ?>
                                        <td class="player-name-<?=$statusData['ranking']?>-<?php if($statusData['sum_point']>=0):?>p<?php else:?>m<?php endif;?>">
                                            <a href="personal_stats?year=<?= $selectedYear ?>&player=<?= $statusData['u_user_id'] ?>">
                                                <span class="stats-value"><?= $statusData[$column] ?></span>
                                                <!-- <span class="badge"><?= $userList[$statusData['u_user_id']]['badge']['name'] ?></span> -->
                                            </a>
                                        </td>
                                    <?php elseif ($column == 'sum_point' && !$scoreDisplayFlag): ?>
                                        <td><span class="stats-value"> --- </span></td>
                                    <?php else: // その他のカラムの場合 ?>
                                        <?php if (is_array($data) && isset($statusData[$column]) && is_array($statusData[$column])): ?>
                                            <?php foreach ($statusData[$column] as $key => $value): ?>
                                                <td><span class="stats-value"><?= $value ?></span></td>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <td><span class="stats-value"><?= $statusData[$column] ?></span></td>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php /* 総合成績 */?>

    <?php /* グラフ */?>
    <?php if ($scoreDisplayFlag): ?>
        <div style="height: 400px; margin-bottom:150px;">
            <canvas id="userPointsChart"></canvas>
        </div>
        <?php
            $userData = [];
            $allDates = [];
            foreach ($userList as $userId => $data) {
                foreach ($yearlyChartList[$selectedYear][$userId] as $data) {
                    // 日付形式の厳密なチェックを追加
                    $playDate = date('Y-m-d', strtotime($data['play_date']));
                    // ポイントの数値変換を厳密化
                    $point = filter_var($data['point'], FILTER_VALIDATE_FLOAT);
                    if ($point === false) {
                        $point = 0;
                    }
                    // ユーザーデータ初期化
                    if (!isset($userData[$userId])) {
                        $userData[$userId] = [];
                    }
                    // ポイント加算処理（Null合体演算子を使用）
                    $userData[$userId][$playDate] = ($userData[$userId][$playDate] ?? 0) + $point;
                    $allDates[$playDate] = true;
                }
            }

            // 日付をソート
            ksort($allDates);
            $dates = array_keys($allDates);

            // 累積ポイントを計算
            $datasets = [];
            $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'];

            foreach ($userData as $userId => $points) {
                $dataPoints = [];
                $total = 0; // 累積ポイントの初期化
                foreach ($dates as $date) {
                    if (isset($points[$date])) {
                        $total += $points[$date]; // 累積計算
                    }
                    // データポイントを追加
                    $dataPoints[] = ['x' => $date, 'y' => $total];
                }
                // データセットを作成
                $datasets[] = [
                    'label' => $userList[$userId]['last_name'],
                    'data' => $dataPoints,
                    'borderColor' => $colors[$userId % count($colors)],
                    'fill' => false
                ];
            }
        ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/moment"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment"></script>
        <script>
            var datasets = <?php echo json_encode($datasets); ?>;
            var dates = <?php echo json_encode($dates); ?>;

            var originDate = new Date(dates[0]);
            originDate.setMonth(originDate.getMonth() - 1);
            var originDateString = originDate.toISOString().split('T')[0];

            datasets.forEach(dataset => {
                // 原点のデータポイントを追加（既存のデータポイントより前に挿入）
                dataset.data.unshift({x: originDateString, y: 0 });
            });
            datasets.forEach(dataset => {
                // 最後の日付の1ヶ月後の日付を計算して追加
                const lastDate = new Date(dataset.data[dataset.data.length - 1].x);
                const emptyDate = new Date(lastDate.setMonth(lastDate.getMonth() + 1));
                dataset.data.push({ x: emptyDate.toISOString().split('T')[0], y: null });
            });
            var ctx = document.getElementById('userPointsChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'line',
                data: {
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'ポイント推移'
                        }
                    },
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: 'month',
                                displayFormats: {
                                    month: 'YY/M'
                                }
                            }
                        },
                    },
                    elements: {
                        line: {
                            spanGaps: true // null値をスキップしてラインを続ける
                        }
                    }
                }
            });
        </script>
    <?php endif; ?>
    <?php /* グラフ */?>

    <?php /* タイトル */ ?>
    <?php if( isset($titleHolderList[$selectedYear]) ):?>
        <div class="page-title">🏆 タイトル獲得履歴 🏆</div>
        <?php foreach ($titleHolderList as $year => $titles): ?>
            <?php if ($year == $selectedYear): // 選択された年のみ表示 ?>
                <div class="year-section">
                    <h2 class="year-header"><?= $year ?>年</h2>
                    <?php foreach ($titles as $item): ?>
                        <div class="title-item">
                            <div class="title-name">
                                <?= $item['title_name'] ?>
                            </div>
                            <div class="user-info">
                                <span><?= $item['u_user_id'] ?></span>
                                <span class="value"><?= $item['value'] ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php /* タイトル */ ?>

</main>
</body>
</html>

<style>
    .table-container {
        background-color: #fff;
        border-radius: 4px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
        overflow: hidden;
    }
    .year-title {
        background-color: #009944;
        color: #fff;
        padding: 15px;
        margin: 0;
        font-size: 1.2em;
    }
    .table-wrapper {
        overflow-x: auto;
        padding: 15px;
    }
    .score-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .score-table th,
    .score-table td {
        padding: 12px;
        text-align: center;
    }
    .score-table th {
        background-color: #c9caca;
        border: none;
    }
    .score-table th:first-child {
        border-top-left-radius: 4px;
    }
    .score-table th:last-child {
        border-top-right-radius: 4px;
    }
    .score-table td {
        border-bottom: 1px solid #e0e0e0;
    }
    .score-table tr:last-child td:first-child {
        border-bottom-left-radius: 4px;
    }
    .score-table tr:last-child td:last-child {
        border-bottom-right-radius: 4px;
    }
    .year-selection-form {
        text-align: center; /* 中央揃え */
        margin-bottom: 20px; /* 下部マージン */
    }
    .year-selection-form label {
        font-size: 1.2em; /* ラベルのフォントサイズ */
        margin-right: 10px; /* ラベルとプルダウンの間隔 */
    }
    .year-select {
        padding: 5px 10px;
        font-size: 1em;
        border-radius: 4px;
        border: 0px;
        background-color: #009944;
        color: white;
        font-weight: bold;
    }
    .year-select:hover,
    .year-select:focus {
        border-color: #009944; /* フォーカス時やホバー時のボーダー色 */
        outline: none; /* デフォルトのアウトラインを無効化 */
    }
    .play-count {
        font-size: 0.8em;
        float: right;
        margin: 5px;
    }
    .rank-column {
        position: relative;
        display: flex;      /* これが重要！Flexboxコンテナにする */
        justify-content: center; /* 水平方向の中央寄せ */
        align-items: center;   /* 垂直方向の中央寄せ */
        height: 100%;          /* tdの高さ全体を使うようにする */
    }
    .rank-icon {
        width: 20px;
        height: 20px;
        display: flex;         /* アイコン内の数字を中央寄せするために維持 */
        justify-content: center;
        align-items: center;
        z-index: 1;
        border-radius: 50%;
    }
    .rank-column .stats-value {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 2;
    }
    .rank-1 {
        background-color: #c1ab05;
        color: #ffffff;
    }
    .rank-2 {
        background-color: #c9caca;
        color: #000000;
    }
    .rank-3 {
        background-color: #ac6b25;
        color: #ffffff;
    }
    .rank-4 {
        background-color: white;
    }
    <?php if ($scoreDisplayFlag): ?>
    .player-name-1-p {
        background: linear-gradient(to right,
                rgba(0, 153, 68, 0) 0%,
                rgba(0, 153, 68, 0) 50%,
                rgba(0, 153, 68, 0.05) 50%,
                rgba(0, 153, 68, 0.05) <?=$rgbaValue[1]?>%,
                rgba(0, 153, 68, 0) <?=$rgbaValue[1]+10?>%);
    }
    .player-name-2-p {
        background: linear-gradient(to right,
                rgba(0, 153, 68, 0) 0%,
                rgba(0, 153, 68, 0) 50%,
                rgba(0, 153, 68, 0.05) 50%,
                rgba(0, 153, 68, 0.05) <?=$rgbaValue[2]?>%,
                rgba(0, 153, 68, 0) <?=$rgbaValue[2]+10?>%);
    }
    .player-name-2-m {
        background: linear-gradient(to left,
                rgba(153, 0, 0, 0) 0%,
                rgba(153, 0, 0, 0) 50%,
                rgba(153, 0, 0, 0.05) 50%,
                rgba(153, 0, 0, 0.05) <?=$rgbaValue[2]?>%,
                rgba(153, 0, 0, 0) <?=$rgbaValue[2]+10?>%);
    }
    .player-name-3-p {
        background: linear-gradient(to right,
                rgba(0, 153, 68, 0) 0%,
                rgba(0, 153, 68, 0) 50%,
                rgba(0, 153, 68, 0.05) 50%,
                rgba(0, 153, 68, 0.05) <?=$rgbaValue[3]?>%,
                rgba(0, 153, 68, 0) <?=$rgbaValue[3]+10?>%);
    }
    .player-name-3-m {
        background: linear-gradient(to left,
                rgba(153, 0, 0, 0) 0%,
                rgba(153, 0, 0, 0) 50%,
                rgba(153, 0, 0, 0.05) 50%,
                rgba(153, 0, 0, 0.05) <?=$rgbaValue[3]?>%,
                rgba(153, 0, 0, 0) <?=$rgbaValue[3]+10?>%);
    }
    .player-name-4-m {
        background: linear-gradient(to left,
                rgba(153, 0, 0, 0) 0%,
                rgba(153, 0, 0, 0) 50%,
                rgba(153, 0, 0, 0.05) 50%,
                rgba(153, 0, 0, 0.05) <?=$rgbaValue[4]?>%,
                rgba(153, 0, 0, 0) <?=$rgbaValue[4]+10?>%);
    }
    <?php endif; ?>
    @media screen and (max-width: 768px) {
        .table-wrapper {
            padding: 10px 7px;
        }
        .score-table th,
        .score-table td {
            padding: 10px 3px;
            font-size: 0.85em;
        }
        .score-table th {
            text-align: center; /* 中央揃え */
            line-height: 1.5; /* 行間を調整 */
        }
    }

    .year-section {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 15px;
        overflow: hidden; /* Clear floats */
    }
    .year-header {
        font-size: 1.5em;
        color: #3498db;
        margin-bottom: 10px;
        border-bottom: 2px solid #eee;
        padding-bottom: 8px;
    }
    .title-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px dashed #eee;
    }
    .title-item:last-child {
        border-bottom: none;
    }
    .title-name {
        font-weight: bold;
        color: #555;
        flex-grow: 1; /* 名前が長い場合にも対応 */
    }
    .user-info {
        text-align: right;
        font-size: 0.9em;
        color: #777;
    }
    .user-info span {
        display: block; /* ユーザーIDと値を縦に表示 */
    }
    .user-info .value {
        font-weight: bold;
        color: #e74c3c;
    }

    /* スマートフォン向けレスポンシブデザイン */
    @media (max-width: 600px) {
        .year-header {
            font-size: 1.3em;
        }
        .title-item {
            flex-direction: column; /* 縦並びにする */
            align-items: flex-start;
        }
        .user-info {
            text-align: left;
            margin-top: 5px; /* スペースを追加 */
        }
    }
</style>

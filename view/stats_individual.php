<?php
// Include necessary files
require_once dirname(dirname(__FILE__)).'/controller/MahjongController.php';
// Include header
include 'common/header.php';

// Get data from controller
$overallStatsData = $mahjongStats->getOverallStatsData();

// Get parameter
$selectedYear   = isset( $_GET['year'] ) ? $_GET['year']   : date("Y");
$selectedPlayer = isset($_GET['player']) ? $_GET['player'] : null;

// Set title
$title = '個人成績';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <link rel=”icon” type=”image/png” href=“/image/favicon_64-64.png”>
    <link rel="stylesheet" href="css/master.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/button.css">
    <link rel="stylesheet" href="css/table.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title><?= $title; ?></title>
</head>
<body>
    <main>
        <?php if (!isset($selectedPlayer)): ?>
            <div class="page-title"><?= $title; ?></div>
            <div class="select-button-container">
                <form action="stats_individual.php" method="get">
                    <?php foreach($u_mahjong_user_result as $userData): ?>
                        <?php if ($userData['u_mahjong_user_id'] == 0): continue; endif;?>
                        <button class="select-button" type="submit" name="player" value="<?=$userData['u_mahjong_user_id']?>"><?=$userData['name']?></button>
                    <?php endforeach; ?>
                </form>
            </div>
        <?php else: ?>
            <?php
                $players = [];
                if (isset($overallStatsData[$selectedYear])) {
                    foreach ($overallStatsData[$selectedYear] as $playerData) {
                        $players[$playerData['user_id']] = $playerData['name'];
                    }
                }
            ?>
            <?php if(isset($players[$selectedPlayer])):?>
                <div class="page-title"><?= $players[$selectedPlayer] . 'の' . $title; ?></div>
            <?php else: ?>
                <div class="page-title"><?= $title; ?></div>
            <?php endif; ?>
            <form action="" method="get" class="player-selection-form">
                <div class="selection-container">
                    <div style="margin-bottom:10px">
                        <label for="year">年を選択:</label>
                        <select name="year" id="year" onchange="this.form.submit()" class="year-select">
                            <?php foreach($overallStatsData as $year => $displayStatsData): ?>
                                <option value="<?= $year ?>" <?= ($year == $selectedYear) ? 'selected' : '' ?>><?= $year ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="player">選手を選択:</label>
                        <select name="player" id="player" onchange="this.form.submit()" class="player-select">
                            <?php foreach($players as $playerId => $playerName): ?>
                                <option value="<?= $playerId ?>" <?= ($playerId == $selectedPlayer) ? 'selected' : '' ?>><?= $playerName ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </form>

            <?php if (isset($selectedYear) && isset($selectedPlayer)): ?>
                <div class="table-container">
                    <div class="table-wrapper">
                        <table class="score-table">
                            <?php
                                // 選択された選手のデータを取得
                                $playerData = null;
                                foreach($overallStatsData[$selectedYear] as $data) {
                                    if ($data['user_id'] == $selectedPlayer) {
                                        $playerData = $data;
                                        break;
                                    }
                                }
                                // データが存在する場合のみ処理
                                if ($playerData):
                                    $columns_1 = array_keys($statsColumnAllConfig_1); // カラムキーを取得
                                    $columns_2 = array_keys($statsColumnAllConfig_2); // カラムキーを取得
                                    $totalRows = count($columns_2); // 全体の行数
                                    $halfRows = ceil($totalRows / 2); // 折り返し位置（半分の行数）
                            ?>
                                <tbody>
                                    <tr>
                                        <?php for ($i = 0; $i < count(array_keys($statsColumnAllConfig_1)); $i++): ?>
                                            <td class="stats-colum"><?= htmlspecialchars($statsColumnAllConfig_1[$columns_1[$i]]) ?></td>
                                            <td><?= htmlspecialchars($playerData[$columns_1[$i]]) ?></td>
                                        <?php endfor; ?>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="score-table">
                                <tbody>
                                    <?php for ($i = 0; $i < $halfRows; $i++): ?>
                                        <tr>
                                            <!-- 左側の列 -->
                                            <?php if (isset($columns_2[$i])): ?>
                                                <td class="stats-colum"><?= htmlspecialchars($statsColumnAllConfig_2[$columns_2[$i]]) ?></td>
                                                <td><?= htmlspecialchars($playerData[$columns_2[$i]]) ?></td>
                                            <?php else: ?>
                                                <td></td><td></td> <!-- 空セル -->
                                            <?php endif; ?>
                                            <!-- 右側の列 -->
                                            <?php if (isset($columns_2[$i + $halfRows])): ?>
                                                <td class="stats-colum"><?= htmlspecialchars($statsColumnAllConfig_2[$columns_2[$i + $halfRows]]) ?></td>
                                                <td><?= htmlspecialchars($playerData[$columns_2[$i + $halfRows]]) ?></td>
                                            <?php else: ?>
                                                <td></td><td></td> <!-- 空セル -->
                                            <?php endif; ?>
                                        </tr>
                                    <?php endfor; ?>
                                </tbody>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">選手のデータが見つかりません。</td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($selectedYear) && isset($selectedPlayer) && $playerData): ?>
                <div class="charts-container">
                    <div class="chart-card">
                        <h3>着順分布</h3>
                        <div class="chart-wrapper">
                            <canvas id="rankingChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-card">
                        <h3>総合成績評価</h3>
                        <div class="chart-wrapper">
                            <canvas id="performanceRadarChart"></canvas>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var playerData = <?= json_encode($playerData) ?>;
                        var rankingCtx = document.getElementById('rankingChart').getContext('2d');
                        new Chart(rankingCtx, {
                            type: 'pie',
                            data: {
                                labels: ['1着', '2着', '3着', '4着'],
                                datasets: [{
                                    data: [
                                        playerData.first_rank_count,
                                        playerData.second_rank_count,
                                        playerData.third_rank_count,
                                        playerData.fourth_rank_count
                                    ],
                                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {position: 'bottom'},
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                var label = context.label || '';
                                                var value = context.raw;
                                                var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                var percentage = Math.round((value / total) * 100);
                                                return `${label}: ${value}回 (${percentage}%)`;
                                            }
                                        }
                                    }
                                }
                            }
                        });

                        // 成績レーダーチャート
                        const performanceRadarCtx = document.getElementById('performanceRadarChart').getContext('2d');
                        playerData.average_score = playerData.average_score.replace(/,/g, '');

                        // データを正規化する関数（0%未満は0に）
                        function normalizeData(value, min, max) {
                            const normalized = ((value - min) / (max - min)) * 100;
                            return normalized < 0 ? 0 : normalized;
                        }

                        // 表示用の基準値を設定（100%として表示する値）
                        const baselineMax = 100;

                        // 平均値データ（例として設定、実際のデータに置き換える）
                        const averageData = {
                            top_probability: 25, // 例: 25%
                            over_second_probability: 50, // 例: 50%
                            over_third_probability: 75, // 例: 75%
                            average_score: 25000 // 例: 25000点
                        };

                        new Chart(performanceRadarCtx, {
                            type: 'radar',
                            data: {
                                labels: ['トップ率', '連対率', 'ラス回避率', '平均点'],
                                datasets: [
                                    // グレーの基準値データセットを削除

                                    // 平均値データ
                                    {
                                        label: '平均値',
                                        data: [
                                            normalizeData(averageData.top_probability, 20, 30),
                                            normalizeData(averageData.over_second_probability, 40, 60),
                                            normalizeData(averageData.over_third_probability, 60, 85),
                                            normalizeData(averageData.average_score, 20000, 30000)
                                        ],
                                        backgroundColor: 'rgba(100, 149, 237, 0.2)', // 青系の背景色
                                        borderColor: 'rgba(100, 149, 237, 0.8)',
                                        borderWidth: 1,
                                        pointRadius: 2,
                                        fill: true,
                                        order: 2
                                    },
                                    // プレイヤーのデータ
                                    {
                                        label: playerData.name,
                                        data: [
                                            normalizeData(parseFloat(playerData.top_probability), 20, 30),
                                            normalizeData(parseFloat(playerData.over_second_probability), 42, 58),
                                            normalizeData(parseFloat(playerData.over_third_probability), 62, 82),
                                            normalizeData(parseFloat(playerData.average_score), 22000, 27000)
                                        ],
                                        backgroundColor: 'rgba(255, 99, 132, 0.2)', // ピンク系
                                        borderColor: 'rgba(255, 99, 132, 1)',
                                        borderWidth: 1,
                                        fill: true,
                                        order: 1
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                scales: {
                                    r: {
                                        angleLines: {
                                            display: true
                                        },
                                        suggestedMin: 0,
                                        suggestedMax: baselineMax, // 基準値を100%として設定
                                        ticks: {
                                            stepSize: 20,
                                            callback: function(value) {
                                                return value; // パーセント記号を削除
                                            }
                                        },
                                        pointLabels: {
                                            fontSize: 14
                                        },
                                        grid: {
                                            color: 'rgba(0, 0, 0, 0.1)'
                                        }
                                    }
                                },
                                plugins: {
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                const value = context.raw;
                                                let actualValue = '';

                                                // 平均値データセットとプレイヤーデータの両方に対応
                                                switch (context.dataIndex) {
                                                    case 0: // トップ率
                                                        actualValue = (value / 10 + 20).toFixed(2) + '%';
                                                        break;
                                                    case 1: // 連対率
                                                        actualValue = value.toFixed(2) + '%';
                                                        break;
                                                    case 2: // ラス回避率
                                                        actualValue = value.toFixed(2) + '%';
                                                        break;
                                                    case 3: // 平均点
                                                        actualValue = Math.round(value * (30000 - 20000) / 100 + 20000);
                                                        break;
                                                }
                                                const datasetLabel = context.dataset.label;
                                                // 100%を超えている場合は視覚的に強調
                                                if (value > 100) {
                                                    return `${datasetLabel} ${context.label}: ${actualValue} (基準値を超過)`;
                                                } else {
                                                    return `${datasetLabel} ${context.label}: ${actualValue}`;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    });
                </script>
                <style>
                    canvas {
                        max-width: 100%;
                        height: auto;
                    }
                    .charts-container {
                        display: grid;
                        grid-template-columns: repeat(2, 1fr);
                        gap: 20px;
                        margin-top: 30px 10px;
                    }

                    .chart-card {
                        background-color: white;
                        border-radius: 8px;
                        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                        padding: 15px;
                    }

                    .chart-card h3 {
                        text-align: center;
                        color: #228b22;
                        margin-top: 0;
                        margin-bottom: 15px;
                    }

                    .chart-wrapper {
                        display: flex;
                        justify-content: center;"
                        height: 300px;
                        position: relative;
                    }

                    @media screen and (max-width: 768px) {
                        .charts-container {
                            grid-template-columns: 1fr;
                        }
                        .chart-wrapper {
                            height: 250px;
                        }
                    }
                </style>
            <?php endif; ?>
        <?php endif;?>
        </main>
    </body>
</html>

<style>
    .player-selection-form{
        text-align: center;
        margin-bottom: 20px;
    }
    .table-container {
        margin-bottom: 30px;
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
        font-weight: bold;
        border-bottom: 1px solid #e0e0e0;
        top: 0;
        z-index: 10;
    }
    .score-table th:first-child {
        border-top-left-radius: 8px;
    }
    .score-table th:last-child {
        border-top-right-radius: 8px;
    }
    .score-table td {
        border-bottom: 1px solid #e0e0e0;
    }
    .stats-colum {
        font-weight: bold;
    }
    .year-select, .player-select {
        padding: 10px; /* 内側の余白 */
        font-size: 1em; /* フォントサイズ */
        border-radius: 4px; /* 角丸 */
        border: 1px solid #ccc; /* ボーダー */
        background-color: white; /* 背景色 */
        transition: border-color 0.3s ease; /* ボーダー色の変化にトランジションを追加 */
    }

    .year-select:hover, .player-select:hover
    .year-select:focus, .player-select:focus {
        border-color: #228b22; /* フォーカス時やホバー時のボーダー色 */
        outline: none; /* デフォルトのアウトラインを無効化 */
    }
    @media screen and (max-width: 768px) {
        .table-wrapper {
            padding: 10px;
        }
        .score-table th,
        .score-table td {
            padding: 10px 8px;
            font-size: 0.85em;
        }
        .score-table th {
            -webkit-writing-mode: vertical-rl;
            -ms-writing-mode: tb-rl;
            writing-mode: vertical-rl;
            text-align: center; /* 中央揃え */
            line-height: 1.5; /* 行間を調整 */
        }
    }
    @media (orientation: portrait) {
        .score-table th {
            writing-mode: vertical-rl;
        }
    }

    @media (orientation: landscape) {
        .score-table th {
            writing-mode: horizontal-tb; /* 横書きに変更 */
        }
    }
</style>

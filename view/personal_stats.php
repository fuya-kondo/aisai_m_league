<?php
// Include necessary files
require_once __DIR__ . '/../config/import_file.php';
// Include header
include '../webroot/common/header.php';

// Get parameter
$selectedYear   = isset( $_GET['year'] ) ? $_GET['year']   : date("Y");
$selectedPlayer = isset($_GET['player']) ? $_GET['player'] : null;

$scoreDisplayFlag = !( $mSettingList[1]['value'] && $selectedYear == date("Y") );

// Set title
$title = '個人成績';
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title><?= $title ?></title>
</head>
<body>
<main>
    <?php if (!isset($selectedPlayer)): ?>
        <div class="page-title"><?= $title ?></div>
        <div class="select-button-container">
            <form action="personal_stats" method="get">
                <?php foreach($userList as $userId => $userData): ?>
                    <button class="select-button" type="submit" name="player" value="<?=$userId?>"><?=$userData['last_name'].$userData['first_name']?></button>
                <?php endforeach; ?>
            </form>
        </div>
    <?php else: ?>
        <?php if( isset($userList[$selectedPlayer]) ):?>
            <div class="profile-header">
                <div class="profile-info">
                    <?php if ( isset($userList[$selectedPlayer]['tier']) ): ?>
                        <span class="tier" style="color:<?=$userList[$selectedPlayer]['tier']['color']?>">
                            <div class="scroll-btn" data-target="tier_history"><?= $userList[$selectedPlayer]['tier']['name'] ?></div>
                        </span>
                    <?php endif;?>
                    <select name="player" id="player" onchange="this.form.submit()" class="player-name player-select">
                        <?php foreach($userList as $userId => $userData): ?>
                            <option value="<?= $userId ?>" <?= ($userId == $selectedPlayer) ? 'selected' : '' ?>><?= $userData['last_name'].$userData['first_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ( isset($userList[$selectedPlayer]['badge']) ): ?>
                    <a href="badge?&userId=<?= $selectedPlayer ?>">
                        <div class="badge">
                            <span class="badge-icon">⭐</span> <span class="badge-name"><?=$userList[$selectedPlayer]['badge']['name']?></span>
                        </div>
                    </a>
                <?php endif;?>
            </div>
        <?php else: ?>
            <h1 class="page-title"><?= $title ?></h1>
        <?php endif; ?>
        <form action="" method="get" class="player-selection-form">
            <div class="selection-container">
                <select name="year" id="year" onchange="this.form.submit()" class="year-select">
                    <?php foreach($yearlyStatsList as $year => $displayStatsData): ?>
                        <option value="<?= $year ?>" <?= ($year == $selectedYear) ? 'selected' : '' ?>><?= $year ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <?php if (isset($selectedYear) && isset($selectedPlayer)): ?>
            <?php
                // 選択された選手のデータを取得
                $playerData = null;
                foreach($yearlyStatsList[$selectedYear] as $data) {
                    if ($data['u_user_id'] == $selectedPlayer) {
                        $playerData = $data;
                        break;
                    }
                }
            ?>
            <?php if ($playerData): // データが存在する場合のみ処理?>
                <?php /* 全体成績 */?>
                <div class="table-container">
                    <div class="table-wrapper">
                        <table class="score-table primary-stats">
                            <tbody>
                                <?php foreach($statsColumnAllConfig_1 as $column => $columnName): ?>
                                    <?php if (is_array($columnName)): ?>
                                    <tr>
                                        <th class="stats-column-2"><?= is_array($columnName) ? implode('<br>', array_values($columnName)) : $columnName ?></th>
                                        <td>
                                            <?php
                                                $values = [];
                                                foreach($columnName as $key => $value) {
                                                    $values[] = $playerData[$column][$key];
                                                }
                                                echo implode('<br>', $values);
                                            ?>
                                        </td>
                                    <?php else: ?>
                                        <th class="stats-column-2"><?= $columnName ?></th>
                                        <td>
                                        <?php if (!$scoreDisplayFlag && ($column == 'sum_point' || $column == 'sum_base_score' || $column == 'average_point' || $column == 'average_score')): ?>
                                            ---
                                        <?php else: ?>
                                            <?= $playerData[$column] ?>
                                        <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                    <tr>
                                <?php endforeach; ?>
                            </tbody>
                            <?php /* 全体成績 */?>
                        </table>
                    </div>
                </div>

                <?php /* グラフ */?>
                <div class="charts-container container">
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
                                        parseFloat(playerData.rank_probability[1].replace('%', '')),
                                        parseFloat(playerData.rank_probability[2].replace('%', '')),
                                        parseFloat(playerData.rank_probability[3].replace('%', '')),
                                        parseFloat(playerData.rank_probability[4].replace('%', ''))
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
                                            normalizeData(parseFloat(parseFloat(playerData.rank_probability[1].replace('%', ''))), 20, 30),
                                            normalizeData(parseFloat(parseFloat(playerData.over_second_probability.replace('%', ''))), 42, 58),
                                            normalizeData(parseFloat(parseFloat(playerData.over_third_probability.replace('%', ''))), 62, 82),
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
                                            display: false
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
                        border-radius: 4px;
                        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                        padding: 15px;
                    }

                    .chart-card h3 {
                        text-align: center;
                        color: #009944;
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
                <?php /* グラフ */?>

                <?php /* 各家の成績 */?>
                <div class="table-container container">
                    <div class="table-wrapper">
                        <h2 class="section-title">各家成績</h2>
                        <table class="score-table house-stats">
                            <thead>
                                <tr>
                                    <th></th>
                                    <?php foreach($mDirectionList as $directionName): ?>
                                        <th><?= $directionName ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($statsColumnAllConfig_2 as $column => $columnName): ?>
                                    <tr>
                                        <th class="stats-column-5"><?= is_array($columnName) ? implode('<br>', array_values($columnName)) : $columnName ?></th>
                                        <?php foreach($mDirectionList as $directionId => $directionName): ?>
                                            <?php if (is_array($columnName)): ?>
                                                <td>
                                                    <?php
                                                        $values = [];
                                                        foreach($columnName as $key => $value) {
                                                            $values[] = $playerData[$column][$directionId][$key];
                                                        }
                                                        echo implode('<br>', $values);
                                                    ?>
                                                </td>
                                            <?php else: ?>
                                                <td>
                                                <?php if (!$scoreDisplayFlag && ($column == 'sum_point_direction' || $column == 'sum_base_score_direction' || $column == 'average_point_direction' || $column == 'average_score_direction')): ?>
                                                    ---
                                                <?php else: ?>
                                                    <?= $playerData[$column][$directionId] ?>
                                                <?php endif; ?>
                                                </td>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php /* 各家の成績 */?>

                <?php /* 各家の成績 */?>
                <h2 class="page-title">席による関係性</h2>
                <div class="relation-section">
                    <div class="relation-column">
                        <h3>上家</h3>
                        <?php foreach( $directionStats['upper'][$selectedPlayer] as $userId => $data ): ?>
                            <div class="player-card">
                                <div class="player-name"><?= $userList[$userId]['last_name'] ?></div>
                                <ul class="player-stats">
                                    <?php foreach($data as $key => $value): ?>
                                        <li><?= $displayStatsColumn_2[$key] ?> ： <?= $value ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="relation-column">
                        <h3>下家</h3>
                        <?php foreach( $directionStats['lower'][$selectedPlayer] as $userId => $data ): ?>
                            <div class="player-card">
                                <div class="player-name"><?= $userList[$userId]['last_name'] ?></div>
                                <ul class="player-stats">
                                    <?php foreach($data as $key => $value): ?>
                                        <li><?= $displayStatsColumn_2[$key] ?> ： <?= $value ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php /* 各家の成績 */?>

                <?php /* ランク履歴 */ ?>
                <h2 class="page-title">ランク履歴</h2>
                <div id="tier_history" class="rank-history-container">
                    <?php foreach ($rankHistoryList[$selectedPlayer] as $year => $tierInfo): ?>
                        <div class="rank-history-item">
                            <div class="rank-year"><?= $year ?></div>
                            <div class="rank-tier" style="color:<?= $tierInfo['before']['color'] ?>">
                                <?= $tierInfo['before']['name'] ?>
                            </div>
                            <div>→</div>
                            <div class="rank-tier" style="color:<?= $tierInfo['after']['color'] ?>">
                                <?= $tierInfo['after']['name'] ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php /* ランク履歴 */ ?>

            <?php else: ?>
                <p class="no-data-message">選択された選手のデータが見つかりません。</p>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif;?>
</main>
</body>
</html>

<script>
    // 全ての .scroll-btn にイベントを付与
    document.querySelectorAll('.scroll-btn').forEach(div => {
        div.addEventListener('click', () => {
            const targetId = div.getAttribute('data-target');
            const target = document.getElementById(targetId);
            if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
</script>

<style>
    .profile-header {
        text-align: center;
        padding: 20px 0;
    }
    .profile-header .profile-info {
        margin-bottom: 5px;
    }
    .profile-header .player-name {
        font-size: 1.8em;
        font-weight: bold;
        margin: 0;
    }
    .profile-header .tier {
        display: block;
        font-size: 1em;
        font-weight: 500;
    }
    .profile-header .badge {
        margin-top: 5px;
        font-size: 0.8em;
        color: #6c757d;
    }
    .profile-header .badge-icon {
        margin-right: 5px;
    }
    .profile-header .page-title {
        text-align: center;
        font-size: 1.8em;
    }
    .player-selection-form {
        text-align: center;
        margin-bottom:0px;
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
        border-top-left-radius: 4px;
    }
    .score-table th:last-child {
        border-top-right-radius: 4px;
    }
    .score-table td {
        border-bottom: 1px solid #e0e0e0;
    }
    .stats-colum {
        font-weight: bold;
    }
    .year-select {
        padding: 0px;
        font-size: 1em; /* フォントサイズ */
        border: 1px solid #fff; /* ボーダー */
        text-align: center;          /* 文字を中央揃え */
        text-align-last: center;     /* Firefox 用 */
        -webkit-appearance: none;    /* iOS/Safari のデフォルト矢印を消す */
        -moz-appearance: none;       /* Firefox */
        appearance: none;
    }
    .player-select {
        padding: 10px 0px 5px 0px;
        border: 1px solid #fff; /* ボーダー */
        font-size: 1em; /* フォントサイズ */
        text-align: center;          /* 文字を中央揃え */
        text-align-last: center;     /* Firefox 用 */
        -webkit-appearance: none;    /* iOS/Safari のデフォルト矢印を消す */
        -moz-appearance: none;       /* Firefox */
        appearance: none;
    }
    .year-select:hover, .player-select:hover
    .year-select:focus, .player-select:focus {
        outline: none; /* デフォルトのアウトラインを無効化 */
    }
    .stats-column-2 {
        width: 50%;
        min-width: 90px;
    }
    .stats-column-5 {
        min-width: 90px;
    }
    .relation-section {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin: 0 10px;
    }
    .relation-column {
        flex: 1;
    }
    .player-card {
        padding: 6px;
        margin-bottom: 8px;
    }
    .player-name {
        font-weight: bold;
        font-size: 1.1em;
        margin-bottom: 8px;
    }
    .player-stats {
        list-style: none;
        padding-left: 0;
        margin: 0;
    }
    .player-stats li {
        padding: 4px 0;
        font-size: 0.9em;
        border-bottom: 1px solid #eee;
    }
    /* Rank History */
    .rank-history-container {
        display: grid;
        grid-template-columns: 80px 1fr 50px 1fr;
        border: 1px solid #ccc;
        border-radius: 6px;
        overflow: hidden;
        margin: 16px 0;
        font-size: 1rem;
    }
    .rank-history-item {
        display: contents; /* グリッドの中で要素をセルのように扱う */
    }
    .rank-history-item > div {
        padding: 8px;
        border-bottom: 1px solid #eee;
        text-align: center;
    }
    .rank-history-item:last-child > div {
        border-bottom: none;
    }
    @media screen and (max-width: 768px) {
        .table-wrapper {
            padding: 10px;
        }
        .score-table th,
        .score-table td {
            padding: 5px 5px;
            font-size: 0.85em;
        }
        .score-table th {
            text-align: center; /* 中央揃え */
            line-height: 1.5; /* 行間を調整 */
        }
    }

    @media (orientation: landscape) {
        .score-table th {
            writing-mode: horizontal-tb; /* 横書きに変更 */
        }
    }
</style>

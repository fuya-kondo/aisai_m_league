<?php
/**
 * 全体成績ページビュー。
 * builder が整形した成績表、タイトル履歴、グラフデータを描画する。
 */
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <link rel="apple-touch-icon" href="<?= h($baseUrl) ?>/favicon.png">
    <link rel="icon" href="<?= h($baseUrl) ?>/favicon.ico" sizes="64x64" type="image/x-icon">
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/master.css">
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/bottom_navigation.css">
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/app.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;700&display=swap" rel="stylesheet">
    <title><?= h($title) ?></title>
</head>
<body>
<?php include __DIR__ . '/bottom_navigation.php'; ?>
<main>
    <?php include __DIR__ . '/page_title.php'; ?>

    <div class="stats-term-selector">
        <div class="stats-term-selector__scroll" role="tablist" aria-label="成績期間">
            <?php foreach ($termOptions as $termOption): ?>
                <a
                    class="stats-term-button<?= (string)$termOption['value'] === (string)$selectedTerm ? ' is-active' : '' ?>"
                    href="<?= h($termOption['href']) ?>"
                >
                    <?= h($termOption['label']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="table-container">
        <div class="stats-table-header">
            <div class="play-count">対局数：<?= h((string)($selectedTermStatsTable['playCount'] ?? 0)) ?></div>
        </div>
        <div class="table-wrapper">
            <?php
            $tableColumns = $selectedTermStatsTable['columns'] ?? [];
            $tableRows = $selectedTermStatsTable['rows'] ?? [];
            include __DIR__ . '/partials/stats_table.php';
            ?>
        </div>
    </div>

    <?php if ($scoreDisplayFlag): ?>
        <div class="points-chart-section">
            <canvas id="userPointsChart"></canvas>
        </div>
        <?php renderExternalScriptTags([
            'https://cdn.jsdelivr.net/npm/chart.js',
            'https://cdn.jsdelivr.net/npm/moment',
            'https://cdn.jsdelivr.net/npm/chartjs-adapter-moment',
        ]); ?>
        <script id="stats-chart-data" type="application/json">
            <?= json_encode($statsChartData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
        </script>
    <?php endif; ?>

    <?php if (!empty($titleHistoryItems)): ?>
        <div class="table-container title-history-section">
            <div class="table-wrapper">
                <div class="page-title">タイトル履歴</div>
                <table class="score-table score-table--panel title-history-table">
                    <thead>
                        <tr>
                            <th>タイトル</th>
                            <th>保持者</th>
                            <th>値</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($titleHistoryItems as $item): ?>
                            <tr>
                                <td><?= h($item['title_name']) ?></td>
                                <td><?= h($item['u_user_id']) ?></td>
                                <td><?= h($item['value']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</main>
<?php renderScriptTags($baseUrl, [
    'resources/js/main/stats-charts.js',
]); ?>
</body>
</html>

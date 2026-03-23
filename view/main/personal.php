<?php
/**
 * 個人成績ページビュー。
 * builder で整形済みのプロフィール、統計表、関係性、ランク履歴を 1 ファイル内で描画する。
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
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/pages/main-personal-stats.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;700&display=swap" rel="stylesheet">
    <?php renderExternalScriptTags([
        'https://cdn.jsdelivr.net/npm/chart.js',
        'https://cdn.jsdelivr.net/npm/moment',
        'https://cdn.jsdelivr.net/npm/chartjs-adapter-moment',
    ]); ?>
    <title><?= h($title) ?></title>
</head>
<body>
<?php include __DIR__ . '/bottom_navigation.php'; ?>
<main>
    <?php include __DIR__ . '/page_title.php'; ?>
    <?php if ($selectedPlayer === null): ?>
        <div class="stats-term-selector personal-selector-group">
            <div class="stats-term-selector__scroll" role="tablist" aria-label="選手">
                <?php foreach ($playerOptions as $option): ?>
                    <a class="stats-term-button personal-selector-button" href="<?= h($option['href']) ?>"><?= h($option['label']) ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="stats-term-selector personal-selector-group">
            <div class="stats-term-selector__scroll" role="tablist" aria-label="期間">
                <?php foreach ($termOptions as $termOption): ?>
                    <a class="stats-term-button<?= !empty($termOption['active']) ? ' is-active' : '' ?>" href="<?= h($termOption['href']) ?>">
                        <?= h($termOption['label']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="stats-term-selector personal-selector-group">
            <div class="stats-term-selector__scroll" role="tablist" aria-label="選手">
                <?php foreach ($playerOptions as $option): ?>
                    <a class="stats-term-button personal-selector-button<?= !empty($option['active']) ? ' is-active' : '' ?>" href="<?= h($option['href']) ?>">
                        <?= h($option['label']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="profile-header">
            <?php if (!empty($playerProfile['avatarUrl'])): ?>
                <div
                    class="profile-avatar-wrap"
                    style="width:88px;height:88px;margin:0 auto 12px;overflow:hidden;border-radius:50%;"
                >
                    <img
                        class="profile-avatar"
                        src="<?= h($playerProfile['avatarUrl']) ?>"
                        alt="<?= h(($playerProfile['displayName'] ?? '選手') . 'のアバター') ?>"
                        width="88"
                        height="88"
                        loading="lazy"
                        style="display:block;width:100%;height:100%;border-radius:50%;object-fit:cover;object-position:center;"
                    >
                </div>
            <?php endif; ?>
            <div class="profile-meta-line">
                <?php if (!empty($playerProfile['tierName'])): ?>
                    <div class="tier dynamic-color-text" data-color="<?= h($playerProfile['tierColor']) ?>">
                        <span class="scroll-btn" data-target="<?= h($playerProfile['tierTargetId']) ?>"><?= h($playerProfile['tierName']) ?>ランク</span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($playerProfile['badgeName'])): ?>
                    <a href="<?= h($playerProfile['badgeUrl']) ?>" class="badge-link">
                        <div class="badge">
                            <span class="badge-label">称号</span>
                            <span class="badge-icon">⭐</span>
                            <span class="badge-name"><?= h($playerProfile['badgeName']) ?></span>
                        </div>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($playerStatsExists): ?>
            <?php
            $hasPersonalPointChart = $scoreDisplayFlag && !empty($personalPointChartData['datasets']);
            ?>
            <div class="table-container">
                <div class="table-wrapper">
                    <table class="score-table score-table--panel primary-stats">
                        <tbody>
                            <?php foreach ($primaryStatsRows as $row): ?>
                                <tr>
                                    <th class="stats-column-2"><?= implode('<br>', array_map('h', $row['labelLines'])) ?></th>
                                    <td><?= implode('<br>', array_map('h', $row['valueLines'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="charts-container container">
                <?php if ($hasPersonalPointChart): ?>
                    <div class="chart-card chart-card--point-trend">
                        <h3>ポイント推移</h3>
                        <div class="chart-wrapper chart-wrapper--point-trend">
                            <canvas id="userPointsChart" data-hide-legend="1"></canvas>
                        </div>
                    </div>
                <?php endif; ?>
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

            <script id="personal-stats-chart-data" type="application/json">
                <?= json_encode($personalStatsChartData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
            </script>
            <script id="stats-chart-data" type="application/json">
                <?= json_encode($personalPointChartData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
            </script>

            <div class="table-container container">
                <div class="table-wrapper">
                    <h2 class="section-title">各家成績</h2>
                    <table class="score-table score-table--panel house-stats">
                        <thead>
                            <tr>
                                <th></th>
                                <?php foreach ($directionHeaders as $header): ?>
                                    <th><?= h($header['label']) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($directionStatsRows as $row): ?>
                                <tr>
                                    <th class="stats-column-5"><?= implode('<br>', array_map('h', $row['labelLines'])) ?></th>
                                    <?php foreach ($directionHeaders as $header): ?>
                                        <td><?= implode('<br>', array_map('h', $row['valuesByDirection'][$header['directionId']] ?? [''])) ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <h2 class="page-title">席による関係性</h2>
            <div class="relation-section">
                <?php foreach ($relationColumns as $column): ?>
                    <div class="relation-column">
                        <h3><?= h($column['label']) ?></h3>
                        <?php if (!empty($column['cards'])): ?>
                            <?php foreach ($column['cards'] as $card): ?>
                                <div class="player-card">
                                    <div class="player-name"><?= h($card['playerName']) ?></div>
                                    <ul class="player-stats">
                                        <?php foreach ($card['stats'] as $stat): ?>
                                            <li><?= h($stat['label']) ?> ： <?= h($stat['value']) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-data-message"><?= h($column['emptyMessage']) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <h2 class="page-title">ランク履歴</h2>
            <div id="tier_history" class="rank-history-container">
                <?php if (!empty($rankHistoryItems)): ?>
                    <?php foreach ($rankHistoryItems as $item): ?>
                        <div class="rank-history-item">
                            <div class="rank-year"><?= h($item['year']) ?></div>
                            <div class="rank-tier dynamic-color-text" data-color="<?= h($item['beforeColor']) ?>">
                                <?= h($item['beforeName']) ?>
                            </div>
                            <div>→</div>
                            <div class="rank-tier dynamic-color-text" data-color="<?= h($item['afterColor']) ?>">
                                <?= h($item['afterName']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-data-message">ランク履歴データがありません。</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p class="no-data-message">選択された選手のデータが見つかりません。</p>
        <?php endif; ?>
    <?php endif; ?>
</main>
<?php renderScriptTags($baseUrl, [
    'resources/js/main/auto-submit.js',
    'resources/js/main/dynamic-colors.js',
    'resources/js/main/stats-charts.js?v=2',
    'resources/js/main/personal-stats.js',
]); ?>
</body>
</html>

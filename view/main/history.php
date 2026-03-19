<?php
/**
 * 履歴ページビュー。
 * builder が作成した全体履歴または個人履歴を 1 ファイル内で描画する。
 */
$renderPagination = static function (array $pagination, ?string $paginationInfoText, string $containerClass = 'pagination'): void {
    if (!empty($pagination['links'])) {
        echo '<div class="' . h($containerClass) . '"><div class="pagination-controls">';
        foreach ($pagination['links'] as $link) {
            echo '<a href="' . h($link['href']) . '" class="page-link ' . ($link['active'] ? 'active' : '') . '">' . h($link['label']) . '</a>';
        }
        echo '</div>';
        if (!empty($paginationInfoText)) {
            echo '<div class="page-info">' . h($paginationInfoText) . '</div>';
        }
        echo '</div>';
        return;
    }

    if (!empty($paginationInfoText)) {
        echo '<div class="' . h($containerClass) . '"><div class="page-info">' . h($paginationInfoText) . '</div></div>';
    }
};
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
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/pages/main-history.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;700&display=swap" rel="stylesheet">
    <title><?= h($title) ?></title>
</head>
<body>
<?php include __DIR__ . '/bottom_navigation.php'; ?>
<main>
    <?php include __DIR__ . '/page_title.php'; ?>
    <?php if (!empty($errorMessage)): ?>
        <div class="error-message"><?= h($errorMessage) ?></div>
    <?php endif; ?>

    <?php if (($selectedView ?? 'overview') !== 'personal'): ?>
        <?php $renderPagination($overviewPagination, $overviewInfoText, 'pagination-nav'); ?>

        <div class="game-history-container container">
            <?php if (!empty($overviewDateGroups)): ?>
                <?php foreach ($overviewDateGroups as $dateGroup): ?>
                    <div class="game-date">
                        <h4 class="date-header"><?= h($dateGroup['date']) ?></h4>
                        <div class="day-stats">
                            <?php foreach ($dateGroup['dayStats'] as $dayStat): ?>
                                <div class="day-stats-item">
                                    <div class="day-stats-user"><?= h($dayStat['userName']) ?></div>
                                    <div class="day-stats-value"><?= h($dayStat['value']) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php foreach ($dateGroup['games'] as $gameSession): ?>
                            <div class="game-session">
                                <div class="game-session-header">
                                    <h5 class="game-header"><?= h($gameSession['gameNumber']) ?>半荘目</h5>
                                    <?php if (!empty($gameSession['canBatchOperate'])): ?>
                                        <div class="game-session-actions">
                                            <form action="bulk-update" method="get" class="inline-form game-session-inline-form">
                                                <input type="hidden" name="tableId" value="<?= h((string)$gameSession['tableId']) ?>">
                                                <input type="hidden" name="playDate" value="<?= h($gameSession['playDate']) ?>">
                                                <input type="hidden" name="game" value="<?= h((string)$gameSession['gameNumber']) ?>">
                                                <?php foreach ($gameSession['historyIds'] as $historyId): ?>
                                                    <input type="hidden" name="historyIds[]" value="<?= h((string)$historyId) ?>">
                                                <?php endforeach; ?>
                                                <button type="submit" class="action-button edit-button">一括修正</button>
                                            </form>
                                            <form
                                                action="history"
                                                method="post"
                                                class="inline-form game-session-inline-form"
                                                data-history-bulk-delete-form
                                                data-play-date="<?= h($gameSession['playDate']) ?>"
                                                data-game-number="<?= h((string)$gameSession['gameNumber']) ?>"
                                            >
                                                <input type="hidden" name="bulkDelete" value="1">
                                                <input type="hidden" name="tableId" value="<?= h((string)$gameSession['tableId']) ?>">
                                                <input type="hidden" name="playDate" value="<?= h($gameSession['playDate']) ?>">
                                                <input type="hidden" name="game" value="<?= h((string)$gameSession['gameNumber']) ?>">
                                                <button type="submit" class="action-button delete-button">一括削除</button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <table class="result-table">
                                    <tbody>
                                        <?php foreach ($gameSession['rows'] as $row): ?>
                                            <tr class="player-row <?= h($row['rankClass']) ?>">
                                                <td class="direction-cell"><?= h($row['directionName']) ?></td>
                                                <td class="rank-cell"><?= h($row['rank']) ?></td>
                                                <td class="name-cell"><?= h($row['playerName']) ?></td>
                                                <td class="score-cell"><?= h($row['score']) ?></td>
                                                <td class="point-cell"><?= h($row['point']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>

                                <?php if (!empty($gameSession['warnings'])): ?>
                                    <div class="history-warning-list">
                                        <?php foreach ($gameSession['warnings'] as $warning): ?>
                                            <span class="history-warning"><?= h($warning) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-data-message">対局履歴はありません。</p>
            <?php endif; ?>
        </div>

        <?php $renderPagination($overviewPagination, $overviewInfoText, 'pagination-nav'); ?>
    <?php else: ?>
        <?php if (!empty($userButtons)): ?>
            <div class="stats-term-selector history-user-selector">
                <div class="stats-term-selector__scroll" role="tablist" aria-label="履歴選手">
                    <?php foreach ($userButtons as $button): ?>
                        <a
                            class="stats-term-button<?= (string)$button['userId'] === (string)$selectedUser ? ' is-active' : '' ?>"
                            href="<?= h('history?' . http_build_query([
                                'view' => 'personal',
                                'year' => $selectedYear,
                                'userId' => $button['userId'],
                            ])) ?>"
                        >
                            <?= h($button['label']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($selectedUser !== null): ?>
        <?php $renderPagination($selectedUserPagination, $selectedUserInfoText, 'pagination'); ?>

        <div class="table-container container">
            <div class="table-wrapper">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>順位</th><th>点数</th><th>Pts</th><th>日時</th><th>半荘数</th><th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($selectedUserRows)): ?>
                            <?php foreach ($selectedUserRows as $row): ?>
                                <tr>
                                    <td class="<?= h($row['rankClass']) ?>"><?= h($row['rankDisplay']) ?></td>
                                    <td class="<?= h($row['scoreClass']) ?>"><?= h($row['score']) ?></td>
                                    <td class="<?= h($row['pointClass']) ?>"><?= h($row['point']) ?></td>
                                    <td><?= h($row['playDate']) ?></td>
                                    <td><?= h($row['game']) ?></td>
                                    <td>
                                        <form action="update" method="post" class="inline-form">
                                            <input type="hidden" name="userId" value="<?= h($row['userId']) ?>">
                                            <input type="hidden" name="rank" value="<?= h($row['rank']) ?>">
                                            <input type="hidden" name="score" value="<?= h($row['score']) ?>">
                                            <input type="hidden" name="game" value="<?= h($row['game']) ?>">
                                            <input type="hidden" name="direction" value="<?= h($row['direction']) ?>">
                                            <button type="submit" name="historyId" value="<?= h($row['historyId']) ?>" class="action-button edit-button">修正</button>
                                        </form>
                                        <form action="history" method="post" class="inline-form" data-history-delete-form data-rank="<?= h($row['rankDisplay']) ?>" data-score="<?= h($row['score']) ?>" data-point="<?= h($row['point']) ?>">
                                            <input type="hidden" name="userId" value="<?= h($row['userId']) ?>">
                                            <button type="submit" name="historyId" value="<?= h($row['historyId']) ?>" class="action-button delete-button">削除</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">該当する履歴はありません。</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php $renderPagination($selectedUserPagination, $selectedUserInfoText, 'pagination'); ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</main>
<script src="<?= h($baseUrl) ?>/resources/js/main/history.js"></script>
</body>
</html>

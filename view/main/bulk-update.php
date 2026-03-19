<?php
/**
 * 一括修正ページビュー。
 * 1半荘分の4席データをまとめて編集し、合計点や自動順位をクライアント側でも補助表示する。
 */
$initialScoreTotal = 0;
for ($direction = 1; $direction <= 4; $direction++) {
    $seatScore = (string)($formData['seats'][$direction]['score'] ?? '');
    if ($seatScore !== '' && preg_match('/^-?\d+$/', $seatScore)) {
        $initialScoreTotal += (int)$seatScore * 100;
    }
}
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
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/pages/main-bulk-add.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;700&display=swap" rel="stylesheet">
    <title><?= h($title) ?></title>
</head>
<body>
<?php include __DIR__ . '/bottom_navigation.php'; ?>
<main>
    <?php include __DIR__ . '/page_title.php'; ?>

    <?php if (!empty($errorMessages)): ?>
        <div class="error-box">
            <ul>
                <?php foreach ($errorMessages as $message): ?>
                    <li><?= h($message) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="form-container container">
        <form id="bulkUpdateForm" action="bulk-update" method="post" class="registration-form">
            <input type="hidden" name="tableId" value="<?= h($formData['table_id']) ?>">
            <input type="hidden" name="playDate" value="<?= h($formData['play_date']) ?>">
            <input type="hidden" name="timePart" value="<?= h($formData['time_part']) ?>">

            <?php for ($direction = 1; $direction <= 4; $direction++): ?>
                <input type="hidden" name="historyId_<?= h($direction) ?>" value="<?= h((string)($formData['seats'][$direction]['history_id'] ?? 0)) ?>">
                <input type="hidden" name="historyIds[]" value="<?= h((string)($formData['seats'][$direction]['history_id'] ?? 0)) ?>">
            <?php endfor; ?>

            <div class="form-row meta-row">
                <div class="form-group game-group">
                    <input id="game" class="input" type="number" name="game" min="1" required value="<?= h($formData['game']) ?>">
                    <label for="game">半荘目</label>
                </div>
            </div>

            <div
                class="score-total"
                id="scoreTotal"
                data-score-total-units="<?= h((string)$scoreTotalUnits) ?>"
                data-score-total="<?= h((string)$scoreTotal) ?>"
            >合計： <?= h(number_format($initialScoreTotal)) ?> 点</div>

            <div class="compact-table-wrap">
                <table class="compact-table">
                    <caption class="compact-caption">上から順に: 選手 / 点数(100点単位) / チョンボ</caption>
                    <thead>
                        <tr>
                            <?php for ($direction = 1; $direction <= 4; $direction++): ?>
                                <th><?= h($directionLabels[$direction]) ?></th>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php for ($direction = 1; $direction <= 4; $direction++): ?>
                                <?php $seat = $formData['seats'][$direction] ?? ['user_id' => '', 'rank' => '', 'score' => '', 'mistake_count' => 0]; ?>
                                <td>
                                    <select id="userId_<?= h($direction) ?>" class="input" name="userId_<?= h($direction) ?>" required>
                                        <option value="">-</option>
                                        <?php foreach ($userList as $userId => $userData): ?>
                                            <option value="<?= h($userId) ?>" <?= (string)$seat['user_id'] === (string)$userId ? 'selected' : '' ?>>
                                                <?= h($userData['last_name'] . $userData['first_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            <?php endfor; ?>
                        </tr>
                        <tr>
                            <?php for ($direction = 1; $direction <= 4; $direction++): ?>
                                <?php $seat = $formData['seats'][$direction] ?? ['user_id' => '', 'rank' => '', 'score' => '', 'mistake_count' => 0]; ?>
                                <td>
                                    <div class="score-input-wrap">
                                        <input
                                            id="score_<?= h($direction) ?>"
                                            class="input score-input"
                                            type="text"
                                            name="score_<?= h($direction) ?>"
                                            inputmode="numeric"
                                            pattern="-?[0-9]+"
                                            placeholder="250"
                                            required
                                            value="<?= h($seat['score']) ?>"
                                        >
                                        <span class="score-suffix">00</span>
                                    </div>
                                </td>
                            <?php endfor; ?>
                        </tr>
                        <tr>
                            <?php for ($direction = 1; $direction <= 4; $direction++): ?>
                                <?php $seat = $formData['seats'][$direction] ?? ['user_id' => '', 'rank' => '', 'score' => '', 'mistake_count' => 0]; ?>
                                <td>
                                    <input
                                        id="mistake_count_<?= h($direction) ?>"
                                        class="input"
                                        type="number"
                                        name="mistake_count_<?= h($direction) ?>"
                                        min="0"
                                        max="99"
                                        value="<?= h($seat['mistake_count']) ?>"
                                    >
                                </td>
                            <?php endfor; ?>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="form-group date-group date-group-bottom">
                <label>対局日</label>
                <div class="date-inputs">
                    <?php $currentYear = (int)date('Y'); ?>
                    <select id="year" class="input play_date year" name="year" required>
                        <?php for ($year = $currentYear - 1; $year <= $currentYear + 1; $year++): ?>
                            <option value="<?= h($year) ?>" <?= (int)$formData['year'] === $year ? 'selected' : '' ?>><?= h($year) ?></option>
                        <?php endfor; ?>
                    </select>
                    <span>年</span>
                    <select id="month" class="input play_date month" name="month" required>
                        <?php for ($month = 1; $month <= 12; $month++): ?>
                            <option value="<?= h($month) ?>" <?= (int)$formData['month'] === $month ? 'selected' : '' ?>><?= h($month) ?></option>
                        <?php endfor; ?>
                    </select>
                    <span>月</span>
                    <select id="day" class="input play_date day" name="day" required>
                        <?php for ($day = 1; $day <= 31; $day++): ?>
                            <option value="<?= h($day) ?>" <?= (int)$formData['day'] === $day ? 'selected' : '' ?>><?= h($day) ?></option>
                        <?php endfor; ?>
                    </select>
                    <span>日</span>
                </div>
            </div>
        </form>
    </div>

    <button class="submit-button btn-primary" type="submit" form="bulkUpdateForm">一括修正する</button>
</main>

<?php renderScriptTags($baseUrl, [
    'resources/js/main/form-common.js',
    'resources/js/main/form-bulk-add.js',
]); ?>
</body>
</html>

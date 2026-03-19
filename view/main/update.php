<?php
/**
 * 履歴修正ページビュー。
 * 個別登録と同じ UI を使って、対局履歴 1 件分を編集する。
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
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/pages/main-add.css">
    <title><?= h($title) ?></title>
</head>
<body>
<?php include __DIR__ . '/bottom_navigation.php'; ?>
<main>
    <?php include __DIR__ . '/page_title.php'; ?>

    <?php if (!empty($error_msg)): ?>
        <div class="error-message"><?= h($error_msg) ?></div>
    <?php endif; ?>

    <?php if (!empty($historyId)): ?>
        <div class="form-container container">
            <form id="updateForm" action="update" method="post" class="registration-form">
                <input type="hidden" name="historyId" value="<?= h((string)$historyId) ?>">
                <input type="hidden" name="userId" value="<?= h($formData['userId']) ?>">
                <input type="hidden" id="direction" name="direction" value="<?= h($formData['direction']) ?>" required>

                <div class="form-group player">
                    <div class="input input--readonly">
                        <?= h(($userList[$formData['userId']]['last_name'] ?? '') . ($userList[$formData['userId']]['first_name'] ?? '')) ?>
                    </div>
                    <label>選手</label>
                </div>
                <div class="form-group form-group--hidden">
                    <label for="tableId">卓</label>
                    <input id="tableId" class="input table-id-input" type="number" name="tableId" required value="<?= h($formData['tableId']) ?>">
                </div>
                <div class="form-group game">
                    <div class="date-inputs game">
                        <input id="game" class="input" type="number" name="game" required value="<?= h($formData['game']) ?>">
                    </div>
                    <label for="game">半荘目</label>
                </div>
                <div class="form-group direction">
                    <div class="button-container">
                        <?php foreach ($mDirectionList as $directionId => $directionData): ?>
                            <button class="direction-button<?= (string)$formData['direction'] === (string)$directionId ? ' selected' : '' ?>" type="button" name="direction_button" value="<?= h($directionId) ?>" data-direction-button><?= h($directionData['name']) ?></button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-group rank">
                    <select id="rank" class="input" name="rank" required>
                        <option value="">-</option>
                        <?php foreach ($rankConfig as $value => $name): ?>
                            <option value="<?= h($value) ?>" <?= (string)$formData['rank'] === (string)$value ? 'selected' : '' ?>><?= h($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="rank">位</label>
                </div>
                <div class="form-group score">
                    <input id="score" class="input" type="text" name="score" required inputmode="text" placeholder="例:25300" pattern="-?[0-9]*\.?[0-9]*" value="<?= h($formData['score']) ?>">
                    <label for="score">点</label>
                </div>
                <div class="form-group mistake">
                    <input id="mistakeCount" class="input" type="number" name="mistake_count" min="0" max="99" value="<?= h($formData['mistakeCount']) ?>" inputmode="numeric">
                    <label for="mistakeCount">ﾁｮﾝﾎﾞ</label>
                </div>
                <div class="form-group date-group">
                    <div class="date-inputs">
                        <?php $currentYear = (int)date('Y'); ?>
                        <select class="input play_date year" name="year" required>
                            <?php for ($year = $currentYear - 1; $year <= $currentYear + 1; $year++): ?>
                                <option value="<?= h($year) ?>" <?= (int)$formData['year'] === $year ? 'selected' : '' ?>><?= h($year) ?></option>
                            <?php endfor; ?>
                        </select>
                        <span>年</span>
                        <select class="input play_date month" name="month" required>
                            <?php for ($month = 1; $month <= 12; $month++): ?>
                                <option value="<?= h($month) ?>" <?= (int)$formData['month'] === $month ? 'selected' : '' ?>><?= h($month) ?></option>
                            <?php endfor; ?>
                        </select>
                        <span>月</span>
                        <select class="input play_date day" name="day" required>
                            <?php for ($day = 1; $day <= 31; $day++): ?>
                                <option value="<?= h($day) ?>" <?= (int)$formData['day'] === $day ? 'selected' : '' ?>><?= h($day) ?></option>
                            <?php endfor; ?>
                        </select>
                        <span>日</span>
                    </div>
                </div>

            </form>
        </div>
        <button class="submit-button btn-primary" type="submit" form="updateForm">修正する</button>
    <?php endif; ?>
</main>

<?php renderScriptTags($baseUrl, [
    'resources/js/main/form-common.js',
    'resources/js/main/form-update.js',
]); ?>
</body>
</html>

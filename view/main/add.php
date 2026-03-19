<?php
/**
 * 単独成績登録ページビュー。
 * 1人分の結果を選手・席・順位・点数単位で入力するフォームを表示する。
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
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;700&display=swap" rel="stylesheet">
    <title><?= h($title) ?></title>
</head>
<body>
<?php include __DIR__ . '/bottom_navigation.php'; ?>
<main>
    <?php include __DIR__ . '/page_title.php'; ?>
    <div class="form-container container">
        <form id="addForm" action="add" method="post" class="registration-form" data-games='<?= h(json_encode($games, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT)) ?>'>
            <div class="form-group player">
                <select id="userId" class="input" name="userId" required>
                    <option value="">-</option>
                    <?php foreach ($userList as $userId => $userData): ?>
                        <option value="<?= h($userId) ?>"><?= h($userData['last_name'] . $userData['first_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="userId">選手</label>
            </div>
            <div class="form-group form-group--hidden">
                <label for="tableId">卓</label>
                <input id="tableId" class="input table-id-input" type="number" name="tableId" required value="1">
            </div>
            <div class="form-group game">
                <div class="date-inputs game">
                    <input id="game" class="input" type="number" name="game" required value="0">
                </div>
                <label for="game">半荘目</label>
            </div>
            <div class="form-group direction">
                <div class="button-container">
                    <?php foreach ($mDirectionList as $directionId => $directionData): ?>
                        <button class="direction-button" type="button" name="direction" value="<?= h($directionId) ?>" data-direction-button><?= h($directionData['name']) ?></button>
                    <?php endforeach; ?>
                    <input type="hidden" id="direction" name="direction" value="" required>
                </div>
            </div>
            <div class="form-group rank">
                <select id="rank" class="input" name="rank" required>
                    <option value="" selected>-</option>
                    <?php foreach ($rankConfig as $value => $name): ?>
                        <option value="<?= h($value) ?>"><?= h($name) ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="rank">位</label>
            </div>
            <div class="form-group score">
                <input id="score" class="input" type="text" name="score" required inputmode="text" placeholder="例:25300" pattern="-?[0-9]*\.?[0-9]*">
                <label for="score">点</label>
            </div>
            <div class="form-group mistake">
                <input id="mistakeCount" class="input" type="number" name="mistake_count" min="0" max="99" value="0" inputmode="numeric">
                <label for="mistakeCount">ﾁｮﾝﾎﾞ</label>
            </div>
            <div class="form-group date-group">
                <div class="date-inputs">
                    <select class="input play_date year" name="year" required>
                        <?php $currentYear = (int)date('Y'); ?>
                        <?php for ($year = $currentYear - 1; $year <= $currentYear + 1; $year++): ?>
                            <option value="<?= h($year) ?>" <?= $year == $currentYear ? 'selected' : '' ?>><?= h($year) ?></option>
                        <?php endfor; ?>
                    </select>
                    <span>年</span>
                    <select class="input play_date month" name="month" required>
                        <?php for ($month = 1; $month <= 12; $month++): ?>
                            <option value="<?= h($month) ?>" <?= $month == date('n') ? 'selected' : '' ?>><?= h($month) ?></option>
                        <?php endfor; ?>
                    </select>
                    <span>月</span>
                    <select class="input play_date day" name="day" required>
                        <?php for ($day = 1; $day <= 31; $day++): ?>
                            <option value="<?= h($day) ?>" <?= $day == date('j') ? 'selected' : '' ?>><?= h($day) ?></option>
                        <?php endfor; ?>
                    </select>
                    <span>日</span>
                </div>
            </div>

        </form>
    </div>
    <button class="submit-button btn-primary" type="submit" form="addForm">登録する</button>
</main>

<?php renderScriptTags($baseUrl, [
    'resources/js/main/form-common.js',
    'resources/js/main/form-add.js',
]); ?>
</body>
</html>

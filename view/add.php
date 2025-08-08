<?php
// Include necessary files
require_once __DIR__ . '/../config/import_file.php';
// Include header
include '../webroot/common/header.php';

// Handling POST requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $require_oncedFields = ['userId', 'tableId', 'game', 'direction', 'rank', 'score', 'year', 'month', 'day'];
    $missingFields = array_filter($require_oncedFields, fn($field) => !isset($_POST[$field]));
    if (empty($missingFields)) {
        $userId    = $_POST['userId'];
        $tableId   = $_POST['tableId'];
        $game      = $_POST['game'];
        $direction = $_POST['direction'];
        $rank      = $_POST['rank'];
        $score     = $_POST['score'];
        $playDate  = sprintf(
            '%04d-%02d-%02d %s',
            $_POST['year'],
            $_POST['month'],
            $_POST['day'],
            date('H:i:s')
        );
        addData($userId, $tableId, $game, $direction, $rank, $score, $playDate);
        header("Location: history.php?userId=" . urlencode($userId));
        exit();
    } else {
        $error_msg = '登録に失敗しました。以下のフィールドが不足しています: ' . implode(', ', $missingFields);
    }
}

// Get play count
$games = array_column($todayStatsList, 'play_count', 'user_id');

// Set title
$title = '登録';
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
    <title><?= $title ?></title>
</head>
<body>
<main>
    <div class="page-title"><?= $title ?></div>
    <div class="form-container container">
        <form action="add" method="post" class="registration-form">
            <div class="form-group">
                <label for="userId">ユーザー</label>

                <select id="userId" class="input" name="userId" require_onced>
                    <option value="">-</option>
                    <?php foreach($userList as $userId => $userData): ?>
                        <option value="<?=$userId?>"><?=$userData['last_name'].$userData['first_name']?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="form-group" style="display:none;">
                <label for="tableId">卓</label>
                <input id="tableId" class="input" type="number" name="tableId" require_onced value="1" style="width: 70%;">
            </div>
            <div class="form-group game">
                <label for="game">半荘目</label>
                <div class="date-inputs">
                    <input id="game" class="input" type="number" name="game" require_onced value="0">
                </div>
            </div>
            <div class="form-group">
                <label for="direction">自家</label>
                <div class="button-container">
                    <?php foreach($mDirectionList as $directionId => $directionName): ?>
                        <button class="direction-button" type="button" name="direction" value="<?=$directionId?>" require_onced onclick="selectButton(this)"><?=$directionName?></button>
                    <?php endforeach;?>
                    <input type="hidden" id="direction" name="direction" value="">
                </div>
            </div>
            <div class="form-group">
                <label for="rank">順位</label>
                <select id="rank" class="input" name="rank" require_onced>
                    <option value="" selected>-</option>
                    <?php foreach($rankConfig as $value => $name): ?>
                        <option value="<?=$value?>"><?=$name?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="form-group">
                <label for="score">点数</label>
                <input id="score" class="input" type="tel" name="score" require_onced inputmode="numeric" pattern="[0-9]*" placeholder="例: 25300">
            </div>
            <div class="form-group date-group">
                <label>日付</label>
                <div class="date-inputs">
                    <select class="input play_date year" name="year" require_onced>
                        <?php for ($year = 2025; $year <= 2027; $year++): ?>
                            <option value="<?= $year ?>" <?= $year == 2025 ? 'selected' : '' ?>><?= $year ?></option>
                        <?php endfor; ?>
                    </select>
                    <span>年</span>
                    <select class="input play_date month" name="month" require_onced>
                        <?php for ($month = 1; $month <= 12; $month++): ?>
                            <option value="<?= $month ?>" <?= $month == date('n') ? 'selected' : '' ?>><?= $month ?></option>
                        <?php endfor; ?>
                    </select>
                    <span>月</span>
                    <select class="input play_date day" name="day" require_onced>
                        <?php for ($day = 1; $day <= 31; $day++): ?>
                            <option value="<?= $day ?>" <?= $day == date('j') ? 'selected' : '' ?>><?= $day ?></option>
                        <?php endfor; ?>
                    </select>
                    <span>日</span>
                </div>
            </div>

            <button class="submit-button btn-primary" type="submit">登録する</button>
        </form>
    </div>
</main>
</body>
</html>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.direction-button');
        buttons.forEach(btn => btn.classList.remove('selected'));
        document.getElementById('direction').value = '';
    });

    let userId  = document.querySelector('[name="userId"]');
    let game    = document.querySelector('[name="game"]');
    let games   = <?=json_encode($games);?>;
    userId.onchange = event => {
        const selectedUserId    = userId.value;
        const currentGameValue  = games[selectedUserId] !== undefined ? games[selectedUserId] : 0;
        game.value = currentGameValue + 1;
    };

    function selectButton(button) {
        const buttons = document.querySelectorAll('.direction-button');
        buttons.forEach(btn => btn.classList.remove('selected'));
        button.classList.add('selected');
        document.getElementById('direction').value = button.value;
    }
</script>

<style>
    .play_date.year { width: 100px; }
    .play_date.month { width: 80px; }
    .play_date.day { width: 80px; }
</style>
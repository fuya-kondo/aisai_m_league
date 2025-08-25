<?php

// Include header
include __DIR__ . '/../header.php';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <link rel="apple-touch-icon" href="<?= $baseUrl ?>/favicon.png">
    <link rel="icon" href="<?= $baseUrl ?>/favicon.ico" sizes="64x64" type="image/x-icon">
    <link rel="stylesheet" href="<?= $baseUrl ?>/resources/css/master.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>/resources/css/header.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>/resources/css/app.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;700&display=swap" rel="stylesheet">
    <title><?= $title ?></title>
</head>
<body>
<main>
    <div class="page-title"><?= $title ?></div>
    <div class="form-container container">
        <form action="add" method="post" class="registration-form" onsubmit="return validateForm()">
            <div class="form-group player">
                <select id="userId" class="input" name="userId" required>
                    <option value="">-</option>
                    <?php foreach($userList as $userId => $userData): ?>
                        <option value="<?=$userId?>"><?=$userData['last_name'].$userData['first_name']?></option>
                    <?php endforeach;?>
                </select>
                <label for="userId">選手</label>
            </div>
            <div class="form-group" style="display:none;">
                <label for="tableId">卓</label>
                <input id="tableId" class="input" type="number" name="tableId" required value="1" style="width: 70%;">
            </div>
            <div class="form-group game">
                <div class="date-inputs game">
                    <input id="game" class="input" type="number" name="game" required value="0">
                </div>
                <label for="game">半荘目</label>
            </div>
            <div class="form-group direction">
                <div class="button-container">
                    <?php foreach($mDirectionList as $directionId => $directionData): ?>
                        <button class="direction-button" type="button" name="direction" value="<?=$directionId?>" onclick="selectButton(this)"><?=$directionData['name']?></button>
                    <?php endforeach;?>
                    <input type="hidden" id="direction" name="direction" value="" required>
                </div>
            </div>
            <div class="form-group rank">
                <select id="rank" class="input" name="rank" required>
                    <option value="" selected>-</option>
                    <?php foreach($rankConfig as $value => $name): ?>
                        <option value="<?=$value?>"><?=$name?></option>
                        <?php endforeach;?>
                </select>
                <label for="rank">位</label>
            </div>
            <div class="form-group score">
                <input id="score" class="input" type="text" name="score" required inputmode="text" placeholder="例:25300" pattern="-?[0-9]*\.?[0-9]*">
                <label for="score">点</label>
            </div>
            <div class="form-group date-group">
                <div class="date-inputs">
                    <select class="input play_date year" name="year" required>
                        <?php for ($year = 2025; $year <= 2027; $year++): ?>
                            <option value="<?= $year ?>" <?= $year == 2025 ? 'selected' : '' ?>><?= $year ?></option>
                        <?php endfor; ?>
                    </select>
                    <span>年</span>
                    <select class="input play_date month" name="month" required>
                        <?php for ($month = 1; $month <= 12; $month++): ?>
                            <option value="<?= $month ?>" <?= $month == date('n') ? 'selected' : '' ?>><?= $month ?></option>
                        <?php endfor; ?>
                    </select>
                    <span>月</span>
                    <select class="input play_date day" name="day" required>
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

   function validateForm() {
        const directionInput = document.getElementById('direction');
        if (directionInput.value === '') {
            alert('席を選択してください。'); // エラーメッセージを表示
            return false; // フォーム送信をキャンセル
        }
        
        // スコアの数値検証
        const scoreInput = document.getElementById('score');
        const scoreValue = scoreInput.value.trim();
        if (scoreValue !== '' && !/^-?\d*\.?\d*$/.test(scoreValue)) {
            alert('スコアには数値のみ入力してください。');
            scoreInput.focus();
            return false;
        }
        
        return true; // フォームを送信
    }

    function selectButton(button) {
        const buttons = document.querySelectorAll('.direction-button');
        buttons.forEach(btn => btn.classList.remove('selected'));
        button.classList.add('selected');
        document.getElementById('direction').value = button.value;
    }
</script>

<style>
    .form-container.container {
        text-align: right;
    }
    .play_date.year { width: 100px; }
    .play_date.month { width: 80px; }
    .play_date.day { width: 80px; }
    .form-group.game, .form-group.rank, .form-group.score, .form-group.player, .form-group.direction {
        text-align: right;
        display: grid;
        gap: 0px;
        justify-content: end;
    }
    .form-group {
        margin: 10px 0;
    }
    .form-group.game, .form-group.player,.form-group.rank, .form-group.score {
        grid-template-columns: 1fr 60px;
    }
    .form-group.direction {
        justify-content: space-evenly;
    }
    .form-group.game > input,
    .form-group.player > select,
    .form-group.rank > select,
    .form-group.score > input, {
        text-align: right;
        padding: 5px 10px;
    }
    .form-group label {
        font-weight: bold;
        color: #333;
        display: flex;
        justify-content: flex-end;
        align-items: flex-end;
    }
    /* 入力欄の共通スタイル */
    .input, select, input[type="tel"], input[type="number"] {
        width: 100%;
        padding: 8px 10px !important;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 6px;
        text-align: right;
        box-sizing: border-box;
    }
</style>
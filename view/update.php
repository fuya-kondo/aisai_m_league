<?php
// Include necessary files
require_once __DIR__ . '/../config/import_file.php';

// Handling POST requests
$isFix = false;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['historyId']) && isset($_POST['rank']) && isset($_POST['score']) && isset($_POST['game']) && isset($_POST['direction']) && isset($_POST['userId'])) {
        $isFix = true;
        $historyId  = $_POST['historyId'];
        $rank       = $_POST['rank'];
        $score      = $_POST['score'];
        $game       = $_POST['game'];
        $direction  = $_POST['direction'];
        $userId     = $_POST['userId'];
    }
    if (isset($_POST['historyId']) && isset($_POST['new_rank']) && isset($_POST['new_score']) && isset($_POST['new_game']) && isset($_POST['new_direction']) && isset($_POST['userId'])) {
        $historyId  = $_POST['historyId'];
        $rank       = $_POST['new_rank'];
        $score      = $_POST['new_score'];
        $game       = $_POST['new_game'];
        $direction  = $_POST['new_direction'];
        $userId     = $_POST['userId'];
        updateData((int)$historyId, $rank, (int)$score, (int)$game, (int)$direction);
        header("Location: history?userId=" . $userId);
        exit();
    }
}

// Set title
$title = '修正';

// Include header
include '../webroot/common/header.php';
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="format-detection" content="telephone=no">
        <link rel="stylesheet" href="../webroot/css/master.css">
        <link rel="stylesheet" href="../webroot/css/header.css">
        <link rel="stylesheet" href="../webroot/css/button.css">
        <link rel="stylesheet" href="../webroot/css/table.css">
        <title><?= $title; ?></title>
    </head>
    <body>
        <main>
        <?php if ($isFix): ?>
            <div class="page-title"><?= $title; ?></div>
            <div class="button-container" style="text-align: center;">
                <form action="update" method="post">
                    <select class="input" name="new_rank" require_onced style="width: 100px">
                        <?php foreach($rankConfig as $value => $name): ?>
                            <option value="<?=$value?>" <?php if($value==$rank):?>selected<?php endif;?>><?=$name?></option>
                        <?php endforeach;?>
                    </select><br>
                    <input class="input" type="number" name="new_score" require_onced value="<?= htmlspecialchars($score) ?>" style="width: 80px"><br>
                    <input class="input" type="number" name="new_game" require_onced value="<?= htmlspecialchars($game) ?>" style="width: 80px">半荘目
                    <div class="form-group">
                        <div class="button-containers">
                            <?php foreach($mDirectionList as $directionId => $directionName): ?>
                                <button class="direction-button <?php if($direction==$directionId):?>selected<?php endif;?>" type="button" name="new_direction" value="<?=$directionId?>" require_onced <?php if($direction==$directionId):?>selected<?php endif;?> onclick="selectButton(this)"><?=$directionName?></button>
                            <?php endforeach;?>
                            <input type="hidden" id="direction" name="new_direction" value="<?=$direction?>">
                        </div>
                    </div>
                    <input name="userId" value="<?= htmlspecialchars($userId) ?>" style="display:none"><br><br>
                    <button class="submit-button"type="submit" name="historyId" value="<?= htmlspecialchars($historyId) ?>">修正する</button>
                </form>
            </div>
        <?php endif; ?>
        </main>
    </body>
</html>
<script>

    function selectButton(button) {
        const buttons = document.querySelectorAll('.direction-button');
        buttons.forEach(btn => btn.classList.remove('selected'));
        button.classList.add('selected');
        document.getElementById('direction').value = button.value;
    }
</script>
<style>
    .container {
        max-width: 600px;
        margin: auto;
        background-color: white;
        border-radius: 4px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }
    .button-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
        align-items: center; /* 中央揃え */
    }
    .input {
        text-align: center;
        height: 35px;
        font-size: 15px;
        margin: 10px 0; /* 上下のマージンを設定 */
        width: calc(100% - 20px); /* 幅を調整 */
        padding: 5px; /* 内側の余白を追加 */
        border-radius: 4px; /* 角丸 */
        border: 1px solid #ccc; /* ボーダー */
    }
    .submit-button {
        padding: 10px 20px;
        font-size: 16px;
        color: white;
        background-color: #009944; /* ボタンの背景色 */
        border: none; /* ボーダーなし */
        border-radius: 4px; /* ボタンの角丸 */
        cursor: pointer; /* カーソルをポインターに */
        transition: background-color 0.3s ease; /* ホバー時のエフェクト */
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }
    .button-containers {
        display: flex;
    }
    .direction-button {
        padding: 10px 20px;
        margin-right: 5px;
        border: 1px solid #ccc;
        cursor: pointer;
    }
    .direction-button:last-child {
        margin-right: 0;
    }
    .selected {
        background-color: #1e90ff; /* 薄い青 */
        color: black; /* 選択時の文字色（見やすさのため） */
    }
    @media screen and (max-width: 768px) {
        .input {
            font-size: 14px; /* スマホ用にフォントサイズを小さく */
            padding: 4px; /* スマホ用に内側の余白を調整 */
            width: calc(100% - 10px); /* 幅を調整 */
        }
        button {
            font-size: 14px; /* スマホ用にフォントサイズを小さく */
            padding: 8px; /* スマホ用にパディングを調整 */
        }
    }
</style>
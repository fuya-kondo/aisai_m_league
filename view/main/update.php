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
    <title><?= $title ?></title>
</head>
<body>
    <main>
        <?php if ($isFix): ?>
            <div class="page-title"><?= $title ?></div>
            <div class="button-container" style="text-align: center;">
                <form action="update" method="post" onsubmit="return validateForm()">
                    <select class="input" name="new_rank" required style="max-width: 160px">
                        <?php foreach($rankConfig as $value => $name): ?>
                            <option value="<?=$value?>" <?php if($value==$rank):?>selected<?php endif;?>><?=$name?></option>
                        <?php endforeach;?>
                    </select><br>
                    <input class="input" type="number" name="new_score" required value="<?= $score ?>" style="max-width: 140px"><br>
                    <div class="date-inputs" style="justify-content:center;">
                        <input class="input" type="number" name="new_game" required value="<?= $game ?>" style="max-width: 120px">
                        <label style="margin:0 0 0 8px; font-size:14px;">半荘目</label>
                    </div>
                    <div class="form-group">
                        <div class="button-containers">
                            <?php foreach($mDirectionList as $directionId => $directionData): ?>
                                <button class="direction-button <?php if($direction==$directionId):?>selected<?php endif;?>" type="button" name="new_direction" value="<?=$directionId?>" required <?php if($direction==$directionId):?>selected<?php endif;?> onclick="selectButton(this)"><?=$directionData['name']?></button>
                            <?php endforeach;?>
                            <input type="hidden" id="direction" name="new_direction" value="<?=$direction?>">
                        </div>
                    </div>
                    <input name="userId" value="<?= $userId ?>" style="display:none"><br><br>
                    <button class="submit-button btn-primary" type="submit" name="historyId" value="<?= $historyId ?>">修正する</button>
                </form>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
<script>
   function validateForm() {
        const directionInput = document.getElementById('direction');
        if (directionInput.value === '') {
            alert('席を選択してください。'); // エラーメッセージを表示
            return false; // フォーム送信をキャンセル
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
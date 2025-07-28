<?php

// Include necessary files
require_once __DIR__ . '/../config/import_file.php';
// Include header
include '../webroot/common/header.php';

// Handling POST requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $settingId = $_POST['settingId'] ?? null;
    if ($settingId !== null) {
        try {
            switchMode($settingId);
            header("Location: setting.php");
            exit();
        } catch (Exception $e) {
            $error_msg = '処理中にエラーが発生しました: ' . $e->getMessage();
        }
    } else {
        $error_msg = '必要なパラメータが不足しています';
    }
}

// Set title
$title = '設定';
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
    <link rel="stylesheet" href="../webroot/css/button.css">
    <link rel="stylesheet" href="../webroot/css/table.css">
    <title><?= $title ?></title>
</head>
<body>
<main>

    <h2>点数非表示モード</h2>
    <div class="toggle-button-container">
        <form action="setting" method="post">
            <button id="toggleButton" type="submit" name="settingId" value="1" class="toggle-button on">
                <span class="toggle-text">ON</span>
                <div class="toggle-handle"></div>
            </button>
        </form>
    </div>

</main>
</body>
</html>

<script>
    const toggleButton = document.getElementById('toggleButton');
    const toggleText = toggleButton.querySelector('.toggle-text');

    // ボタンの現在の状態（true: ON, false: OFF）
    let isToggledOn = true; 

    // ボタンがクリックされた時の処理
    toggleButton.addEventListener('click', () => {
        isToggledOn = !isToggledOn; // 状態を反転

        if (isToggledOn) {
            // ONの状態にする
            toggleButton.classList.remove('off');
            toggleButton.classList.add('on');
            toggleText.textContent = 'ON';
        } else {
            // OFFの状態にする
            toggleButton.classList.remove('on');
            toggleButton.classList.add('off');
            toggleText.textContent = 'OFF';
        }
    });

    <?php
        if ($scoreHiddenMode == 1) {
            echo "isToggledOn = true;";
            echo "toggleButton.classList.add('on');";
            echo "toggleText.textContent = 'ON';";
        } else {
            echo "isToggledOn = false;";
            echo "toggleButton.classList.add('off');";
            echo "toggleText.textContent = 'OFF';";
        }
    ?>
</script>

<style>
    /* トグルボタンのコンテナ */
    .toggle-button-container {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    /* トグルボタンの基本スタイル */
    .toggle-button {
        width: 100px; /* ボタンの幅 */
        height: 40px; /* ボタンの高さ */
        border-radius: 20px; /* 角丸でカプセル型に */
        border: none;
        cursor: pointer;
        position: relative;
        overflow: hidden; /* ハンドルがはみ出さないように */
        transition: background-color 0.3s ease-in-out;
        display: flex;
        align-items: center;
        justify-content: space-between; /* ON/OFFテキストとハンドルを左右に配置 */
        padding: 0 10px; /* テキストとハンドルの内側余白 */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* 軽く影をつける */
    }

    /* ON状態のスタイル */
    .toggle-button.on {
        background-color: #4CAF50; /* 緑色 */
    }

    /* OFF状態のスタイル */
    .toggle-button.off {
        background-color: #ccc; /* 灰色 */
    }

    /* トグルボタンのテキスト */
    .toggle-text {
        color: white;
        font-weight: bold;
        font-size: 1.1em;
        position: absolute; /* ハンドルと重ならないように絶対配置 */
        transition: transform 0.3s ease-in-out;
    }

    /* ON時のテキスト位置 */
    .toggle-button.on .toggle-text {
        left: 15px; /* ONの文字を左に */
        transform: translateX(0);
    }

    /* OFF時のテキスト位置 */
    .toggle-button.off .toggle-text {
        right: -30px; /* OFFの文字を右に */
        transform: translateX(0);
    }

    /* トグルボタンのハンドル（丸い部分） */
    .toggle-handle {
        width: 30px;
        height: 30px;
        background-color: white;
        border-radius: 50%; /* 完全な丸に */
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        transition: left 0.3s ease-in-out;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3); /* ハンドルに影 */
    }

    /* ON時のハンドル位置 */
    .toggle-button.on .toggle-handle {
        left: calc(100% - 35px); /* 右端に寄せる */
    }

    /* OFF時のハンドル位置 */
    .toggle-button.off .toggle-handle {
        left: 5px; /* 左端に寄せる */
    }
</style>
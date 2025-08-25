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

    <?php foreach($mSettingList as $settingId => $data): ?>
        <h2><?=$data['name']?>モード</h2>
        <div class="toggle-button-container">
            <form action="setting" method="post">
                <button type="submit" name="settingId" value="<?=$settingId?>" class="toggle-button <?=$data['value'] == 1 ? 'on' : 'off'?>">
                    <span class="toggle-text"><?=$data['value'] == 1 ? 'ON' : 'OFF'?></span>
                    <div class="toggle-handle"></div>
                </button>
            </form>
        </div>
    <?php endforeach; ?>

</main>
</body>
</html>

<script>
    // ページ上のすべてのトグルボタンを取得
    const toggleButtons = document.querySelectorAll('.toggle-button');

    // それぞれのボタンに対して処理を設定
    toggleButtons.forEach(button => {
        const toggleText = button.querySelector('.toggle-text');

        // ボタンがクリックされた時の処理
        button.addEventListener('click', () => {
            // 現在のクラスを反転させる
            button.classList.toggle('on');
            button.classList.toggle('off');

            // テキストも反転
            if (button.classList.contains('on')) {
                toggleText.textContent = 'ON';
            } else {
                toggleText.textContent = 'OFF';
            }
        });
    });
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
        right: 10px; /* OFFの文字を右に */
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
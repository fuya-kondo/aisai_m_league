<?php

// Include necessary files
require_once __DIR__ . '/../config/import_file.php';
// Include header
include '../webroot/common/header.php';

// Set title
$title = '';
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
    <title><?php echo $title; ?></title>
</head>
<body>
    <main>
        <div class="button-container">
            <button class="sound-button blue" data-sound="../sound/pon.mp3">ポン</button>
            <button class="sound-button red" data-sound="../sound/chi.mp3">チー</button>
            <button class="sound-button green" data-sound="../sound/kan.mp3">カン</button>
            <button class="sound-button yellow" data-sound="../sound/ri-chi.mp3">リーチ</button>
            <button class="sound-button purple" data-sound="../sound/ron.mp3">ロン</button>
            <button class="sound-button orange" data-sound="../sound/tumo.mp3">ツモ</button>
        </div>

        <audio id="audioPlayer"></audio>
    </main>
</body>
</html>

<script>
    // JavaScriptでボタンと音を紐付ける
    document.addEventListener('DOMContentLoaded', () => {
        const buttons = document.querySelectorAll('.sound-button');
        const audioPlayer = document.getElementById('audioPlayer');

        buttons.forEach(button => {
            button.addEventListener('click', function() {
                const soundFile = this.dataset.sound; // data-sound属性からファイル名を取得
                if (soundFile) {
                    audioPlayer.src = soundFile;
                    audioPlayer.play();
                }
            });
        });
    });
</script>

<style>
    .button-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr); /* 2列で表示 */
        gap: 20px; /* ボタン間のスペース */
        padding: 20px;
        max-width: 600px; /* コンテナの最大幅 */
        width: 100%;
        box-sizing: border-box;
    }
    .sound-button {
        width: 100%;
        padding: 30px 20px;
        font-size: 24px;
        font-weight: bold;
        color: #fff;
        background-color: #007bff;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s ease, transform 0.1s ease;
        -webkit-tap-highlight-color: rgba(0, 0, 0, 0); /* スマホでのタップ時のハイライトを無効化 */
    }
    .sound-button:active {
        background-color: #0056b3;
        transform: scale(0.98);
    }
    /* ボタンの色を個別に設定する例 */
    .sound-button.red { background-color: #dc3545; }
    .sound-button.green { background-color: #28a745; }
    .sound-button.yellow { background-color: #ffc107; }
    .sound-button.purple { background-color: #6f42c4; }
    .sound-button.orange { background-color: #fd7e14; }

    /* スマートフォン向けに1列表示にするメディアクエリ */
    @media (max-width: 600px) {
        .button-container {
            grid-template-columns: 1fr; /* 1列で表示 */
            padding: 15px;
        }
        .sound-button {
            font-size: 20px;
            padding: 25px 15px;
        }
    }
</style>
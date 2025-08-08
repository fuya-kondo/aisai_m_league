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
    <link rel="stylesheet" href="../webroot/css/app.css">
    <title><?= $title ?></title>
</head>
<body>
<main>
    <div class="button-container grid-2">
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
        margin-top: 25px;
    }
    /* Sound buttons */
    .button-container.grid-2 { display:grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
    .sound-button { font-size: 1.1rem; padding: 14px; color:#fff; }
    .sound-button.red{ background:#dc3545; }
    .sound-button.green{ background:#28a745; }
    .sound-button.yellow{ background:#ffc107; color:#333; }
    .sound-button.purple{ background:#6f42c4; }
    .sound-button.orange{ background:#fd7e14; }
</style>
<?php

// Include necessary files
require_once __DIR__ . '/../config/import_file.php';
// Include header
include '../webroot/common/header.php';

// Set title
$title = 'AISAI.M.LEAGUE';
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
<main style="padding-left: 0; padding-right: 0;">
    <div id="schedule">
        <div class="day-area">
            <h2 class="day"><?=$formattedDate?></h2>
            <ul class="playerImg">
                <li><img src="../image/player_1.png"></li>
                <li><img src="../image/player_2.png"></li>
                <li><img src="../image/player_3.png"></li>
                <li><img src="../image/player_4.png"></li>
            </ul>
        </div>
    </div>
    <div id="about">
        <h1>What is AISAI.M.LEAGUE</h1>
        <h3>いま、最高の遊びが、最高の競技になる。</h3>
        <div>
            <p style="font-weight:700">
                麻雀リーグ戦、AISAI.Mリーグ開幕。
                数多の麻雀プレイヤー達の中から、ほんの一握りの愛西市民だけが出場できるナショナルリーグが始まる。
                知性に裏打ちされた采配。洗練されたリーグ空間。
                企業とプロ契約を結ばず、タンクトップを纏ったAISAI.Mリーガー達が威信をかけて知を競い合う。<br><br>
            </p>
        </div>
        <h3>さぁ、麻雀をあたらしい時代へ。</h3>
    </div>
</main>
</body>
</html>

<script>
    const images = document.querySelectorAll('.playerImg img');
    let currentIndex = -1;  // -1から開始

    function showNextImage() {
        currentIndex++;
        if (currentIndex < images.length) {
            images[currentIndex].classList.add('active');
            setTimeout(showNextImage, 1000);
        }
    }

    setTimeout(showNextImage, 1000);
</script>

<style>
    .playerImg img {
        opacity: 0; /* 最初は非表示 */
        transition: opacity 1s ease-in-out; /* フェードイン/アウト効果 */
        border-radius: 5px;
    }
    .playerImg img.active {
        opacity: 1; /* 表示 */
    }
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    #about {
        text-align: center;
        margin: 50px 0px;
    }
    #about > h1 {
        margin: 20px 30px 10px 30px;
    }
    #about > h3 {
        margin: 0px 30px 40px 30px;
    }
    #about > div > p {
        line-height: 1.8;
        margin: 0px 20px;
    }
    @media screen and (max-width: 768px) {
    }
    @media (orientation: portrait) {
    }
    @media (orientation: landscape) {
    }
</style>
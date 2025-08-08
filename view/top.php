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
                <li>
                    <video autoplay muted playsinline loop>
                        <source src="../movie/player_1.mp4" type="video/mp4">
                        お使いのブラウザは動画タグをサポートしていません。
                    </video>
                </li>
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
    /* /対局予定\ */
    #schedule {
        background-image: url("https://m-league.jp/assets/media/img/common/bg-pattern_sp.png");
        background-repeat: repeat;
        background-origin: padding-box;
        padding-top: 20px;
        padding-bottom: 40px;
        text-align: center;
    }
    #schedule .day {
        font-size: 3.5rem;
        margin: 10px 0;
    }
    #schedule .playerImg {
        list-style: none;
        padding-inline-start: 0px;
    }
    #schedule .playerImg > li{
        display: inline;
    }
    #schedule .playerImg > li > img, #schedule .playerImg > li > video {
        width: 219px;
        display: inline;
        margin: 0 10px;
    }
    .playerImg img, .playerImg video {
        /* opacity: 0; */
        /* transition: opacity 1s ease-in-out; */
        border-radius: 5px;
    }
    /*
    .playerImg img.active {
        opacity: 1;
    }
    */
    /* @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    } */

    /* 小型スマホ向け */
    @media (max-width: 375px) {
        #schedule .day {
            font-size: 2.5rem;
        }
        #schedule .playerImg {
            list-style: none;
            padding-inline-start: 0px;
            display: grid;
            grid-template-columns: repeat(2, auto);
            gap: 20px 15px; /* 縦横の隙間を10pxに統一 */
            justify-content: center; /* 水平方向の中央揃え */
            align-content: center; /* 垂直方向の中央揃え */
        }
        #schedule .playerImg > li {
            text-align: center;
        }
        #schedule .playerImg > li > img, #schedule .playerImg > li > video {
            width: 120px;
            display: block;
            margin: 0 auto;
        }
    }
    /* 標準サイズスマホ向け */
    @media (min-width: 376px) and (max-width: 767px) {
        #schedule .day {
            font-size: 2.5rem;
        }
        #schedule .playerImg {
            list-style: none;
            padding-inline-start: 0px;
            display: grid;
            grid-template-columns: repeat(2, auto);
            gap: 20px 15px; /* 縦横の隙間を10pxに統一 */
            justify-content: center; /* 水平方向の中央揃え */
            align-content: center; /* 垂直方向の中央揃え */
        }
        #schedule .playerImg > li {
            text-align: center;
        }
        #schedule .playerImg > li > img, #schedule .playerImg > li > video {
            width: 136px;
            display: block;
            margin: 0 auto;
        }
    }
    /* 大型スマホ向け */
    @media (min-width: 768px) and (max-width: 1024px) {
        #schedule .day {
            font-size: 2.5rem;
        }
        #schedule .playerImg {
            list-style: none;
            padding-inline-start: 0px;
            display: grid;
            grid-template-columns: repeat(2, auto);
            gap: 20px 15px; /* 縦横の隙間を10pxに統一 */
            justify-content: center; /* 水平方向の中央揃え */
            align-content: center; /* 垂直方向の中央揃え */
        }
        #schedule .playerImg > li {
            text-align: center;
        }
        #schedule .playerImg > li > img, #schedule .playerImg > li > video {
            width: 136px;
            display: block;
            margin: 0 auto;
        }
    }
    /* \対局予定/ */

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
</style>
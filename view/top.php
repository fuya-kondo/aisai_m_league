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
    <link rel="stylesheet" href="../webroot/css/master.css">
    <link rel="stylesheet" href="../webroot/css/header.css">
    <link rel="stylesheet" href="../webroot/css/button.css">
    <link rel="stylesheet" href="../webroot/css/table.css">
    <title><?php echo $title; ?></title>
</head>
<body>
    <div class="main">
        <div id="schedule">
            <div class="day-area">
                <h2 class="day"><?=$formattedDate?></h2>
                <ul class="playerImg">
                    <li><img src="https://m-league.jp/wp/wp-content/uploads/2018/10/AR_7-1_hagiwara-1.png"></li>
                    <li><img src="https://m-league.jp/wp/wp-content/uploads/2018/09/AR_3-4_hori-1.png"></li>
                    <li><img src="https://m-league.jp/wp/wp-content/uploads/2018/10/AR_5-2_shiratori-1.png"></li>
                    <li><img src="https://m-league.jp/wp/wp-content/uploads/2017/08/AR_8-2_nakabayashi-2.png"></li>
                </ul>
            </div>
        </div>
        <div id="about">
            <h1>What is AISAI.M.LEAGUE</h1>
            <h3>いま、最高の個人競技が、最高の団体競技になる。</h3>
            <div>
                <p style="font-weight:700">
                    麻雀プロリーグ戦、AISAI.Mリーグ開幕。数多の麻雀プレイヤー達の中から、ほんの一握りのトッププロだけが出場できるナショナルリーグが始まる。知性に裏打ちされた采配。洗練されたリーグ空間。企業とプロ契約を結び、ユニフォームを纏ったAISAI.Mリーガー達がチームの威信をかけて知を競い合う。さぁ、麻雀をあたらしい時代へ。
                </p>
            </div>
        </div>
    </div>
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
    }
    .playerImg img.active {
        opacity: 1; /* 表示 */
    }
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    @media screen and (max-width: 768px) {
    }
    @media (orientation: portrait) {
    }
    @media (orientation: landscape) {
    }
</style>
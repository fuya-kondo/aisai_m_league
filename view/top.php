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
    .table-container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
        overflow: hidden;
    }
    .today-title {
        background-color: #228b22;
        color: #fff;
        padding: 15px;
        margin: 0;
        font-size: 1.2em;
    }
    .table-wrapper {
        overflow-x: auto;
        padding: 15px;
    }
    .score-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .score-table th,
    .score-table td {
        padding: 12px;
        text-align: center;
    }
    .score-table th {
        background-color: #228b22;
        color: white;
        font-weight: bold;
        border: none;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .score-table th:first-child {
        border-top-left-radius: 8px;
    }
    .score-table th:last-child {
        border-top-right-radius: 8px;
    }
    .score-table td {
        border-bottom: 1px solid #e0e0e0;
    }
    .score-table tr:last-child td:first-child {
        border-bottom-left-radius: 8px;
    }
    .score-table tr:last-child td:last-child {
        border-bottom-right-radius: 8px;
    }
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
        .table-wrapper {
            padding: 10px;
        }
        .score-table th,
        .score-table td {
            padding: 10px 8px;
            font-size: 0.85em;
        }
        .score-table th {
            -webkit-writing-mode: vertical-rl;
            -ms-writing-mode: tb-rl;
            writing-mode: vertical-rl;
            text-align: center; /* 中央揃え */
            line-height: 1.5; /* 行間を調整 */
        }
    }
    @media (orientation: portrait) {
        .score-table th {
            writing-mode: vertical-rl;
        }
    }
    @media (orientation: landscape) {
        .score-table th {
            writing-mode: horizontal-tb; /* 横書きに変更 */
        }
    }
</style>
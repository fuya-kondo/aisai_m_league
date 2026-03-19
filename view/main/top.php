<?php
/**
 * トップページビュー。
 * 開催予定、リーグ紹介、メディア表示を組み合わせたランディング画面を構成する。
 */


$title = 'AISAI.M.LEAGUE';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <link rel="apple-touch-icon" href="<?= h($baseUrl) ?>/favicon.png">
    <link rel="icon" href="<?= h($baseUrl) ?>/favicon.ico" sizes="64x64" type="image/x-icon">
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/master.css">
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/header.css">
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/bottom_navigation.css">
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/app.css">
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/pages/main-top.css">
    <title><?= h($title) ?></title>
</head>
<body>
<?php include __DIR__ . '/bottom_navigation.php'; ?>
<main class="top-main">
    <nav id="headerArea">
        <a href="<?= h($baseUrl) ?>/top"><img src="<?= h($baseUrl) ?>/resources/image/aisai_m_league.jpg" alt="AISAI.M.LEAGUE"></a>
    </nav>
    <div id="schedule">
        <div class="day-area">
            <div class="game-day-container">
            <?php if (!empty($nextTwoGameDays)): ?>
                <div class="day">
                    <?= h($nextTwoGameDays[0]) ?>
                    <?php if (count($nextTwoGameDays) > 1): ?>
                    <div class="next-day">
                        <?= h($nextTwoGameDays[1]) ?>
                    </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="day">予定なし</div>
            <?php endif; ?>
            </div>
            <ul class="playerImg">
                <li>
                    <video autoplay muted playsinline loop>
                        <source src="<?= h($baseUrl) ?>/resources/movie/player_1.mp4" type="video/mp4">
                        お使いのブラウザは動画タグをサポートしていません。
                    </video>
                </li>
                <li><img src="<?= h($baseUrl) ?>/resources/image/player_2.png"></li>
                <li><img src="<?= h($baseUrl) ?>/resources/image/player_3.png"></li>
                <li><img src="<?= h($baseUrl) ?>/resources/image/player_4.png"></li>
            </ul>
        </div>
    </div>
    <div id="about">
        <h1>What is AISAI.M.LEAGUE</h1>
        <h3>いま、最高の遊びが、最高の競技になる。</h3>
        <div>
            <p class="top-intro-text">
                麻雀リーグ戦、AISAI.Mリーグ開幕。
                数多の麻雀プレイヤー達の中から、ほんの一握りの愛西市民だけが出場できるナショナルリーグが始まる。
                知性に裏打ちされた采配。洗練されたリーグ空間。
                企業とプロ契約を結ばず、ユニフォームを纏ったAISAI.Mリーガー達が威信をかけて知を競い合う。<br><br>
            </p>
        </div>
        <h3>さぁ、麻雀をあたらしい時代へ。</h3>
    </div>
</main>
<script src="<?= h($baseUrl) ?>/resources/js/main/image-sequence.js"></script>
</body>
</html>

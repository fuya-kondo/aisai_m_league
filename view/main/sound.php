<?php
/**
 * 発声ページビュー。
 * 麻雀用効果音の再生ボタンを並べ、ワンタップで再生できるようにする。
 */

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
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/bottom_navigation.css">
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/app.css">
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/pages/main-sound.css">
    <title><?= h($title) ?></title>
</head>
<body>
<?php include __DIR__ . '/bottom_navigation.php'; ?>
<main>
    <?php include __DIR__ . '/page_title.php'; ?>
    <div class="button-container grid-2">
        <button class="sound-button blue" data-sound="<?= h($baseUrl) ?>/resources/sound/pon.mp3">ポン</button>
        <button class="sound-button red" data-sound="<?= h($baseUrl) ?>/resources/sound/chi.mp3">チー</button>
        <button class="sound-button green" data-sound="<?= h($baseUrl) ?>/resources/sound/kan.mp3">カン</button>
        <button class="sound-button yellow" data-sound="<?= h($baseUrl) ?>/resources/sound/ri-chi.mp3">リーチ</button>
        <button class="sound-button purple" data-sound="<?= h($baseUrl) ?>/resources/sound/ron.mp3">ロン</button>
        <button class="sound-button orange" data-sound="<?= h($baseUrl) ?>/resources/sound/tumo.mp3">ツモ</button>
    </div>

    <audio id="audioPlayer"></audio>
</main>
<script src="<?= h($baseUrl) ?>/resources/js/main/sound-player.js"></script>
</body>
</html>

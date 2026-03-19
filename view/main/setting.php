<?php
/**
 * 設定ページビュー。
 * 運用フラグの ON/OFF 切り替えを単純なトグル UI として表示する。
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
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/pages/main-setting.css">
    <title><?= h($title) ?></title>
</head>
<body>
<?php include __DIR__ . '/bottom_navigation.php'; ?>
<main>
    <?php include __DIR__ . '/page_title.php'; ?>
    <?php if (!empty($error_msg)): ?>
        <div class="error-message"><?= h($error_msg) ?></div>
    <?php endif; ?>

    <?php foreach ($mSettingList as $settingId => $settingRecord): ?>
        <h2><?= h($settingRecord['name']) ?>モード</h2>
        <div class="toggle-button-container">
            <form action="setting" method="post">
                <button type="submit" name="settingId" value="<?= h($settingId) ?>" class="toggle-button <?= $settingRecord['value'] == 1 ? 'on' : 'off' ?>">
                    <span class="toggle-text"><?= h($settingRecord['value'] == 1 ? 'ON' : 'OFF') ?></span>
                    <div class="toggle-handle"></div>
                </button>
            </form>
        </div>
    <?php endforeach; ?>

</main>
<script src="<?= h($baseUrl) ?>/resources/js/main/settings-toggle.js"></script>
</body>
</html>

<?php
/**
 * 称号選択ページビュー。
 * 現在の称号と選択可能な称号一覧を表示し、送信時に称号を切り替える。
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
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/pages/main-badge.css">
    <title><?= h($title) ?></title>
</head>
<body>
<?php include __DIR__ . '/bottom_navigation.php'; ?>
<main>
    <?php include __DIR__ . '/page_title.php'; ?>

    <div class="back-button-container">
        <a href="personal?&player=<?= h($userId) ?>" class="back-button">個人成績へ戻る</a>
    </div>

    <?php if (isset($successMessage)): ?>
        <div class="success-message"><?= h($successMessage) ?></div>
    <?php endif; ?>

    <div class="current-badge-container">
        <h3>現在の称号</h3>
        <?php if ($currentBadgeData): ?>
            <div class="badge-item current">
                <?= htmlspecialchars($currentBadgeData['image']) ?>
                <span class="badge-name"><?= htmlspecialchars($currentBadgeData['name']) ?></span>
            </div>
        <?php else: ?>
            <p>称号が設定されていません。</p>
        <?php endif; ?>
    </div>

    <hr class="divider">

    <div class="badge-list-container">
        <h3>称号を選択</h3>
        <form action="" method="post" class="badge-selection-form">
            <div class="badge-grid">
                <?php foreach ($mBadgeList as $badgeId => $badgeData): ?>
                    <?php // ユーザーが所持している称号のみ表示
                        if (!in_array($badgeId, $userPossessionBadgeIds)) {
                            continue;
                        }
                    ?>
                    <button type="submit" name="m_badge_id" value="<?= htmlspecialchars($badgeId) ?>" 
                            class="badge-button <?= ($badgeId == $currentUserBadgeId) ? 'selected' : '' ?>"
                            aria-label="称号を<?= htmlspecialchars($badgeData['name']) ?>に変更する">
                        <div class="badge-item">
                            <!-- <img src="../../webroot/image/badge/<?= htmlspecialchars($badgeData['image']) ?>" alt="" class="badge-image"> -->
                            <span class="badge-name"><?= htmlspecialchars($badgeData['name']) ?></span>
                        </div>
                    </button>
                <?php endforeach; ?>
            </div>
        </form>
    </div>

</main>
</body>
</html>

<?php

// Include necessary files
require_once __DIR__ . '/../config/import_file.php';
// Include header
include '../webroot/common/header.php';

// Set title
$title = '称号';

$userId = isset($_GET['userId']) ? $_GET['userId'] : null;

$userPossessionBadgeIds = array_keys($mBadgeList);

$currentUserBadgeId = $userList[$userId]['badge']['m_badge_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['m_badge_id'])) {
    $selectedBadgeId = (int)$_POST['m_badge_id'];

    if (in_array($selectedBadgeId, $userPossessionBadgeIds)) {
        updateUserBadge($userId, $selectedBadgeId);

        $currentUserBadgeId = $selectedBadgeId;

        $successMessage = "称号を「" . htmlspecialchars($mBadgeList[$selectedBadgeId]['name']) . "」に変更しました。";
    }
}

// 現在の称号データを取得
$currentBadgeData = $mBadgeList[$currentUserBadgeId] ?? null;
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
<main>

    <div class="back-button-container">
        <a href="personal_stats?&player=<?= $userId ?>" class="back-button">個人成績へ戻る</a>
    </div>

    <?php if (isset($successMessage)): ?>
        <div class="success-message"><?= $successMessage ?></div>
    <?php endif; ?>

    <div class="current-badge-container">
        <h3>現在の称号</h3>
        <?php if ($currentBadgeData): ?>
            <div class="badge-item current">
                <!-- <img src="../webroot/image/badge/<?= htmlspecialchars($currentBadgeData['image']) ?>" alt="" class="badge-image"> -->
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
                            <!-- <img src="../webroot/image/badge/<?= htmlspecialchars($badgeData['image']) ?>" alt="" class="badge-image"> -->
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

<script>
// JavaScriptでの特別な処理は不要な設計ですが、
// 今後の拡張のために残しておきます。
</script>

<style>
    .page-title {
        font-size: 22px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 20px;
    }
    .divider {
        border: none;
        border-top: 1px solid #eee;
        margin: 25px 0;
    }
    .success-message {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        text-align: center;
    }

    /* コンテナのスタイル */
    .current-badge-container,
    .badge-list-container {
        margin-bottom: 20px;
    }
    .current-badge-container h3,
    .badge-list-container h3 {
        font-size: 16px;
        font-weight: bold;
        border-bottom: 2px solid #efefef;
        padding-bottom: 8px;
        margin-bottom: 15px;
    }

    /* 称号アイテムの共通スタイル */
    .badge-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
        padding: 10px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-sizing: border-box;
    }
    .badge-image {
        width: 50px;
        height: 50px;
        object-fit: contain;
    }
    .badge-name {
        font-size: 12px;
        font-weight: 600;
        text-align: center;
        color: #333;
    }
    
    /* 現在の称号表示用 */
    .badge-item.current {
        width: 90%;
        max-width: 300px;
        margin: 0 auto;
        padding: 15px;
        background-color: #fff;
    }
    .badge-item.current .badge-image {
        width: 60px;
        height: 60px;
    }
     .badge-item.current .badge-name {
        font-size: 14px;
    }

    /* 称号選択グリッド */
    .badge-grid {
        display: grid;
        /* 列の最小幅を90pxとし、画面幅に応じて自動で列数を調整 */
        grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
        gap: 10px;
    }
    .badge-button {
        padding: 0;
        border: 2px solid transparent;
        border-radius: 10px;
        background-color: transparent;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        text-align: left;
        color: inherit;
        font-family: inherit;
    }
    .badge-button:hover .badge-item {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-color: #007bff;
    }

    /* 選択中の称号スタイル */
    .badge-button.selected .badge-item {
        border-color: #28a745;
        box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
        position: relative;
    }
    .badge-button.selected .badge-item::after {
        content: '✔';
        position: absolute;
        top: 2px;
        right: 4px;
        background-color: #28a745;
        color: white;
        font-size: 10px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }

    .back-button-container {
        text-align: center;
        padding-top: 20px;
        margin-bottom: 20px;
    }
    .back-button {
        display: inline-block;
        padding: 10px 20px;
        font-size: 16px;
        font-weight: bold;
        color: #333;
        background-color: #f0f0f0;
        border: 1px solid #ccc;
        border-radius: 8px;
        text-decoration: none;
        transition: background-color 0.2s ease;
    }
    .back-button:hover {
        background-color: #e0e0e0;
    }
</style>
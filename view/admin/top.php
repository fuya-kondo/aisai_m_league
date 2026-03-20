<?php
/**
 * 管理画面トップ。
 * 管理系メニューの入口として統計と各管理ページへの導線を表示する。
 */
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageData['title']) ?></title>
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/master.css">
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/admin.css">
</head>
<body>
    <div class="admin-container admin-top">
        <div class="admin-header">
            <h1 class="admin-title">管理画面</h1>
            <p class="admin-subtitle">AISAI.M.LEAGUE<br>データベース管理システム</p>
        </div>

        <div class="nav-grid">
            <a class="nav-card" href="<?= h($baseUrl) ?>/admin/users">
                <div class="nav-icon">👥</div>
                <div class="nav-title">ユーザー管理</div>
                <div class="nav-description">
                    ユーザー情報、バッジ、ティア、タイトルの管理を行います。
                    新規ユーザーの追加、既存ユーザーの編集・削除が可能です。
                </div>
            </a>

            <a class="nav-card" href="<?= h($baseUrl) ?>/admin/game-history">
                <div class="nav-icon">🎮</div>
                <div class="nav-title">ゲーム履歴管理</div>
                <div class="nav-description">
                    ゲーム履歴の確認、編集、削除を行います。
                    スコアや結果の修正、不正なデータの削除が可能です。
                </div>
            </a>

            <a class="nav-card" href="<?= h($baseUrl) ?>/admin/master-data">
                <div class="nav-icon">⚙️</div>
                <div class="nav-title">マスターデータ管理</div>
                <div class="nav-description">
                    バッジ、ティア、タイトル、ルール、設定などの
                    マスターデータの管理を行います。
                </div>
            </a>
        </div>

        <div class="admin-top__footer">
            <a href="<?= h($baseUrl) ?>/top" class="back-link">← メインサイトに戻る</a>
        </div>
    </div>

    <?php renderScriptTags($baseUrl, [
        'resources/js/admin/api-client.js',
        'resources/js/admin/top.js',
    ]); ?>
</body>
</html>

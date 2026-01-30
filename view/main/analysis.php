<?php

// Include header
include __DIR__ . '/../header.php';

// Include GeminiAPI
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
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/app.css">
    <title><?= h($title) ?></title>
</head>
<body>
<main>
    <div class="container">
        <?php if (!isset($selectUser)): ?>
            <div class="page-title"><?= h($title) ?></div>
            <div class="select-button-container">
                <form action="analysis" method="get">
                    <?php foreach($userList as $userId => $userData): ?>
                        <button class="select-button" type="submit" name="userId" value="<?= h($userId) ?>"><?= h($userData['last_name'] . $userData['first_name']) ?></button>
                    <?php endforeach; ?>
                </form>
            </div>
            <div class="circle-parent" style="display:none">
                <div class="circle-spin-8"></div>
            </div>
        <?php else: ?>
            <?php if (!empty($analysisError)): ?>
                <div class="error"><?= h($analysisError) ?></div>
            <?php elseif (!empty($analysisResultText)): ?>
                <div class="analysis-result"><?= nl2br(h($analysisResultText)) ?></div>
            <?php else: ?>
                <div class="error">????????????????</div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>
</body>
</html>

<script>
    document.querySelectorAll('.select-button').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelector('.select-button-container').style.display = 'none';
            document.querySelector('.circle-parent').style.display = 'flex';
        });
    });
</script>

<style>
    /* ページ固有の微調整がある場合のみここに追加 */
    .table-responsive {
        overflow-x: auto;
        margin-bottom: 20px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th,td {
        padding: 8px;
        border: 1px solid #ddd;
        white-space: nowrap;
    }
    .textarea {
        text-align: center;
        margin-bottom: 20px;
    }
    textarea {
        width: 100%;
        max-width: 500px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    @media screen and (max-width: 768px) {
        textarea {
            padding: 0;
        }
    }
    .circle-parent {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .circle-spin-8 {
        --size: 24px;
        --color: currentColor;
        --animation-timing-function: linear;
        --animation-duration: 2s;
        width: var(--size);
        height: var(--size);
        mask-image: radial-gradient(circle at 50% 50%, transparent calc(var(--size) / 3), black calc(var(--size) / 3));
        background-image: conic-gradient(transparent, transparent 135deg, currentColor);
        border-radius: 50%;
        animation: var(--animation-timing-function) var(--animation-duration) infinite circle-spin-8-animation;
    }
    @keyframes circle-spin-8-animation {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
</style>

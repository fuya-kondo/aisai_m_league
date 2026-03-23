<?php
/**
 * AI分析履歴詳細ページビュー。
 * 保存済みの分析結果本文を表示する。
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
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/pages/main-analysis.css">
    <title><?= h($title) ?></title>
</head>
<body>
<?php include __DIR__ . '/bottom_navigation.php'; ?>
<main class="analysis-main">
    <div class="container">
        <?php include __DIR__ . '/page_title.php'; ?>

        <?php if ($analysisHistoryError): ?>
            <div class="error analysis-message-card"><?= h($analysisHistoryError) ?></div>
        <?php elseif ($analysisHistory): ?>
            <div class="analysis-history-detail-meta">
                <a class="analysis-history-back-link" href="<?= h($analysisHistoryBackHref) ?>">AI分析へ戻る</a>
                <div class="analysis-result-heading">
                    <?= h(($analysisHistoryPlayerName ?? '') . ' / ' . (($analysisHistory['term'] ?? '') ?: '')) ?>
                </div>
                <div class="analysis-history-meta-row">生成日時: <?= h($analysisHistoryGeneratedAt ?? '') ?></div>
            </div>
            <div class="analysis-result-card">
                <div class="analysis-result"><?= $analysisHistory['result_html'] ?? '' ?></div>
            </div>
        <?php endif; ?>
    </div>
</main>
</body>
</html>

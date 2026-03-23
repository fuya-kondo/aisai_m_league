<?php
/**
 * AI 成績分析ページビュー。
 * 期間・選手選択 UI と、分析開始後の結果表示を担当する。
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

        <form action="analysis" method="get" class="analysis-form" id="analysisForm">
            <input type="hidden" name="term" id="analysisTermInput" value="<?= h($selectedTerm ?? '') ?>">
            <input type="hidden" name="userId" id="analysisUserInput" value="<?= h($selectedUser ?? '') ?>">
            <input type="hidden" name="run" value="1">

            <div class="analysis-selector-label">期間</div>
            <div class="stats-term-selector analysis-selector">
                <div class="stats-term-selector__scroll" role="tablist" aria-label="分析期間">
                    <?php foreach ($termOptions as $option): ?>
                        <button
                            type="button"
                            class="stats-term-button<?= !empty($option['active']) ? ' is-active' : '' ?>"
                            data-analysis-term-button
                            data-value="<?= h((string)$option['value']) ?>"
                            aria-pressed="<?= !empty($option['active']) ? 'true' : 'false' ?>"
                        >
                            <?= h((string)$option['label']) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="analysis-selector-label">選手</div>
            <div class="stats-term-selector analysis-selector">
                <div class="stats-term-selector__scroll" role="tablist" aria-label="分析選手">
                    <?php foreach ($userOptions as $option): ?>
                        <button
                            type="button"
                            class="stats-term-button<?= !empty($option['active']) ? ' is-active' : '' ?>"
                            data-analysis-user-button
                            data-value="<?= h((string)$option['value']) ?>"
                            aria-pressed="<?= !empty($option['active']) ? 'true' : 'false' ?>"
                        >
                            <?= h((string)$option['label']) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <button class="submit-button btn-primary analysis-submit-button" type="submit">分析開始</button>
        </form>

        <div class="circle-parent is-hidden" id="analysisLoading">
            <div class="circle-spin-8"></div>
        </div>

        <?php if ($analysisError): ?>
            <div class="error analysis-message-card"><?= h($analysisError) ?></div>
        <?php elseif ($analysisResultHtml): ?>
            <div class="analysis-result-card">
                <div class="analysis-result-heading">
                    <?= h(($selectedUserName ?? '') . ' / ' . ($selectedTermLabel ?? '')) ?>
                </div>
                <div class="analysis-result"><?= $analysisResultHtml ?></div>
            </div>
        <?php elseif (!$shouldRun): ?>
            <div class="analysis-message-card">
                期間と選手を選んで分析開始してください。
            </div>
        <?php else: ?>
            <div class="error analysis-message-card">分析結果を取得できませんでした。</div>
        <?php endif; ?>

        <?php if ($selectedUser !== null): ?>
            <div class="analysis-history-card">
                <div class="analysis-history-title">過去の分析</div>
                <?php if (!empty($analysisHistoryItems)): ?>
                    <div class="analysis-history-list">
                        <?php foreach ($analysisHistoryItems as $item): ?>
                            <a class="analysis-history-item" href="<?= h($item['detailHref']) ?>">
                                <div class="analysis-history-item__term"><?= h($item['term']) ?></div>
                                <div class="analysis-history-item__status"><?= h($item['statusLabel'] ?? '') ?></div>
                                <div class="analysis-history-item__date"><?= h($item['generatedAt']) ?></div>
                                <div class="analysis-history-item__link">詳細を見る</div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="analysis-message-card">過去の分析はまだありません。</div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>
<script src="<?= h($baseUrl) ?>/resources/js/main/analysis.js"></script>
</body>
</html>

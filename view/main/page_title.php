<?php
/**
 * main 配下の各ページで共通利用するタイトル表示パーツ。
 * コントローラー側で渡された $title または $pageTabs を、統一された見た目で描画する。
 */
if (!empty($pageTabs)):
?>
<div class="page-tabs page-title--main" role="tablist">
    <?php foreach ($pageTabs as $tab): ?>
        <a
            class="page-tab<?= !empty($tab['active']) ? ' is-active' : '' ?>"
            href="<?= h((string)($tab['href'] ?? '#')) ?>"
        >
            <?= h((string)($tab['label'] ?? '')) ?>
        </a>
    <?php endforeach; ?>
</div>
<?php elseif (!empty($title)): ?>
<div class="page-title page-title--main"><?= h($title) ?></div>
<?php endif; ?>

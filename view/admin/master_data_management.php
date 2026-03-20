<?php
/**
 * マスターデータ管理画面。
 * セクション設定に沿ってテーブルとモーダルフォームを描画する。
 */
$adminPageTitle = 'マスターデータ管理';
$masterSections = $pageData['masterSections'] ?? [];
$masterForms = $pageData['masterForms'] ?? [];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageData['title']) ?></title>
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/master.css">
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/bottom_navigation.css">
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/admin.css">
</head>
<body>
    <div class="admin-container admin-master">
        <?php include __DIR__ . '/partials/common/page_header.php'; ?>
        <?php include __DIR__ . '/partials/common/feedback.php'; ?>

        <div class="master-sections">
            <?php foreach ($masterSections as $section): ?>
                <?php include __DIR__ . '/partials/master/section.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">マスターデータ編集</h3>
                <button class="close" type="button" data-modal-close>&times;</button>
            </div>
            <form id="editForm" novalidate>
                <input type="hidden" id="editId" name="id">
                <input type="hidden" id="editTable" name="table">
                <?php include __DIR__ . '/partials/master/modal_forms.php'; ?>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" data-modal-close>キャンセル</button>
                    <button type="submit" class="btn-save">保存</button>
                </div>
            </form>
        </div>
    </div>

    <?php renderScriptTags($baseUrl, [
        'resources/js/admin/api-client.js',
        'resources/js/admin/common.js',
        'resources/js/main/dynamic-colors.js',
        'resources/js/admin/master-data-config.js',
        'resources/js/admin/master-data.js',
    ]); ?>
</body>
</html>

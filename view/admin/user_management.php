<?php
/**
 * ユーザー管理画面。
 * 一覧表示とモーダル編集を同居させ、ユーザー CRUD をブラウザ上で完結させる。
 */
$adminPageTitle = 'ユーザー管理';
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
    <div class="admin-container admin-users">
        <?php include __DIR__ . '/partials/common/page_header.php'; ?>
        <?php include __DIR__ . '/partials/common/feedback.php'; ?>

        <div class="user-section">
            <div class="section-header" data-section-toggle>
                <h3 class="section-title">ユーザー一覧 (<?= count($pageData['users']) ?>件)</h3>
                <div class="section-header__actions">
                    <button class="add-button" type="button" data-user-add>追加</button>
                    <span class="toggle-icon">▼</span>
                </div>
            </div>
            <div class="section-content">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>姓</th>
                                <th>名</th>
                                <th>バッジ</th>
                                <th>ティア</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($pageData['users'] as $user): ?>
                            <tr
                                data-user-id="<?= h($user['u_user_id']) ?>"
                                data-last-name="<?= h($user['last_name']) ?>"
                                data-first-name="<?= h($user['first_name']) ?>"
                                data-badge-id="<?= isset($user['badge']) ? h($user['badge']['m_badge_id']) : '' ?>"
                                data-tier-id="<?= isset($user['tier']) ? h($user['tier']['m_tier_id']) : '' ?>"
                            >
                                <td><?= h($user['u_user_id']) ?></td>
                                <td><?= h($user['last_name']) ?></td>
                                <td><?= h($user['first_name']) ?></td>
                                <td>
                                    <?php if (isset($user['badge'])): ?>
                                        <?= h($user['badge']['name']) ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isset($user['tier'])): ?>
                                        <span class="dynamic-color-text" data-color="<?= h($user['tier']['color']) ?>">
                                            <?= h($user['tier']['name']) ?>
                                        </span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-edit" type="button" data-user-edit>編集</button>
                                        <button class="btn-delete" type="button" data-user-delete="<?= h($user['u_user_id']) ?>">削除</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">ユーザー情報編集</h3>
                <button class="close" type="button" data-modal-close>&times;</button>
            </div>
            <form id="editForm">
                <input type="hidden" id="editUserId" name="user_id">
                <div class="form-group">
                    <label class="form-label" for="editLastName">姓</label>
                    <input type="text" id="editLastName" name="last_name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="editFirstName">名</label>
                    <input type="text" id="editFirstName" name="first_name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="editBadge">バッジ</label>
                    <select id="editBadge" name="badge_id" class="form-select">
                        <option value="">なし</option>
                        <?php foreach ($pageData['badges'] as $badge): ?>
                            <option value="<?= h($badge['m_badge_id']) ?>"><?= h($badge['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="editTier">ティア</label>
                    <select id="editTier" name="tier_id" class="form-select">
                        <option value="">なし</option>
                        <?php foreach ($pageData['tiers'] as $tier): ?>
                            <option value="<?= h($tier['m_tier_id']) ?>"><?= h($tier['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

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
        'resources/js/admin/user-management.js',
    ]); ?>
</body>
</html>

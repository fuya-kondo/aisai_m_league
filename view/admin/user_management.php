<?php
    require_once __DIR__ . '/../../config/import_file.php';
    $baseUrl = getBaseUrl();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?></title>
    <link rel="stylesheet" href="<?= $baseUrl ?>/resources/css/master.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>/resources/css/header.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .page-title {
            font-size: 1.8rem;
            color: #009944;
            margin: 0;
        }
        .back-link {
            padding: 8px 16px;
            background: #6c757d;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.2s ease;
        }
        .back-link:hover {
            background: #5a6268;
        }
        .user-section {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 20px;
        }
        .section-header {
            background: #009944;
            color: #fff;
            padding: 15px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .section-header:hover {
            background: #007a37;
        }
        .section-header .toggle-icon {
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }
        .section-header.collapsed .toggle-icon {
            transform: rotate(-90deg);
        }
        .section-content {
            max-height: 2000px;
            overflow: hidden;
            transition: all 0.3s ease;
            opacity: 1;
        }
        .section-content.collapsed {
            max-height: 0;
            opacity: 0;
        }
        .table-container {
            overflow-x: auto;
            max-height: 400px;
            overflow-y: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        .btn-edit, .btn-delete {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.2s ease;
        }
        .btn-edit {
            background: #28a745;
            color: #fff;
        }
        .btn-edit:hover {
            background: #218838;
        }
        .btn-delete {
            background: #dc3545;
            color: #fff;
        }
        .btn-delete:hover {
            background: #c82333;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 95%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .modal-title {
            font-size: 1.3rem;
            font-weight: bold;
            color: #333;
        }
        .close {
            font-size: 1.5rem;
            cursor: pointer;
            color: #aaa;
        }
        .close:hover {
            color: #000;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        .form-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            box-sizing: border-box;
            max-width: 100%;
        }
        .form-select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            background: #fff;
            box-sizing: border-box;
            max-width: 100%;
        }
        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }
        .btn-save, .btn-cancel {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.2s ease;
        }
        .btn-save {
            background: #009944;
            color: #fff;
        }
        .btn-save:hover {
            background: #007a37;
        }
        .btn-cancel {
            background: #6c757d;
            color: #fff;
        }
        .btn-cancel:hover {
            background: #5a6268;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            display: none;
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            display: none;
        }
        @media (max-width: 768px) {
            .admin-container {
                padding: 10px;
            }
            .page-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            .table-content {
                font-size: 0.9rem;
            }
            th, td {
                padding: 8px 6px;
            }
            .action-buttons {
                flex-direction: column;
                gap: 4px;
            }
            .btn-edit, .btn-delete {
                padding: 4px 8px;
                font-size: 0.8rem;
            }
            .modal-content {
                width: 88%;
                margin: 2% auto;
                padding: 15px;
            }
            .form-input, .form-select {
                font-size: 16px; /* iOSでズームを防ぐ */
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="page-header">
            <h1 class="page-title">ユーザー管理</h1>
            <a href="?controller=admin&action=top" class="back-link">← 管理画面に戻る</a>
        </div>

        <div class="success-message" id="successMessage"></div>
        <div class="error-message" id="errorMessage"></div>

        <div class="user-section">
            <div class="section-header" onclick="toggleSection(this)">
                <h3 class="section-title">ユーザー一覧 (<?= count($data['users']) ?>件)</h3>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <button class="add-button" onclick="event.stopPropagation(); addUser()">追加</button>
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
                        <?php 
                        $userCount = 0;
                        foreach ($data['users'] as $user): 
                            $userCount++;
                            error_log("Displaying user {$userCount}: ID {$user['u_user_id']}, Name: {$user['last_name']} {$user['first_name']}, Badge: {$user['m_badge_id']}, Tier: {$user['m_tier_id']}");
                        ?>
                             <tr data-user-id="<?= $user['u_user_id'] ?>" 
                                 data-badge-id="<?= isset($user['badge']) ? $user['badge']['m_badge_id'] : '' ?>"
                                 data-tier-id="<?= isset($user['tier']) ? $user['tier']['m_tier_id'] : '' ?>">
                                 <td><?= $user['u_user_id'] ?></td>
                                <td><?= htmlspecialchars($user['last_name']) ?></td>
                                <td><?= htmlspecialchars($user['first_name']) ?></td>
                                <td>
                                    <?php if (isset($user['badge'])): ?>
                                        <?= htmlspecialchars($user['badge']['name']) ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                <?php if (isset($user['tier'])): ?>
                                    <span style="color: <?= $user['tier']['color'] ?>">
                                        <?= htmlspecialchars($user['tier']['name']) ?>
                                    </span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <div class="action-buttons">
                                     <button class="btn-edit" onclick="editUser(<?= $user['u_user_id'] ?>)">編集</button>
                                     <button class="btn-delete" onclick="deleteUser(<?= $user['u_user_id'] ?>)">削除</button>
                                 </div>
                            </td>
                                                 </tr>
                         <?php endforeach; ?>
                         <?php error_log("Total users displayed: {$userCount}"); ?>
                     </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 編集モーダル -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">ユーザー情報編集</h3>
                <span class="close" onclick="closeModal()">&times;</span>
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
                         <?php foreach ($data['badges'] as $badge): ?>
                             <option value="<?= $badge['m_badge_id'] ?>"><?= htmlspecialchars($badge['name']) ?></option>
                         <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="editTier">ティア</label>
                    <select id="editTier" name="tier_id" class="form-select">
                        <option value="">なし</option>
                        <?php foreach ($data['tiers'] as $tier): ?>
                             <option value="<?= $tier['m_tier_id'] ?>"><?= htmlspecialchars($tier['name']) ?></option>
                         <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">キャンセル</button>
                    <button type="submit" class="btn-save">保存</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentUserId = null;

        // アコーディオン機能
        function toggleSection(header) {
            const section = header.parentElement;
            const content = section.querySelector('.section-content');
            const isCollapsed = content.classList.contains('collapsed');
            
            if (isCollapsed) {
                content.classList.remove('collapsed');
                header.classList.remove('collapsed');
            } else {
                content.classList.add('collapsed');
                header.classList.add('collapsed');
            }
        }

        // 初期状態でセクションを折りたたむ
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('.user-section');
            sections.forEach(section => {
                const header = section.querySelector('.section-header');
                const content = section.querySelector('.section-content');
                content.classList.add('collapsed');
                header.classList.add('collapsed');
            });
        });

        function addUser() {
            currentUserId = null;
            document.querySelector('.modal-title').textContent = 'ユーザー追加';
            document.getElementById('editUserId').value = '';
            document.getElementById('editLastName').value = '';
            document.getElementById('editFirstName').value = '';
            document.getElementById('editBadge').value = '';
            document.getElementById('editTier').value = '';
            document.getElementById('editModal').style.display = 'block';
        }

        function editUser(userId) {
            currentUserId = userId;
            const row = document.querySelector(`tr[data-user-id="${userId}"]`);
            const cells = row.cells;
            
            document.getElementById('editUserId').value = userId;
            document.getElementById('editLastName').value = cells[1].textContent;
            document.getElementById('editFirstName').value = cells[2].textContent;
            
            // データ属性からバッジとティアのIDを取得
            const badgeId = row.getAttribute('data-badge-id');
            const tierId = row.getAttribute('data-tier-id');
            
            // バッジの選択値を設定
            const badgeSelect = document.getElementById('editBadge');
            badgeSelect.value = badgeId || '';
            
            // ティアの選択値を設定
            const tierSelect = document.getElementById('editTier');
            tierSelect.value = tierId || '';
            
            document.getElementById('editModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
            currentUserId = null;
        }

        function deleteUser(userId) {
            if (confirm('このユーザーを削除しますか？この操作は取り消せません。')) {
                const formData = new FormData();
                formData.append('action', 'delete_user');
                formData.append('user_id', userId);
                
                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage(data.message || '削除が完了しました', 'success');
                        // 行を削除
                        document.querySelector(`tr[data-user-id="${userId}"]`).remove();
                    } else {
                        showMessage(data.error || '削除に失敗しました', 'error');
                    }
                })
                .catch(error => {
                    showMessage('エラーが発生しました', 'error');
                });
            }
        }

        function showMessage(message, type) {
            const successMsg = document.getElementById('successMessage');
            const errorMsg = document.getElementById('errorMessage');
            
            if (type === 'success') {
                successMsg.textContent = message;
                successMsg.style.display = 'block';
                errorMsg.style.display = 'none';
            } else {
                errorMsg.textContent = message;
                errorMsg.style.display = 'block';
                successMsg.style.display = 'none';
            }
            
            setTimeout(() => {
                successMsg.style.display = 'none';
                errorMsg.style.display = 'none';
            }, 5000);
        }

        // フォーム送信処理
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const isNew = !currentUserId;
            formData.append('action', isNew ? 'add_user' : 'update_user');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const message = data.data ? data.data.message : data.message || '保存が完了しました';
                    showMessage(message, 'success');
                    closeModal();
                    // ページをリロードして最新のデータを表示
                    location.reload();
                } else {
                    const message = data.error || data.message || '保存に失敗しました';
                    showMessage(message, 'error');
                }
            })
            .catch(error => {
                showMessage('エラーが発生しました', 'error');
            });
        });

        // モーダル外クリックで閉じる
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>

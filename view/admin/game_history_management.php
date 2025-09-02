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
        .game-section {
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
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 0.9rem;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .action-buttons {
            display: flex;
            gap: 6px;
        }
        .btn-edit, .btn-delete {
            padding: 4px 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
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
            margin: 2% auto;
            padding: 20px;
            border-radius: 8px;
            width: 95%;
            max-width: 800px;
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
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group.full-width {
            grid-column: 1 / -1;
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
            grid-column: 1 / -1;
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
        .score-inputs {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
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
                font-size: 0.8rem;
            }
            th, td {
                padding: 6px 4px;
            }
            .action-buttons {
                flex-direction: column;
                gap: 3px;
            }
            .btn-edit, .btn-delete {
                padding: 3px 6px;
                font-size: 0.7rem;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
            .score-inputs {
                grid-template-columns: repeat(2, 1fr);
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
            <h1 class="page-title">ゲーム履歴管理</h1>
            <a href="?controller=admin&action=top" class="back-link">← 管理画面に戻る</a>
        </div>

        <div class="success-message" id="successMessage"></div>
        <div class="error-message" id="errorMessage"></div>

        <div class="game-section">
            <div class="section-header" onclick="toggleSection(this)">
                <h3 class="section-title">ゲーム履歴一覧 (<?= isset($data['gameHistory']) ? count($data['gameHistory']) : 0 ?>件)</h3>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <button class="add-button" onclick="event.stopPropagation(); addGame()">追加</button>
                    <span class="toggle-icon">▼</span>
                </div>
            </div>
            <div class="section-content">
                <div class="table-container">
                    <table>
                                                 <thead>
                             <tr>
                                 <th>ID</th>
                                 <th>日付</th>
                                 <th>試合番号</th>
                                 <th>ユーザー</th>
                                 <th>順位</th>
                                 <th>スコア</th>
                                 <th>ポイント</th>
                                 <th>方向</th>
                                 <th>チョンボ回数</th>
                                 <th>操作</th>
                             </tr>
                         </thead>
                        <tbody>
                        <?php if (isset($data['gameHistory']) && is_array($data['gameHistory'])): ?>
                            <?php foreach ($data['gameHistory'] as $game): ?>
                                 <tr data-game-id="<?= $game['u_game_history_id'] ?>">
                                 <td><?= $game['u_game_history_id'] ?></td>
                                 <td><?= htmlspecialchars($game['play_date']) ?></td>
                                 <td><?= $game['game'] ?></td>
                                 <td><?= isset($data['users'][$game['u_user_id']]) ? htmlspecialchars($data['users'][$game['u_user_id']]['last_name'] . $data['users'][$game['u_user_id']]['first_name']) : 'Unknown' ?></td>
                                 <td><?= htmlspecialchars($game['rank']) ?></td>
                                 <td><?= $game['score'] ?></td>
                                 <td><?= $game['point'] ?></td>
                                 <td><?= isset($data['directions'][$game['m_direction_id']]) ? htmlspecialchars($data['directions'][$game['m_direction_id']]['name']) : 'Unknown' ?></td>
                                 <td><?= $game['mistake_count'] ?></td>
                                 <td>
                                     <div class="action-buttons">
                                          <button class="btn-edit" onclick="editGame(<?= $game['u_game_history_id'] ?>)">編集</button>
                                      <button class="btn-delete" onclick="deleteGame(<?= $game['u_game_history_id'] ?>)">削除</button>
                                 </div>
                             </td>
                         </tr>
                        <?php 
                            endforeach; 
                        endif; 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 編集モーダル -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">ゲーム履歴編集</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="editForm">
                <input type="hidden" id="editGameId" name="game_id">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="editPlayDate">プレイ日</label>
                        <input type="date" id="editPlayDate" name="play_date" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="editPlayTime">プレイ時刻</label>
                        <input type="time" id="editPlayTime" name="play_time" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="editGame">試合番号</label>
                        <input type="number" id="editGame" name="game" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="editUser">ユーザー</label>
                        <select id="editUser" name="u_user_id" class="form-select" required>
                            <?php if (isset($data['users']) && is_array($data['users'])): ?>
                                <?php foreach ($data['users'] as $user): ?>
                                    <option value="<?= $user['u_user_id'] ?>"><?= htmlspecialchars($user['last_name'] . $user['first_name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="editRank">順位</label>
                        <input type="text" id="editRank" name="rank" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="editScore">スコア</label>
                        <input type="text" id="editScore" name="score" class="form-input" inputmode="text" pattern="-?[0-9]*\.?[0-9]*" required>
                    </div>
                    <div class="form-group">
                         <label class="form-label" for="editDirection">方向</label>
                         <select id="editDirection" name="m_direction_id" class="form-select" required>
                             <?php foreach ($data['directions'] ?? [] as $direction): ?>
                                 <option value="<?= $direction['m_direction_id'] ?>"><?= htmlspecialchars($direction['name']) ?></option>
                             <?php endforeach; ?>
                         </select>
                     </div>
                     <div class="form-group">
                         <label class="form-label" for="editMistakeCount">チョンボ回数</label>
                         <input type="number" id="editMistakeCount" name="mistake_count" class="form-input" min="0" max="99" value="0">
                     </div>
                 </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">キャンセル</button>
                    <button type="submit" class="btn-save">保存</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentGameId = null;

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
            const sections = document.querySelectorAll('.game-section');
            sections.forEach(section => {
                const header = section.querySelector('.section-header');
                const content = section.querySelector('.section-content');
                content.classList.add('collapsed');
                header.classList.add('collapsed');
            });
        });

        function addGame() {
             currentGameId = null;
             document.querySelector('.modal-title').textContent = 'ゲーム履歴追加';
             document.getElementById('editGameId').value = '';
             document.getElementById('editPlayDate').value = '';
             document.getElementById('editPlayTime').value = '';
             document.getElementById('editGame').value = '';
             document.getElementById('editUser').value = '';
             document.getElementById('editRank').value = '';
             document.getElementById('editScore').value = '';
             document.getElementById('editDirection').value = '';
             document.getElementById('editMistakeCount').value = '0';
             document.getElementById('editModal').style.display = 'block';
         }

        function editGame(gameId) {
            currentGameId = gameId;
            const row = document.querySelector(`tr[data-game-id="${gameId}"]`);
            const cells = row.cells;
            
            // 現在の値をフォームに設定
            document.getElementById('editGameId').value = gameId;
            
            // プレイ日時を設定
            const playDateTime = cells[1].textContent.trim();
            if (playDateTime) {
                // 日付と時刻を分離
                const parts = playDateTime.split(' ');
                if (parts.length >= 2) {
                    // 日付部分を設定
                    document.getElementById('editPlayDate').value = parts[0];
                    // 時刻部分を設定（HH:MM:SS形式からHH:MM形式に変換）
                    const timePart = parts[1];
                    const timeOnly = timePart.substring(0, 5); // HH:MM部分のみ取得
                    document.getElementById('editPlayTime').value = timeOnly;
                } else {
                    // 時刻がない場合は日付のみ設定
                    document.getElementById('editPlayDate').value = parts[0];
                    document.getElementById('editPlayTime').value = '';
                }
            }
            
            // 試合番号を設定
            document.getElementById('editGame').value = cells[2].textContent.trim();
            
            // ユーザー名からIDを逆引きしてセレクトボックスを設定
            const userName = cells[3].textContent.trim();
            setSelectValue('editUser', userName);
            
            // 順位を設定
            document.getElementById('editRank').value = cells[4].textContent.trim();
            
            // スコアを設定
            document.getElementById('editScore').value = cells[5].textContent.trim();
            
             // 方向を設定
             const directionName = cells[7].textContent.trim();
             setSelectValue('editDirection', directionName);
             
             // チョンボ回数を設定
             const mistakeCount = cells[8].textContent.trim();
             document.getElementById('editMistakeCount').value = mistakeCount;
             
             document.getElementById('editModal').style.display = 'block';
        }
        
        function setSelectValue(selectId, displayName) {
            const select = document.getElementById(selectId);
            for (let option of select.options) {
                if (option.textContent.includes(displayName)) {
                    select.value = option.value;
                    break;
                }
            }
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
            currentGameId = null;
        }

        function deleteGame(gameId) {
            if (confirm('このゲーム履歴を削除しますか？この操作は取り消せません。')) {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete_data&type=game_history&id=${gameId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const message = data.data ? data.data.message : data.message || '削除が完了しました';
                        showMessage(message, 'success');
                        // 行を削除
                        document.querySelector(`tr[data-game-id="${gameId}"]`).remove();
                    } else {
                        const message = data.error || data.message || '削除に失敗しました';
                        showMessage(message, 'error');
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
            
            // スコアの数値検証
            const scoreInput = document.getElementById('editScore');
            const scoreValue = scoreInput.value.trim();
            if (scoreValue !== '' && !/^-?\d*\.?\d*$/.test(scoreValue)) {
                alert('スコアには数値のみ入力してください。');
                scoreInput.focus();
                return;
            }
            
            const formData = new FormData(this);
            const isNew = !currentGameId;
            formData.append('action', isNew ? 'add_game_history' : 'update_game_history');
            
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

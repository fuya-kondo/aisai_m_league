<?php
require_once __DIR__ . '/../../config/import_file.php';
$baseUrl = getBaseUrl();
$users = $data['users'] ?? [];
$directions = $data['directions'] ?? [];
$games = $data['gameHistory'] ?? [];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($data['title']) ?></title>
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/master.css">
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/header.css">
    <meta name="csrf-token" content="<?= h(csrf_token()) ?>">
    <style>
        .admin-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .page-title { font-size: 1.8rem; color: #009944; margin: 0; }
        .back-link { padding: 8px 16px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 6px; }
        .game-section { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.1); overflow: hidden; }
        .section-header { background: #009944; color: #fff; padding: 15px; font-weight: bold; display: flex; justify-content: space-between; align-items: center; cursor: pointer; }
        .section-content { max-height: 2000px; overflow: hidden; transition: all .3s ease; opacity: 1; }
        .section-content.collapsed { max-height: 0; opacity: 0; }
        .table-container { overflow: auto; max-height: 500px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border-bottom: 1px solid #eee; font-size: .9rem; }
        th { background: #f8f9fa; }
        .action-buttons { display: flex; gap: 6px; }
        .btn-edit, .btn-delete, .btn-save, .btn-cancel { border: none; border-radius: 4px; cursor: pointer; color: #fff; }
        .btn-edit { background: #28a745; padding: 4px 8px; }
        .btn-delete { background: #dc3545; padding: 4px 8px; }
        .btn-save { background: #009944; padding: 10px 20px; }
        .btn-cancel { background: #6c757d; padding: 10px 20px; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,.5); }
        .modal-content { background: #fff; margin: 2% auto; padding: 20px; border-radius: 8px; width: 95%; max-width: 980px; max-height: 90vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .participant-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .participant-card { border: 1px solid #ddd; border-radius: 8px; padding: 10px; }
        .participant-card h4 { margin: 0 0 8px; color: #009944; }
        .form-group { margin-bottom: 8px; }
        .form-label { display: block; margin-bottom: 4px; font-size: .85rem; }
        .form-input, .form-select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .header-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 12px; }
        .form-actions { margin-top: 14px; display: flex; gap: 10px; justify-content: flex-end; }
        .success-message, .error-message { padding: 10px; border-radius: 4px; margin-bottom: 15px; display: none; }
        .success-message { background: #d4edda; color: #155724; }
        .error-message { background: #f8d7da; color: #721c24; }
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
            <h3>ゲーム履歴一覧 (<?= h(count($games)) ?>件)</h3>
            <div style="display:flex; gap:8px; align-items:center;">
                <button class="add-button" onclick="event.stopPropagation(); addGame()">追加</button><span>▼</span>
            </div>
        </div>
        <div class="section-content">
            <div class="table-container">
                <table>
                    <thead>
                    <tr>
                        <th>GameID</th><th>日付</th><th>半荘</th><th>卓</th><th>参加者(4人)</th><th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($games as $game): ?>
                        <?php
                        $participantsText = [];
                        foreach ($game['participants'] as $p) {
                            $userName = isset($users[$p['playerId']]) ? $users[$p['playerId']]['last_name'] . $users[$p['playerId']]['first_name'] : 'Unknown';
                            $seatName = isset($directions[$p['seat']]) ? $directions[$p['seat']]['name'] : '席' . $p['seat'];
                            $participantsText[] = sprintf('%s:%s/%s位/%s点/%spt/チョンボ%s', $seatName, $userName, $p['rank'], $p['score'], $p['point'], $p['chombo']);
                        }
                        ?>
                        <tr data-game-id="<?= h($game['game_id']) ?>">
                            <td><?= h($game['game_id']) ?></td>
                            <td><?= h($game['play_date']) ?></td>
                            <td><?= h($game['game']) ?></td>
                            <td><?= h($game['u_table_id']) ?></td>
                            <td><?= h(implode(' | ', $participantsText)) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-edit" onclick="editGame('<?= h($game['game_id']) ?>')">編集</button>
                                    <button class="btn-delete" onclick="deleteGame('<?= h($game['game_id']) ?>')">削除</button>
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
            <h3 class="modal-title">ゲーム履歴編集</h3>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <form id="editForm">
            <?= csrf_field() ?>
            <input type="hidden" id="editGameId" name="game_id">
            <div class="header-grid">
                <div class="form-group"><label class="form-label" for="editPlayDate">プレイ日</label><input type="date" id="editPlayDate" name="play_date" class="form-input" required></div>
                <div class="form-group"><label class="form-label" for="editPlayTime">プレイ時刻</label><input type="time" id="editPlayTime" name="play_time" class="form-input" required></div>
                <div class="form-group"><label class="form-label" for="editGame">半荘目</label><input type="number" id="editGame" name="game" class="form-input" required></div>
            </div>

            <div class="participant-grid">
                <?php for ($seat = 1; $seat <= 4; $seat++): ?>
                    <div class="participant-card">
                        <h4><?= h($directions[$seat]['name'] ?? ('席'.$seat)) ?></h4>
                        <input type="hidden" name="participants[<?= $seat ?>][seat]" value="<?= $seat ?>">
                        <div class="form-group">
                            <label class="form-label">プレイヤー</label>
                            <select class="form-select" id="p<?= $seat ?>Player" name="participants[<?= $seat ?>][playerId]" required>
                                <option value="">-</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= h($user['u_user_id']) ?>"><?= h($user['last_name'] . $user['first_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group"><label class="form-label">順位</label><input class="form-input" id="p<?= $seat ?>Rank" name="participants[<?= $seat ?>][rank]" required></div>
                        <div class="form-group"><label class="form-label">スコア</label><input class="form-input" id="p<?= $seat ?>Score" name="participants[<?= $seat ?>][score]" required></div>
                        <div class="form-group"><label class="form-label">チョンボ</label><input class="form-input" id="p<?= $seat ?>Chombo" type="number" min="0" max="99" name="participants[<?= $seat ?>][chombo]" value="0"></div>
                    </div>
                <?php endfor; ?>
            </div>

            <div class="form-actions"><button type="button" class="btn-cancel" onclick="closeModal()">キャンセル</button><button type="submit" class="btn-save">保存</button></div>
        </form>
    </div>
</div>

<script>
const csrfToken = "<?= h(csrf_token()) ?>";
const gameMap = <?= json_encode($games, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
const gameIndex = {};
gameMap.forEach(g => { gameIndex[g.game_id] = g; });
let currentGameId = null;

function toggleSection(header) {
    const content = header.parentElement.querySelector('.section-content');
    content.classList.toggle('collapsed');
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.section-content').forEach(el => el.classList.add('collapsed'));
});

function addGame() {
    currentGameId = null;
    document.querySelector('.modal-title').textContent = 'ゲーム履歴追加';
    document.getElementById('editForm').reset();
    document.getElementById('editGameId').value = '';
    document.getElementById('editModal').style.display = 'block';
}

function editGame(gameId) {
    currentGameId = gameId;
    const game = gameIndex[gameId];
    if (!game) return;
    document.querySelector('.modal-title').textContent = 'ゲーム履歴編集';
    document.getElementById('editGameId').value = gameId;

    const parts = String(game.play_date).split(' ');
    document.getElementById('editPlayDate').value = parts[0] || '';
    document.getElementById('editPlayTime').value = parts[1] ? parts[1].substring(0,5) : '';
    document.getElementById('editGame').value = game.game;

    [1,2,3,4].forEach(seat => {
        const participant = (game.participants || []).find(p => Number(p.seat) === seat);
        document.getElementById(`p${seat}Player`).value = participant ? participant.playerId : '';
        document.getElementById(`p${seat}Rank`).value = participant ? participant.rank : '';
        document.getElementById(`p${seat}Score`).value = participant ? participant.score : '';
        document.getElementById(`p${seat}Chombo`).value = participant ? participant.chombo : 0;
    });

    document.getElementById('editModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
    currentGameId = null;
}

function validateParticipants() {
    for (let seat = 1; seat <= 4; seat++) {
        const player = document.getElementById(`p${seat}Player`).value;
        const rank = document.getElementById(`p${seat}Rank`).value.trim();
        const score = document.getElementById(`p${seat}Score`).value.trim();
        if (!player || !rank || score === '') {
            alert('4人分すべてのプレイヤー・順位・スコアを入力してください。');
            return false;
        }
        if (!/^-?\d+$/.test(score)) {
            alert('スコアは整数で入力してください。');
            return false;
        }
    }
    return true;
}

function deleteGame(gameId) {
    if (!confirm('このゲーム履歴を削除しますか？')) return;
    fetch('', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=delete_data&type=game_history&id=${encodeURIComponent(gameId)}&csrf_token=${encodeURIComponent(csrfToken)}`
    }).then(r => r.json()).then(data => {
        if (data.success) location.reload();
        else showMessage(data.error || '削除に失敗しました', 'error');
    }).catch(() => showMessage('エラーが発生しました', 'error'));
}

function showMessage(message, type) {
    const s = document.getElementById('successMessage');
    const e = document.getElementById('errorMessage');
    if (type === 'success') { s.textContent = message; s.style.display = 'block'; e.style.display = 'none'; }
    else { e.textContent = message; e.style.display = 'block'; s.style.display = 'none'; }
}

document.getElementById('editForm').addEventListener('submit', function(ev) {
    ev.preventDefault();
    if (!validateParticipants()) return;

    const formData = new FormData(this);
    formData.append('action', currentGameId ? 'update_game_history' : 'add_game_history');

    fetch('', {method: 'POST', headers: {'X-CSRF-Token': csrfToken}, body: formData})
        .then(r => r.json())
        .then(data => {
            if (data.success) location.reload();
            else showMessage(data.error || '保存に失敗しました', 'error');
        })
        .catch(() => showMessage('エラーが発生しました', 'error'));
});

window.onclick = function(event) {
    if (event.target === document.getElementById('editModal')) closeModal();
};
</script>
</body>
</html>

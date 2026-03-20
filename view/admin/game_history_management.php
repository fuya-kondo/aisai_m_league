<?php
/**
 * 対局履歴管理画面。
 * 履歴一覧、削除、モーダル経由の追加・編集をまとめて扱う。
 */
$adminPageTitle = 'ゲーム履歴管理';
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
    <div class="admin-container admin-game-history">
        <?php include __DIR__ . '/partials/common/page_header.php'; ?>
        <?php include __DIR__ . '/partials/common/feedback.php'; ?>

        <div class="game-section">
            <div class="section-header" data-section-toggle>
                <h3 class="section-title">ゲーム履歴一覧 (<?= h(isset($pageData['gameHistory']) ? count($pageData['gameHistory']) : 0) ?>件)</h3>
                <div class="section-header__actions">
                    <button class="add-button" type="button" data-game-add>追加</button>
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
                        <?php if (isset($pageData['gameHistory']) && is_array($pageData['gameHistory'])): ?>
                            <?php foreach ($pageData['gameHistory'] as $game): ?>
                                <?php
                                    $playDateTime = trim((string)$game['play_date']);
                                    $parts = preg_split('/\s+/', $playDateTime);
                                    $playDate = $parts[0] ?? '';
                                    $playTime = isset($parts[1]) ? substr($parts[1], 0, 5) : '';
                                ?>
                                <tr
                                    data-game-id="<?= h($game['u_game_history_id']) ?>"
                                    data-play-date="<?= h($playDate) ?>"
                                    data-play-time="<?= h($playTime) ?>"
                                    data-game="<?= h($game['game']) ?>"
                                    data-user-id="<?= h($game['u_user_id']) ?>"
                                    data-rank="<?= h($game['rank']) ?>"
                                    data-score="<?= h($game['score']) ?>"
                                    data-direction-id="<?= h($game['m_direction_id']) ?>"
                                    data-mistake-count="<?= h($game['mistake_count']) ?>"
                                >
                                    <td><?= h($game['u_game_history_id']) ?></td>
                                    <td><?= h($game['play_date']) ?></td>
                                    <td><?= h($game['game']) ?></td>
                                    <td><?= isset($pageData['users'][$game['u_user_id']]) ? h($pageData['users'][$game['u_user_id']]['last_name'] . $pageData['users'][$game['u_user_id']]['first_name']) : 'Unknown' ?></td>
                                    <td><?= h($game['rank']) ?></td>
                                    <td><?= h($game['score']) ?></td>
                                    <td><?= h($game['point']) ?></td>
                                    <td><?= isset($pageData['directions'][$game['m_direction_id']]) ? h($pageData['directions'][$game['m_direction_id']]['name']) : 'Unknown' ?></td>
                                    <td><?= h($game['mistake_count']) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-edit" type="button" data-game-edit>編集</button>
                                            <button class="btn-delete" type="button" data-game-delete="<?= h($game['u_game_history_id']) ?>">削除</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
                <button class="close" type="button" data-modal-close>&times;</button>
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
                            <?php if (isset($pageData['users']) && is_array($pageData['users'])): ?>
                                <?php foreach ($pageData['users'] as $user): ?>
                                    <option value="<?= h($user['u_user_id']) ?>"><?= h($user['last_name'] . $user['first_name']) ?></option>
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
                            <?php foreach ($pageData['directions'] ?? [] as $direction): ?>
                                <option value="<?= h($direction['m_direction_id']) ?>"><?= h($direction['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="editMistakeCount">チョンボ回数</label>
                        <input type="number" id="editMistakeCount" name="mistake_count" class="form-input" min="0" max="99" value="0">
                    </div>
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
        'resources/js/admin/game-history.js',
    ]); ?>
</body>
</html>

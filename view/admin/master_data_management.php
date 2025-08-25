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
        .master-sections {
             display: flex;
             flex-direction: column;
            gap: 20px;
        }
        .master-section {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
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
            padding: 15px;
            opacity: 1;
        }
        .section-content.collapsed {
            max-height: 0;
            padding: 0 15px;
            opacity: 0;
        }
        
        .section-title {
            margin: 0;
        }
        .table-container {
            overflow-x: auto;
            max-height: 400px;
            overflow-y: auto;
        }
        .master-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }
        .master-table th,
        .master-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 0.9rem;
        }
        .master-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .action-buttons {
            display: flex;
            gap: 4px;
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
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
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
        }
        .form-color {
            width: 60px;
            height: 40px;
            padding: 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
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
        .color-preview {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 8px;
            vertical-align: middle;
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
            .master-sections {
                grid-template-columns: 1fr;
            }
            .master-table th,
            .master-table td {
                padding: 6px 4px;
                font-size: 0.8rem;
            }
            .action-buttons {
                flex-direction: column;
                gap: 3px;
            }
            .btn-edit, .btn-delete {
                padding: 3px 6px;
                font-size: 0.7rem;
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
            <h1 class="page-title">マスターデータ管理</h1>
            <a href="?controller=admin&action=top" class="back-link">← 管理画面に戻る</a>
        </div>

        <div class="success-message" id="successMessage"></div>
        <div class="error-message" id="errorMessage"></div>

        <div class="master-sections">
            <!-- バッジ管理 -->
            <div class="master-section">
                 <div class="section-header" onclick="toggleSection(this)">
                    <h3 class="section-title">バッジ管理</h3>
                     <div style="display: flex; align-items: center; gap: 10px;">
                         <button class="add-button" onclick="event.stopPropagation(); addMasterData('badge')">追加</button>
                         <span class="toggle-icon">▼</span>
                     </div>
                </div>
                <div class="section-content">
                      <div class="table-container">
                    <table class="master-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>名前</th>
                                <th>画像</th>
                                <th>枠</th>
                                <th>背景</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['badges'] as $badge): ?>
                                   <tr data-id="<?= $badge['m_badge_id'] ?>" data-type="badge">
                                       <td><?= $badge['m_badge_id'] ?></td>
                                <td><?= htmlspecialchars($badge['name']) ?></td>
                                <td><?= htmlspecialchars($badge['image']) ?></td>
                                <td><?= htmlspecialchars($badge['flame']) ?></td>
                                <td><?= htmlspecialchars($badge['background']) ?></td>
                                <td>
                                    <div class="action-buttons">
                                               <button class="btn-edit" onclick="editMasterData('badge', <?= $badge['m_badge_id'] ?>)">編集</button>
                                               <button class="btn-delete" onclick="deleteMasterData('badge', <?= $badge['m_badge_id'] ?>)">削除</button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                      </div>
                </div>
            </div>

            <!-- ティア管理 -->
            <div class="master-section">
                 <div class="section-header" onclick="toggleSection(this)">
                    <h3 class="section-title">ティア管理</h3>
                     <div style="display: flex; align-items: center; gap: 10px;">
                         <button class="add-button" onclick="event.stopPropagation(); addMasterData('tier')">追加</button>
                         <span class="toggle-icon">▼</span>
                     </div>
                </div>
                <div class="section-content">
                      <div class="table-container">
                    <table class="master-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>名前</th>
                                <th>色</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['tiers'] as $tier): ?>
                                   <tr data-id="<?= $tier['m_tier_id'] ?>" data-type="tier">
                                       <td><?= $tier['m_tier_id'] ?></td>
                                <td><?= htmlspecialchars($tier['name']) ?></td>
                                <td>
                                    <span class="color-preview" style="background-color: <?= $tier['color'] ?>"></span>
                                    <?= $tier['color'] ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                               <button class="btn-edit" onclick="editMasterData('tier', <?= $tier['m_tier_id'] ?>)">編集</button>
                                               <button class="btn-delete" onclick="deleteMasterData('tier', <?= $tier['m_tier_id'] ?>)">削除</button>
                                          </div>
                                      </td>
                                  </tr>
                                  <?php endforeach; ?>
                              </tbody>
                          </table>
                      </div>
                  </div>
             </div>

             <!-- ゲーム日管理 -->
             <div class="master-section">
                 <div class="section-header" onclick="toggleSection(this)">
                     <h3 class="section-title">ゲーム日管理</h3>
                     <div style="display: flex; align-items: center; gap: 10px;">
                         <button class="add-button" onclick="event.stopPropagation(); addMasterData('game_day')">追加</button>
                         <span class="toggle-icon">▼</span>
                     </div>
                 </div>
                 <div class="section-content">
                     <div class="table-container">
                         <table class="master-table">
                             <thead>
                                 <tr>
                                     <th>ID</th>
                                     <th>日付</th>
                                     <th>時間</th>
                                     <th>操作</th>
                                 </tr>
                             </thead>
                             <tbody>
                             <?php foreach ($data['gameDays'] as $gameDay): ?>
                                  <tr data-id="<?= $gameDay['game_day'] ?>" data-type="game_day">
                                      <td><?= $gameDay['game_day'] ?></td>
                                     <td><?= htmlspecialchars($gameDay['game_day']) ?></td>
                                     <td>-</td>
                                     <td>
                                         <div class="action-buttons">
                                              <button class="btn-edit" onclick="editMasterData('game_day', '<?= $gameDay['game_day'] ?>')">編集</button>
                                              <button class="btn-delete" onclick="deleteMasterData('game_day', '<?= $gameDay['game_day'] ?>')">削除</button>
                                         </div>
                                     </td>
                                 </tr>
                                 <?php endforeach; ?>
                             </tbody>
                         </table>
                     </div>
                 </div>
             </div>

             <!-- ティア履歴管理 -->
             <div class="master-section">
                 <div class="section-header" onclick="toggleSection(this)">
                     <h3 class="section-title">ティア履歴管理</h3>
                     <div style="display: flex; align-items: center; gap: 10px;">
                         <button class="add-button" onclick="event.stopPropagation(); addMasterData('tier_history')">追加</button>
                         <span class="toggle-icon">▼</span>
                     </div>
                 </div>
                 <div class="section-content">
                     <div class="table-container">
                         <table class="master-table">
                             <thead>
                                 <tr>
                                     <th>ID</th>
                                     <th>ユーザーID</th>
                                     <th>ティアID</th>
                                     <th>年</th>
                                     <th>操作</th>
                                 </tr>
                             </thead>
                             <tbody>
                             <?php foreach ($data['tierHistory'] as $tierHistory): ?>
                                  <tr data-id="<?= $tierHistory['u_user_tier_history_id'] ?>" data-type="tier_history">
                                      <td><?= $tierHistory['u_user_tier_history_id'] ?></td>
                                     <td><?= htmlspecialchars($tierHistory['u_user_id']) ?></td>
                                     <td><?= htmlspecialchars($tierHistory['m_tier_id']) ?></td>
                                     <td><?= htmlspecialchars($tierHistory['year']) ?></td>
                                     <td>
                                         <div class="action-buttons">
                                              <button class="btn-edit" onclick="editMasterData('tier_history', <?= $tierHistory['u_user_tier_history_id'] ?>)">編集</button>
                                              <button class="btn-delete" onclick="deleteMasterData('tier_history', <?= $tierHistory['u_user_tier_history_id'] ?>)">削除</button>
                                         </div>
                                     </td>
                                 </tr>
                                 <?php endforeach; ?>
                             </tbody>
                         </table>
                     </div>
                 </div>
             </div>

             <!-- 方向管理 -->
             <div class="master-section">
                 <div class="section-header" onclick="toggleSection(this)">
                     <h3 class="section-title">方向管理</h3>
                     <div style="display: flex; align-items: center; gap: 10px;">
                         <button class="add-button" onclick="event.stopPropagation(); addMasterData('direction')">追加</button>
                         <span class="toggle-icon">▼</span>
                     </div>
                 </div>
                 <div class="section-content">
                     <div class="table-container">
                         <table class="master-table">
                             <thead>
                                 <tr>
                                     <th>ID</th>
                                     <th>名前</th>
                                     <th>操作</th>
                                 </tr>
                             </thead>
                             <tbody>
                             <?php foreach ($data['directions'] as $direction): ?>
                                  <tr data-id="<?= $direction['m_direction_id'] ?>" data-type="direction">
                                      <td><?= $direction['m_direction_id'] ?></td>
                                     <td><?= htmlspecialchars($direction['name']) ?></td>
                                     <td>
                                         <div class="action-buttons">
                                              <button class="btn-edit" onclick="editMasterData('direction', <?= $direction['m_direction_id'] ?>)">編集</button>
                                              <button class="btn-delete" onclick="deleteMasterData('direction', <?= $direction['m_direction_id'] ?>)">削除</button>
                                         </div>
                                     </td>
                                 </tr>
                                 <?php endforeach; ?>
                             </tbody>
                         </table>
                     </div>
                 </div>
             </div>

             <!-- グループ管理 -->
             <div class="master-section">
                 <div class="section-header" onclick="toggleSection(this)">
                     <h3 class="section-title">グループ管理</h3>
                     <div style="display: flex; align-items: center; gap: 10px;">
                         <button class="add-button" onclick="event.stopPropagation(); addMasterData('group')">追加</button>
                         <span class="toggle-icon">▼</span>
                     </div>
                 </div>
                 <div class="section-content">
                     <div class="table-container">
                         <table class="master-table">
                             <thead>
                                 <tr>
                                     <th>ID</th>
                                     <th>名前</th>
                                     <th>ルールID</th>
                                     <th>操作</th>
                                 </tr>
                             </thead>
                             <tbody>
                             <?php foreach ($data['groups'] as $group): ?>
                                  <tr data-id="<?= $group['m_group_id'] ?>" data-type="group">
                                      <td><?= $group['m_group_id'] ?></td>
                                     <td><?= htmlspecialchars($group['name']) ?></td>
                                     <td><?= $group['m_rule_id'] ?></td>
                                     <td>
                                         <div class="action-buttons">
                                              <button class="btn-edit" onclick="editMasterData('group', <?= $group['m_group_id'] ?>)">編集</button>
                                              <button class="btn-delete" onclick="deleteMasterData('group', <?= $group['m_group_id'] ?>)">削除</button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                     </div>
                </div>
            </div>

             <!-- ルール管理 -->
            <div class="master-section">
                 <div class="section-header" onclick="toggleSection(this)">
                     <h3 class="section-title">ルール管理</h3>
                     <div style="display: flex; align-items: center; gap: 10px;">
                         <button class="add-button" onclick="event.stopPropagation(); addMasterData('rule')">追加</button>
                         <span class="toggle-icon">▼</span>
                     </div>
                </div>
                <div class="section-content">
                     <div class="table-container">
                    <table class="master-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>名前</th>
                                <th>開始点数</th>
                                <th>終了点数</th>
                                <th>1位ウマオカ</th>
                                <th>2位ウマオカ</th>
                                <th>3位ウマオカ</th>
                                <th>4位ウマオカ</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                             <?php foreach ($data['rules'] as $rule): ?>
                                  <tr data-id="<?= $rule['m_rule_id'] ?>" data-type="rule">
                                      <td><?= $rule['m_rule_id'] ?></td>
                                     <td><?= htmlspecialchars($rule['name']) ?></td>
                                     <td><?= $rule['start_score'] ?></td>
                                     <td><?= $rule['end_score'] ?></td>
                                     <td><?= $rule['point_1'] ?></td>
                                     <td><?= $rule['point_2'] ?></td>
                                     <td><?= $rule['point_3'] ?></td>
                                     <td><?= $rule['point_4'] ?></td>
                                     <td>
                                         <div class="action-buttons">
                                              <button class="btn-edit" onclick="editMasterData('rule', <?= $rule['m_rule_id'] ?>)">編集</button>
                                              <button class="btn-delete" onclick="deleteMasterData('rule', <?= $rule['m_rule_id'] ?>)">削除</button>
                                         </div>
                                </td>
                                 </tr>
                                 <?php endforeach; ?>
                             </tbody>
                         </table>
                     </div>
                 </div>
             </div>

             <!-- 設定管理 -->
             <div class="master-section">
                 <div class="section-header" onclick="toggleSection(this)">
                     <h3 class="section-title">設定管理</h3>
                     <div style="display: flex; align-items: center; gap: 10px;">
                         <button class="add-button" onclick="event.stopPropagation(); addMasterData('setting')">追加</button>
                         <span class="toggle-icon">▼</span>
                     </div>
                 </div>
                 <div class="section-content">
                     <div class="table-container">
                         <table class="master-table">
                             <thead>
                                 <tr>
                                     <th>ID</th>
                                     <th>名前</th>
                                     <th>値</th>
                                     <th>操作</th>
                                 </tr>
                             </thead>
                             <tbody>
                             <?php foreach ($data['settings'] as $setting): ?>
                                  <tr data-id="<?= $setting['m_setting_id'] ?>" data-type="setting">
                                      <td><?= $setting['m_setting_id'] ?></td>
                                     <td><?= htmlspecialchars($setting['name']) ?></td>
                                     <td><?= htmlspecialchars($setting['value']) ?></td>
                                <td>
                                    <div class="action-buttons">
                                              <button class="btn-edit" onclick="editMasterData('setting', <?= $setting['m_setting_id'] ?>)">編集</button>
                                              <button class="btn-delete" onclick="deleteMasterData('setting', <?= $setting['m_setting_id'] ?>)">削除</button>
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
    </div>

    <!-- 編集モーダル -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">マスターデータ編集</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="editForm" novalidate>
                <input type="hidden" id="editId" name="id">
                <input type="hidden" id="editTable" name="table">
                 
                 <!-- バッジ用フォーム -->
                 <div id="badgeForm" style="display: none;">
                     <div class="form-group">
                         <label class="form-label" for="badgeName">名前</label>
                         <input type="text" id="badgeName" name="name" class="form-input" required>
                     </div>
                     <div class="form-group">
                         <label class="form-label" for="badgeImage">画像</label>
                         <input type="text" id="badgeImage" name="image" class="form-input">
                     </div>
                     <div class="form-group">
                         <label class="form-label" for="badgeFlame">枠</label>
                         <input type="text" id="badgeFlame" name="flame" class="form-input">
                     </div>
                     <div class="form-group">
                         <label class="form-label" for="badgeBackground">背景</label>
                         <input type="text" id="badgeBackground" name="background" class="form-input">
                     </div>
                 </div>
                 
                 <!-- ティア用フォーム -->
                 <div id="tierForm" style="display: none;">
                <div class="form-group">
                    <label class="form-label" for="tierName">名前</label>
                    <input type="text" id="tierName" name="name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="tierColor">色</label>
                    <input type="color" id="tierColor" name="color" class="form-color" required>
                </div>
                 </div>
                 
                                   <!-- ゲーム日用フォーム -->
                  <div id="gameDayForm" style="display: none;">
                      <div class="form-group">
                          <label class="form-label" for="gameDayDate">日付</label>
                          <input type="date" id="gameDayDate" name="game_day" class="form-input" required>
                      </div>
                  </div>
                 
                                   <!-- ティア履歴用フォーム -->
                  <div id="tierHistoryForm" style="display: none;">
                      <div class="form-group">
                          <label class="form-label" for="tierHistoryUserId">ユーザーID</label>
                          <input type="number" id="tierHistoryUserId" name="u_user_id" class="form-input" required>
                      </div>
                      <div class="form-group">
                          <label class="form-label" for="tierHistoryTierId">ティアID</label>
                          <input type="number" id="tierHistoryTierId" name="m_tier_id" class="form-input" required>
                      </div>
                      <div class="form-group">
                          <label class="form-label" for="tierHistoryYear">年</label>
                          <input type="number" id="tierHistoryYear" name="year" class="form-input" min="2020" max="2030" required>
                      </div>
                  </div>

                  <!-- 方向用フォーム -->
                  <div id="directionForm" style="display: none;">
                      <div class="form-group">
                          <label class="form-label" for="directionName">名前</label>
                          <input type="text" id="directionName" name="name" class="form-input" required>
                      </div>
                  </div>

                  <!-- グループ用フォーム -->
                  <div id="groupForm" style="display: none;">
                      <div class="form-group">
                          <label class="form-label" for="groupName">名前</label>
                          <input type="text" id="groupName" name="name" class="form-input" required>
                      </div>
                      <div class="form-group">
                          <label class="form-label" for="groupRuleId">ルールID</label>
                          <input type="number" id="groupRuleId" name="m_rule_id" class="form-input" min="0">
                      </div>
                  </div>

                  <!-- ルール用フォーム -->
                  <div id="ruleForm" style="display: none;">
                      <div class="form-group">
                          <label class="form-label" for="ruleName">名前</label>
                          <input type="text" id="ruleName" name="name" class="form-input" required>
                      </div>
                      <div class="form-group">
                          <label class="form-label" for="ruleStartScore">開始点数</label>
                          <input type="number" id="ruleStartScore" name="start_score" class="form-input" min="0">
                      </div>
                      <div class="form-group">
                          <label class="form-label" for="ruleEndScore">終了点数</label>
                          <input type="number" id="ruleEndScore" name="end_score" class="form-input" min="0">
                      </div>
                      <div class="form-group">
                          <label class="form-label" for="rulePoint1">1位ウマオカ</label>
                          <input type="number" id="rulePoint1" name="point_1" class="form-input">
                      </div>
                      <div class="form-group">
                          <label class="form-label" for="rulePoint2">2位ウマオカ</label>
                          <input type="number" id="rulePoint2" name="point_2" class="form-input">
                      </div>
                      <div class="form-group">
                          <label class="form-label" for="rulePoint3">3位ウマオカ</label>
                          <input type="number" id="rulePoint3" name="point_3" class="form-input">
                      </div>
                      <div class="form-group">
                          <label class="form-label" for="rulePoint4">4位ウマオカ</label>
                          <input type="number" id="rulePoint4" name="point_4" class="form-input">
                      </div>
                  </div>

                  <!-- 設定用フォーム -->
                  <div id="settingForm" style="display: none;">
                      <div class="form-group">
                          <label class="form-label" for="settingName">名前</label>
                          <input type="text" id="settingName" name="name" class="form-input" required>
                      </div>
                      <div class="form-group">
                          <label class="form-label" for="settingValue">値</label>
                          <input type="number" id="settingValue" name="value" class="form-input" required>
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
        let currentEditData = null;
        let originalRequiredFields = [];
        let hiddenForms = [];

        // 非表示のフォームフィールドからrequired属性を一時的に削除
        function disableHiddenRequiredFields() {
            originalRequiredFields = [];
            const allForms = ['badgeForm', 'tierForm', 'gameDayForm', 'tierHistoryForm', 
                            'directionForm', 'groupForm', 'ruleForm', 'settingForm'];
            
            allForms.forEach(formId => {
                const form = document.getElementById(formId);
                if (form) {
                    const allFields = form.querySelectorAll('input, select, textarea');
                    allFields.forEach(field => {
                        originalRequiredFields.push({
                            field: field, 
                            hadRequired: field.hasAttribute('required'),
                            wasDisabled: field.disabled
                        });
                        
                        if (form.style.display === 'none') {
                            // 非表示のフォームの全フィールドを無効化
                            field.removeAttribute('required');
                            field.disabled = true;
                        } else {
                            // 表示されているフォームのフィールドを有効化
                            field.disabled = false;
                        }
                    });
                }
            });
        }

        // フィールドの状態を復元
        function restoreRequiredFields() {
            originalRequiredFields.forEach(item => {
                if (item.hadRequired) {
                    item.field.setAttribute('required', '');
                }
                item.field.disabled = item.wasDisabled;
            });
            originalRequiredFields = [];
        }

        // 安全にフィールドの値を取得
        function getFieldValue(form, fieldName, defaultValue = '') {
            const field = form.querySelector(`[name="${fieldName}"]`);
            return field ? field.value : defaultValue;
        }

        // 安全に数値フィールドの値を取得
        function getNumberFieldValue(form, fieldName, defaultValue = 0) {
            const field = form.querySelector(`[name="${fieldName}"]`);
            return field ? (parseInt(field.value) || defaultValue) : defaultValue;
        }

        // 表示されているフォームの必須フィールドをバリデーション
        function validateVisibleForm(visibleForm) {
            const requiredFields = visibleForm.querySelectorAll('[required]');
            let isValid = true;
            let firstInvalidField = null;

            requiredFields.forEach(field => {
                if (!field.disabled && !field.value.trim()) {
                    field.style.borderColor = '#ff0000';
                    isValid = false;
                    if (!firstInvalidField) {
                        firstInvalidField = field;
                    }
                } else {
                    field.style.borderColor = '';
                }
            });

            if (!isValid && firstInvalidField) {
                firstInvalidField.focus();
                showMessage('必須項目を入力してください', 'error');
            }

            return isValid;
        }

        // 非表示のフォームを一時的に削除し、required属性も削除
        function removeHiddenForms() {
            hiddenForms = [];
            const allForms = ['badgeForm', 'tierForm', 'gameDayForm', 'tierHistoryForm', 
                            'directionForm', 'groupForm', 'ruleForm', 'settingForm'];
            
            allForms.forEach(formId => {
                const form = document.getElementById(formId);
                if (form && form.style.display === 'none') {
                    // 非表示のフォームのrequired属性を削除
                    const requiredFields = form.querySelectorAll('[required]');
                    requiredFields.forEach(field => {
                        field.setAttribute('data-originally-required', 'true');
                        field.removeAttribute('required');
                    });
                    
                    // 非表示のフォームを一時的に削除
                    hiddenForms.push({form: form, parent: form.parentNode, nextSibling: form.nextSibling});
                    form.parentNode.removeChild(form);
                }
            });
        }

        // 削除したフォームを復元し、required属性も復元
        function restoreHiddenForms() {
            hiddenForms.forEach(item => {
                if (item.nextSibling) {
                    item.parent.insertBefore(item.form, item.nextSibling);
                } else {
                    item.parent.appendChild(item.form);
                }
                
                // required属性を復元（元々requiredだったフィールドに）
                const requiredFields = item.form.querySelectorAll('input, select, textarea');
                requiredFields.forEach(field => {
                    if (field.hasAttribute('data-originally-required')) {
                        field.setAttribute('required', '');
                    }
                });
            });
            hiddenForms = [];
        }

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

         // 初期状態で全てのセクションを折りたたむ
         document.addEventListener('DOMContentLoaded', function() {
             const sections = document.querySelectorAll('.master-section');
             sections.forEach(section => {
                 const header = section.querySelector('.section-header');
                 const content = section.querySelector('.section-content');
                 content.classList.add('collapsed');
                 header.classList.add('collapsed');
             });
         });

        function addMasterData(type) {
            currentEditData = { type: type, isNew: true };
            document.getElementById('modalTitle').textContent = getTypeDisplayName(type) + '追加';
            document.getElementById('editId').value = '';
            document.getElementById('editTable').value = type;
             
             // フォームの表示/非表示を制御
             showFormByType(type);
             
             // フォームをリセット
             resetFormByType(type);
             
            document.getElementById('editModal').style.display = 'block';
        }

        function editMasterData(type, id) {
            currentEditData = { type: type, id: id, isNew: false };
            const row = document.querySelector(`tr[data-type="${type}"][data-id="${id}"]`);
            const cells = row.cells;
            
            document.getElementById('modalTitle').textContent = getTypeDisplayName(type) + '編集';
            document.getElementById('editId').value = id;
            document.getElementById('editTable').value = type;
             
             // フォームの表示/非表示を制御
             showFormByType(type);
             
             // データをフォームに設定
             setFormDataByType(type, cells);
            
            document.getElementById('editModal').style.display = 'block';
         }

                   function showFormByType(type) {
              // 全てのフォームを非表示
              document.getElementById('badgeForm').style.display = 'none';
              document.getElementById('tierForm').style.display = 'none';
              document.getElementById('gameDayForm').style.display = 'none';
              document.getElementById('tierHistoryForm').style.display = 'none';
              document.getElementById('directionForm').style.display = 'none';
              document.getElementById('groupForm').style.display = 'none';
              document.getElementById('ruleForm').style.display = 'none';
              document.getElementById('settingForm').style.display = 'none';
              
              // 該当するフォームを表示
              switch (type) {
                  case 'badge':
                      document.getElementById('badgeForm').style.display = 'block';
                      break;
                  case 'tier':
                      document.getElementById('tierForm').style.display = 'block';
                      break;
                  case 'game_day':
                      document.getElementById('gameDayForm').style.display = 'block';
                      break;
                  case 'tier_history':
                      document.getElementById('tierHistoryForm').style.display = 'block';
                      break;
                  case 'direction':
                      document.getElementById('directionForm').style.display = 'block';
                      break;
                  case 'group':
                      document.getElementById('groupForm').style.display = 'block';
                      break;
                  case 'rule':
                      document.getElementById('ruleForm').style.display = 'block';
                      break;
                  case 'setting':
                      document.getElementById('settingForm').style.display = 'block';
                      break;
              }
          }

                   function resetFormByType(type) {
              switch (type) {
                  case 'badge':
                      document.getElementById('badgeName').value = '';
                      document.getElementById('badgeImage').value = '';
                      document.getElementById('badgeFlame').value = '';
                      document.getElementById('badgeBackground').value = '';
                      break;
                  case 'tier':
                      document.getElementById('tierName').value = '';
                      document.getElementById('tierColor').value = '#000000';
                      break;
                  case 'game_day':
                      document.getElementById('gameDayDate').value = '';
                      break;
                  case 'tier_history':
                      document.getElementById('tierHistoryUserId').value = '';
                      document.getElementById('tierHistoryTierId').value = '';
                      document.getElementById('tierHistoryYear').value = '';
                      break;
                  case 'direction':
                      document.getElementById('directionName').value = '';
                      break;
                  case 'group':
                      document.getElementById('groupName').value = '';
                      document.getElementById('groupRuleId').value = '';
                      break;
                  case 'rule':
                      document.getElementById('ruleName').value = '';
                      document.getElementById('ruleStartScore').value = '';
                      document.getElementById('ruleEndScore').value = '';
                      document.getElementById('rulePoint1').value = '';
                      document.getElementById('rulePoint2').value = '';
                      document.getElementById('rulePoint3').value = '';
                      document.getElementById('rulePoint4').value = '';
                      break;
                  case 'setting':
                      document.getElementById('settingName').value = '';
                      document.getElementById('settingValue').value = '';
                      break;
              }
          }

                   function setFormDataByType(type, cells) {
              switch (type) {
                  case 'badge':
                      document.getElementById('badgeName').value = cells[1].textContent;
                      document.getElementById('badgeImage').value = cells[2].textContent;
                      document.getElementById('badgeFlame').value = cells[3].textContent;
                      document.getElementById('badgeBackground').value = cells[4].textContent;
                      break;
                  case 'tier':
                      document.getElementById('tierName').value = cells[1].textContent;
                      const colorPreview = cells[2].querySelector('.color-preview');
                      const color = colorPreview ? colorPreview.style.backgroundColor : '#000000';
                      document.getElementById('tierColor').value = color;
                      break;
                  case 'game_day':
                      document.getElementById('gameDayDate').value = cells[1].textContent;
                      break;
                  case 'tier_history':
                      document.getElementById('tierHistoryUserId').value = cells[1].textContent;
                      document.getElementById('tierHistoryTierId').value = cells[2].textContent;
                      document.getElementById('tierHistoryYear').value = cells[3].textContent;
                      break;
                  case 'direction':
                      document.getElementById('directionName').value = cells[1].textContent;
                      break;
                  case 'group':
                      document.getElementById('groupName').value = cells[1].textContent;
                      document.getElementById('groupRuleId').value = cells[2].textContent;
                      break;
                  case 'rule':
                      document.getElementById('ruleName').value = cells[1].textContent;
                      document.getElementById('ruleStartScore').value = cells[2].textContent;
                      document.getElementById('ruleEndScore').value = cells[3].textContent;
                      document.getElementById('rulePoint1').value = cells[4].textContent;
                      document.getElementById('rulePoint2').value = cells[5].textContent;
                      document.getElementById('rulePoint3').value = cells[6].textContent;
                      document.getElementById('rulePoint4').value = cells[7].textContent;
                      break;
                  case 'setting':
                      document.getElementById('settingName').value = cells[1].textContent;
                      document.getElementById('settingValue').value = cells[2].textContent;
                      break;
              }
        }

        function getTypeDisplayName(type) {
            switch (type) {
                case 'badge': return 'バッジ';
                case 'tier': return 'ティア';
                  case 'game_day': return 'ゲーム日';
                  case 'tier_history': return 'ティア履歴';
                  case 'direction': return '方向';
                  case 'group': return 'グループ';
                  case 'rule': return 'ルール';
                  case 'setting': return '設定';
                default: return 'データ';
            }
        }

        function closeModal() {
            // required属性を復元
            restoreRequiredFields();
            document.getElementById('editModal').style.display = 'none';
            currentEditData = null;
        }

        function deleteMasterData(type, id) {
            const typeName = getTypeDisplayName(type);
            if (confirm(`${typeName}を削除しますか？この操作は取り消せません。`)) {
                  const formData = new FormData();
                  formData.append('action', 'delete_data');
                  formData.append('type', type);
                  formData.append('id', id);
                  
                fetch('', {
                    method: 'POST',
                      body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const message = data.data ? data.data.message : data.message || '削除が完了しました';
                        showMessage(message, 'success');
                        // 行を削除
                        document.querySelector(`tr[data-type="${type}"][data-id="${id}"]`).remove();
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
            
            // 非表示のフォームフィールドからrequired属性を一時的に削除
            disableHiddenRequiredFields();
            
            // 表示されているフォームのみからデータを取得
            const typeElement = document.getElementById('editTable');
            const idElement = document.getElementById('editId');
            
            if (!typeElement || !idElement) {
                showMessage('フォーム要素が見つかりません', 'error');
                restoreRequiredFields();
                return;
            }
            
            const type = typeElement.value;
            const id = idElement.value;
            
            // 表示されているフォームを特定
            let visibleForm = null;
            const allForms = ['badgeForm', 'tierForm', 'gameDayForm', 'tierHistoryForm', 
                            'directionForm', 'groupForm', 'ruleForm', 'settingForm'];
            
            for (const formId of allForms) {
                const form = document.getElementById(formId);
                if (form && form.style.display !== 'none') {
                    visibleForm = form;
                    break;
                }
            }
            
            if (!visibleForm) {
                showMessage('表示されているフォームが見つかりません', 'error');
                return;
            }

            // 表示されているフォームの必須フィールドをバリデーション
            if (!validateVisibleForm(visibleForm)) {
                // バリデーションエラーの場合、required属性を復元して終了
                restoreRequiredFields();
                return;
            }
            
            // 表示されているフォームのフィールドからデータを構築
            const data = {};
            
            if (type === 'badge') {
                data.name = getFieldValue(visibleForm, 'name');
                data.image = getFieldValue(visibleForm, 'image');
                data.flame = getFieldValue(visibleForm, 'flame');
                data.background = getFieldValue(visibleForm, 'background');
            } else if (type === 'tier') {
                data.name = getFieldValue(visibleForm, 'name');
                data.color = getFieldValue(visibleForm, 'color');
            } else if (type === 'game_day') {
                data.game_day = getFieldValue(visibleForm, 'game_day');
            } else if (type === 'tier_history') {
                data.u_user_id = getNumberFieldValue(visibleForm, 'u_user_id');
                data.m_tier_id = getNumberFieldValue(visibleForm, 'm_tier_id');
                data.year = getFieldValue(visibleForm, 'year');
            } else if (type === 'direction') {
                data.name = getFieldValue(visibleForm, 'name');
            } else if (type === 'group') {
                data.name = getFieldValue(visibleForm, 'name');
                data.m_rule_id = getNumberFieldValue(visibleForm, 'm_rule_id');
            } else if (type === 'rule') {
                data.name = getFieldValue(visibleForm, 'name');
                data.start_score = getNumberFieldValue(visibleForm, 'start_score');
                data.end_score = getNumberFieldValue(visibleForm, 'end_score');
                data.point_1 = getNumberFieldValue(visibleForm, 'point_1');
                data.point_2 = getNumberFieldValue(visibleForm, 'point_2');
                data.point_3 = getNumberFieldValue(visibleForm, 'point_3');
                data.point_4 = getNumberFieldValue(visibleForm, 'point_4');
            } else if (type === 'setting') {
                data.name = getFieldValue(visibleForm, 'name');
                data.value = getNumberFieldValue(visibleForm, 'value');
            }
              
              // 新規追加か編集かを判定
              const isNew = !id || id === '';
              const action = isNew ? 'add_master_data' : 'update_master_data';
              
              const postData = new FormData();
              postData.append('action', action);
              postData.append('type', type);
              if (!isNew) {
                  postData.append('id', id);
              }
              postData.append('data', JSON.stringify(data));
            
            fetch('', {
                method: 'POST',
                  body: postData
            })
            .then(response => response.json())
            .then(data => {
                // required属性を復元
                restoreRequiredFields();
                
                if (data.success) {
                    const message = data.data ? data.data.message : data.message || '操作が完了しました';
                    showMessage(message, 'success');
                    closeModal();
                    // ページをリロードして最新のデータを表示
                    location.reload();
                } else {
                    const message = data.error || data.message || 'エラーが発生しました';
                    showMessage(message, 'error');
                }
            })
            .catch(error => {
                // required属性を復元
                restoreRequiredFields();
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

<?php
// Include necessary files
require_once __DIR__ . '/../config/import_file.php';
// Include header
include '../webroot/common/header.php';

// Handling POST requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $historyId = $_POST['historyId'] ?? null;
    $userId = $_POST['userId'] ?? null;
    if ($historyId !== null && $userId !== null) {
        try {
            deleteData($historyId);
            header("Location: history?userId=" . urlencode($userId));
            exit();
        } catch (Exception $e) {
            $error_msg = '削除処理中にエラーが発生しました: ' . $e->getMessage();
        }
    } else {
        $error_msg = '必要なパラメータが不足しています';
    }
}

// Get parameter
if( isset( $_GET['userId'] ) ) $selectUser = $_GET['userId'];
$selectYear = date("Y");
if( isset(  $_GET['year']  ) ) $selectYear = $_GET['year'];

// ページネーションの設定
if (isset($_GET['userId'])) {
    $records_per_page = 15; // 1ページあたりの表示件数
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // 現在のページ番号
    $offset = ($current_page - 1) * $records_per_page; // オフセット計算
    // 対象レコードの合計数を計算
    $total_records = 0;
    foreach($uGameHistoryList[$selectUser] as $data) {
        if(date('Y', strtotime($data['play_date'])) == $selectYear || date('Y', strtotime($data['play_date'])) == $selectYear - 1) {
            $total_records++;
        }
    }
    // 総ページ数を計算
    $total_pages = ceil($total_records / $records_per_page);
    // 表示するレコードをフィルタリングして配列に格納
    $filtered_data = [];
    foreach($uGameHistoryList[$selectUser] as $data) {
        if(date('Y', strtotime($data['play_date'])) == $selectYear || date('Y', strtotime($data['play_date'])) == $selectYear - 1) {
            $filtered_data[] = $data;
        }
    }
    // 現在のページに表示するレコードのみを抽出
    $paginated_data = array_slice($filtered_data, $offset, $records_per_page);
}

// ページネーションの設定
$dates_per_page = 1; // 1ページあたりの表示日数
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// 日付の配列を取得
$all_dates = array_keys($gameHistoryDataList);
$total_dates = count($all_dates);
$total_pages_game = ceil($total_dates / $dates_per_page);

// 現在のページに表示する日付の範囲を計算
$start_index = ($current_page - 1) * $dates_per_page;
$current_dates = array_slice($all_dates, $start_index, $dates_per_page);

// Set title
$title = '履歴';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="../webroot/css/master.css">
    <link rel="stylesheet" href="../webroot/css/header.css">
    <link rel="stylesheet" href="../webroot/css/button.css">
    <link rel="stylesheet" href="../webroot/css/table.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;700&display=swap" rel="stylesheet">
    <title><?= $title; ?></title>
</head>
<body>
    <main>
        <?php if (!isset($selectUser)): ?>
            <div class="page-title">個人<?= $title; ?></div>
            <div class="select-button-container">
                <form action="history" method="get">
                    <?php foreach($userList as $userId => $userData): ?>
                        <button class="select-button" type="submit" name="userId" value="<?=$userId?>"><?=$userData[0]['last_name'].$userData[0]['first_name']?></button>
                    <?php endforeach; ?>
                </form>
            </div>

            <div class="page-title">対局履歴</div>
            <!-- ページネーションナビゲーション（上部） -->
            <div class="pagination-nav">
                <div class="pagination-controls">
                    <?php if($total_pages_game > 1): ?>
                        <?php if($current_page > 1): ?>
                            <a href="?page=1" class="page-link">最初</a>
                            <a href="?page=<?=$current_page-1?>" class="page-link">前へ</a>
                        <?php endif; ?>
                        <?php
                        // ページリンクの表示（現在のページの前後2ページずつ表示）
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages_game, $current_page + 2);
                        for($i = $start_page; $i <= $end_page; $i++): ?>
                            <a href="?page=<?=$i?>" class="page-link <?=$i == $current_page ? 'active' : ''?>"><?=$i?></a>
                        <?php endfor; ?>
                        <?php if($current_page < $total_pages_game): ?>
                            <a href="?page=<?=$current_page+1?>" class="page-link">次へ</a>
                            <a href="?page=<?=$total_pages_game?>" class="page-link">最後</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="page-info">
                    <?=$total_dates?>日分中 <?=$start_index+1?>～<?=min($start_index+$dates_per_page, $total_dates)?>日目表示 (<?=$current_page?>/<?=$total_pages_game?>ページ)
                </div>
            </div>
            <!-- 対戦記録の表示 -->
            <div class="game-history-container">
                <?php foreach($current_dates as $date): ?>
                    <div class="game-date">
                        <h4 class="date-header"><?=$date?></h4>
                        <?php foreach($gameHistoryDataList[$date] as $game => $gameData): ?>
                            <div class="game-session">
                                <h5 class="game-header"><?=$game?>半荘目</h5>
                                <table class="result-table">
                                    <tbody>
                                        <?php
                                        $sum_score = 0;
                                        $sum_rank = 0;
                                        $check_flag = true;
                                        // rankでソート
                                        usort($gameData, function($a, $b) {
                                            $rankA = $a['rank'];
                                            $rankB = $b['rank'];
                                            if (strlen($rankA) == 3) {
                                                $rankA = mb_substr($rankA, 0, 1); // マルチバイト文字に対応
                                            }
                                            if (strlen($rankB) == 3) {
                                                $rankB = mb_substr($rankB, 0, 1); // マルチバイト文字に対応
                                            }
                                            return $rankA <=> $rankB;
                                        });
                                        foreach($gameData as $historyData):
                                        if (strlen($historyData['rank']) == 3) $check_flag = false; // マルチバイト文字に対応
                                        if ($check_flag) {
                                            $sum_score += ($historyData['score']);
                                            $sum_rank += $historyData['rank'];
                                        }
                                        ?>
                                            <tr class="player-row rank-<?=$historyData['rank']?>">
                                                <?php if(isset($mDirectionList[$historyData['m_direction_id']])): ?>
                                                    <td class="direction-cell"><?=$mDirectionList[$historyData['m_direction_id']]?></td>
                                                <?php endif;?>
                                                <td class="rank-cell"><?=$historyData['rank']?></td>
                                                <td class="name-cell"><?=$userList[$historyData['u_user_id']][0]['last_name']?></td>
                                                <td class="score-cell"><?=number_format($historyData['score'])?></td>
                                                <td class="point-cell"><?=$historyData['point']?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <?php if($check_flag): ?>
                                        <?php if($sum_score != 100000): ?>
                                            <span style="background-color: red; color:white; padding:3px">点数が正しくないです</span><br>
                                        <?php endif; ?>
                                        <?php if($sum_rank != 10): ?>
                                            <span style="background-color: red; color:white; padding:3px">順位が正しくないです</span><br>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- ページネーションナビゲーション（下部） -->
            <div class="pagination-nav">
                <div class="pagination-controls">
                    <?php if($total_pages_game > 1): ?>
                        <?php if($current_page > 1): ?>
                            <a href="?page=1" class="page-link">最初</a>
                            <a href="?page=<?=$current_page-1?>" class="page-link">前へ</a>
                        <?php endif; ?>
                        <?php
                        // ページリンクの表示（現在のページの前後2ページずつ表示）
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages_game, $current_page + 2);
                        for($i = $start_page; $i <= $end_page; $i++): ?>
                            <a href="?page=<?=$i?>" class="page-link <?=$i == $current_page ? 'active' : ''?>"><?=$i?></a>
                        <?php endfor; ?>
                        <?php if($current_page < $total_pages_game): ?>
                            <a href="?page=<?=$current_page+1?>" class="page-link">次へ</a>
                            <a href="?page=<?=$total_pages_game?>" class="page-link">最後</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="page-info">
                    <?=$total_dates?>日分中 <?=$start_index+1?>～<?=min($start_index+$dates_per_page, $total_dates)?>日目表示 (<?=$current_page?>/<?=$total_pages_game?>ページ)
                </div>
            </div>
        <?php else: ?>
            <div class="page-title"><?=$userList[$selectUser][0]['last_name'].$userList[$selectUser][0]['first_name']?>の履歴</div>
            <div class="pagination">
                <?php if($total_pages > 1): ?>
                    <div class="pagination-controls">
                        <?php if($current_page > 1): ?>
                            <a href="?page=1&year=<?=$selectYear?>&userId=<?=$selectUser?>" class="page-link">最初</a>
                            <a href="?page=<?=$current_page-1?>&year=<?=$selectYear?>&userId=<?=$selectUser?>" class="page-link">前へ</a>
                        <?php endif; ?>
                        <?php
                            // ページリンクの表示（現在のページの前後2ページずつ表示）
                            $start_page = max(1, $current_page - 2);
                            $end_page = min($total_pages, $current_page + 2);
                        ?>
                        <?php for($i = $start_page; $i <= $end_page; $i++): ?>
                            <a href="?page=<?=$i?>&year=<?=$selectYear?>&userId=<?=$selectUser?>" class="page-link <?=$i == $current_page ? 'active' : ''?>"><?=$i?></a>
                        <?php endfor; ?>
                        <?php if($current_page < $total_pages): ?>
                            <a href="?page=<?=$current_page+1?>&year=<?=$selectYear?>&userId=<?=$selectUser?>" class="page-link">次へ</a>
                            <a href="?page=<?=$total_pages?>&year=<?=$selectYear?>&userId=<?=$selectUser?>" class="page-link">最後</a>
                        <?php endif; ?>
                    </div>
                    <div class="page-info">
                        <?=$total_records?>件中 <?=($offset+1)?>-<?=min($offset+$records_per_page, $total_records)?>件表示 (<?=$current_page?>/<?=$total_pages?>ページ)
                    </div>
                <?php endif; ?>
            </div>
            <div class="table-container">
                <div class="table-wrapper">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>順位</th><th>点数</th><th>Pts</th><th>日時</th><th>半荘数</th><th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($paginated_data as $data):?>
                                <?php
                                    $data['rank_display'] = mb_strlen($data['rank']) == 3 ? "同率".substr($data['rank'], 0,1) : $data['rank'];
                                ?>
                                <tr>
                                    <td class="<?= $data['rank_display'] == 4 ? 'red-text' : '' ?>"><?=$data['rank_display']?></td>
                                    <td class="<?= $data['score'] < 0 ? 'red-text' : '' ?>"><?=$data['score']?></td>
                                    <td class="<?= $data['point'] < 0 ? 'red-text' : '' ?>"><?=$data['point']?></td>
                                    <td><?=date('Y/m/d', strtotime($data['play_date']))?></td>
                                    <td><?=$data['game']?></td>
                                    <td>
                                        <form action="update" method="post" class="inline-form">
                                            <input type="hidden" name="userId" value="<?=$data['u_user_id']?>">
                                            <input type="hidden" name="rank" value="<?=$data['rank']?>">
                                            <input type="hidden" name="score" value="<?=$data['score']?>">
                                            <input type="hidden" name="game" value="<?=$data['game']?>">
                                            <input type="hidden" name="direction" value="<?=$data['m_direction_id']?>">
                                            <button type="submit" name="historyId" value="<?=$data['u_game_history_id']?>" class="action-button edit-button">修正</button>
                                        </form>
                                        <form action="history" method="post" onSubmit="return check(<?=$data['rank']?>,<?=$data['score']?>,<?=$data['point']?>)" class="inline-form">
                                            <input type="hidden" name="userId" value="<?=$data['u_user_id']?>">
                                            <button type="submit" name="historyId" value="<?=$data['u_game_history_id']?>" class="action-button delete-button">削除</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <?php if($total_pages > 1): ?>
                        <div class="pagination-controls">
                            <?php if($current_page > 1): ?>
                                <a href="?page=1&year=<?=$selectYear?>&userId=<?=$selectUser?>" class="page-link">最初</a>
                                <a href="?page=<?=$current_page-1?>&year=<?=$selectYear?>&userId=<?=$selectUser?>" class="page-link">前へ</a>
                            <?php endif; ?>
                            <?php
                                // ページリンクの表示（現在のページの前後2ページずつ表示）
                                $start_page = max(1, $current_page - 2);
                                $end_page = min($total_pages, $current_page + 2);
                            ?>
                            <?php for($i = $start_page; $i <= $end_page; $i++): ?>
                                <a href="?page=<?=$i?>&year=<?=$selectYear?>&userId=<?=$selectUser?>" class="page-link <?=$i == $current_page ? 'active' : ''?>"><?=$i?></a>
                            <?php endfor; ?>

                            <?php if($current_page < $total_pages): ?>
                                <a href="?page=<?=$current_page+1?>&year=<?=$selectYear?>&userId=<?=$selectUser?>" class="page-link">次へ</a>
                                <a href="?page=<?=$total_pages?>&year=<?=$selectYear?>&userId=<?=$selectUser?>" class="page-link">最後</a>
                            <?php endif; ?>
                        </div>
                        <div class="page-info">
                            <?=$total_records?>件中 <?=($offset+1)?>-<?=min($offset+$records_per_page, $total_records)?>件表示 (<?=$current_page?>/<?=$total_pages?>ページ)
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif;?>
    </main>
</body>
</html>

<script>
    function check(rank, score, point) {
        if (window.confirm(rank + '位\n' + score + '点\n' + point + 'Pts\n' + '削除しますか？')) {
            return true;
        } else {
            window.alert('キャンセルされました');
            return false;
        }
    }
</script>

<style>
    .table-container {
        background-color: #fff;
        border-radius: 4px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .table-wrapper {
        overflow-x: auto;
        padding: 15px;
    }
    .history-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .history-table th,
    .history-table td {
        padding: 12px;
        text-align: center;
    }
    .history-table th {
        background-color: #009944;
        color: white;
        font-weight: bold;
    }
    .history-table td {
        border-bottom: 1px solid #e0e0e0;
    }
    .history-table tr:last-child td {
        border-bottom: none;
    }
    .red-text {
        color: #e74c3c;
    }
    .inline-form {
        display: inline-block;
        margin: 0 5px;
    }
    .action-button {
        padding: 8px 12px;
        font-size: 14px;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    .edit-button {
        background-color: #2ecc71;
        margin-bottom: 3px;
    }
    .edit-button:hover {
        background-color: #27ae60;
    }
    .delete-button {
        background-color: #e74c3c;
        margin-bottom: 3px;
    }
    .delete-button:hover {
        background-color: #c0392b;
    }
    @media screen and (max-width: 768px) {
        .history-table th,
        .history-table td {
            padding: 10px 8px;
            font-size: 0.85em;
        }
        .action-button {
            padding: 6px 10px;
            font-size: 12px;
        }
    }
    .pagination {
        margin-top: 20px;
        text-align: center;
    }
    .pagination-controls {
        margin-bottom: 10px;
    }
    .page-link {
        display: inline-block;
        padding: 5px 10px;
        margin: 0 3px;
        border: 1px solid #ddd;
        background-color: #f8f8f8;
        color: #333;
        text-decoration: none;
        border-radius: 3px;
    }
    .page-link:hover {
        background-color: #e9e9e9;
    }
    .page-link.active {
        background-color: #4CAF50;
        color: white;
        border-color: #4CAF50;
    }
    .pagination-nav {
        margin: 20px 0;
        text-align: center;
    }
    .page-info {
        color: #666;
        font-size: 0.9em;
        margin-bottom: 15px;
    }
    .game-history-container {
        border: 1px solid #eaeaea;
        border-radius: 5px;
        padding: 15px;
        background-color: #fbfbfb;
    }
    .game-date {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px dashed #ccc;
    }
    .game-date:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    .date-header {
        background-color: #f0f0f0;
        padding: 8px 12px;
        border-radius: 4px;
        margin-bottom: 15px;
        font-size: 1.1em;
        color: #333;
    }
    .game-session {
        margin-bottom: 20px;
    }
    .game-session:last-child {
        margin-bottom: 0;
    }
    .game-header {
        margin: 10px 0;
        font-size: 1em;
        color: #555;
        padding-left: 10px;
        border-left: 3px solid #4CAF50;
    }
    .result-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    .result-table th {
        background-color: #f5f5f5;
        padding: 8px;
        text-align: left;
        border: 1px solid #ddd;
    }
    .result-table td {
        padding: 8px;
        border: 1px solid #ddd;
    }
    .rank-1 {
        background-color: #98d98e;
    }
    .rank-4 {
        background-color: #f6bfbc;
    }
    .score-cell, .point-cell {
        text-align: right;
    }
    .point-cell {
        font-weight: bold;
    }
</style>
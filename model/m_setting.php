<?php

/**
 * 設定（m_setting）一覧を取得し、IDをキーに整形します。
 */

// クエリ定義
$mSettingSql = 'SELECT * FROM `m_setting`;';

try {
    // DB接続
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    // 問い合わせ実行
    $mSettingStatement = $pdo->query($mSettingSql);
    $mSettingList = $mSettingStatement->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}

// IDをキーに整形
$settingRows = $mSettingList;
$mSettingList = [];
foreach ($settingRows as $settingRow) {
    $mSettingList[$settingRow['m_setting_id']]['name'] = $settingRow['name'];
    $mSettingList[$settingRow['m_setting_id']]['value'] = $settingRow['value'];
}

/**
 * データを更新
 *
 * @param int $settingId
 * @param int $enableFlag
 * @return bool
 */
function switchMode(int $settingId): bool {
    try {
        $dbConfig = getDatabaseConfig();
        $db = Database::getInstance($dbConfig);
        $pdo = $db->getConnection();

        // 1. 現在のvalueの値を取得
        $sqlSelect = 'SELECT `value` FROM `m_setting` WHERE `m_setting_id` = :m_setting_id';
        $stmtSelect = $pdo->prepare($sqlSelect);
        $stmtSelect->bindParam(':m_setting_id', $settingId, PDO::PARAM_INT);
        $stmtSelect->execute();
        $currentFlag = $stmtSelect->fetchColumn(); // valueの値を取得

        // 現在の値に基づいて次の値を決定
        $changeMode = ($currentFlag == 1) ? 0 : 1;

        // 2. valueを更新
        $sqlUpdate = 'UPDATE `m_setting` SET `value` = :value WHERE `m_setting_id` = :m_setting_id';
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':value', $changeMode, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':m_setting_id', $settingId, PDO::PARAM_INT);

        return $stmtUpdate->execute();

    } catch (Exception $e) {
        error_log('データ更新エラー: ' . $e->getMessage());
        return false;
    }
}
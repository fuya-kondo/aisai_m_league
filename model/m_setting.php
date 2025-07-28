<?php

// ----------------------------------------------------------------------
// データ取得
// ----------------------------------------------------------------------
$mSettingSql = 'SELECT * FROM `m_setting`;';

try {
    $dbConfig = getDatabaseConfig();
    $db = Database::getInstance($dbConfig);
    $pdo = $db->getConnection();
    $mSettingSth = $pdo->query($mSettingSql);
    $mSettingList = $mSettingSth->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    exit($e->getMessage());
}
$tmp = $mSettingList;
$mSettingList = [];
foreach ($tmp as $key => $value) {
    $mSettingList[$value['m_setting_id']] = $value;
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

        // 1. 現在のenable_flagの値を取得
        $sqlSelect = 'SELECT `enable_flag` FROM `m_setting` WHERE `m_setting_id` = :m_setting_id';
        $stmtSelect = $pdo->prepare($sqlSelect);
        $stmtSelect->bindParam(':m_setting_id', $settingId, PDO::PARAM_INT);
        $stmtSelect->execute();
        $currentFlag = $stmtSelect->fetchColumn(); // enable_flagの値を取得

        // 現在の値に基づいて次の値を決定
        $changeMode = ($currentFlag == 1) ? 0 : 1;

        // 2. enable_flagを更新
        $sqlUpdate = 'UPDATE `m_setting` SET `enable_flag` = :enable_flag WHERE `m_setting_id` = :m_setting_id';
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':enable_flag', $changeMode, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':m_setting_id', $settingId, PDO::PARAM_INT);

        return $stmtUpdate->execute();

    } catch (Exception $e) {
        error_log('データ更新エラー: ' . $e->getMessage());
        return false;
    }
}
<?php

/**
 * 設定マスターモデルクラス
 * 設定情報の取得、更新、削除を行う
 */
class MSetting
{
    private $db;
    private $table_name = 'm_setting';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * すべてのデータを取得
     */
    public function getAllData()
    {
        try {
            $sql = 'SELECT * FROM '.$this->table_name;
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($this->table_name.'取得エラー: ' . $e->getMessage());
            return [];
        }

        // IDをキーに整形
        $result = [];
        foreach ($data as $value) {
            $result[$value[$this->table_name.'_id']] = $value;
        }

        return $result;
    }

    /**
     * 設定の切り替え
     */
    public function switchMode($settingId)
    {
        try {
            // 現在の設定を取得
            $sql = 'SELECT value FROM '.$this->table_name.' WHERE '.$this->table_name.'_id = ?';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$settingId]);
            $currentSetting = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$currentSetting) {
                throw new Exception('設定が見つかりません');
            }

            // 値を反転（0→1、1→0）
            $newValue = $currentSetting['value'] == 1 ? 0 : 1;

            // 設定を更新
            $sql = 'UPDATE '.$this->table_name.' SET value = ? WHERE '.$this->table_name.'_id = ?';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$newValue, $settingId]);

            return true;
        } catch (Exception $e) {
            error_log('設定切り替えエラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 設定を追加
     */
    public function addSetting($data)
    {
        try {
            $sql = 'INSERT INTO m_setting (name, value) VALUES (:name, :value)';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':value', $data['value']);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('設定追加エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 設定を更新
     */
    public function updateSetting($id, $data)
    {
        try {
            $sql = 'UPDATE m_setting SET name = :name, value = :value WHERE m_setting_id = :id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':value', $data['value']);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('設定更新エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 設定を削除
     */
    public function deleteSetting($id)
    {
        try {
            $sql = 'DELETE FROM m_setting WHERE m_setting_id = :id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('設定削除エラー: ' . $e->getMessage());
            throw $e;
        }
    }
}
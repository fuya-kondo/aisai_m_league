<?php

/**
 * グループマスターモデルクラス
 * グループ情報の取得、更新、削除を行う
 */
class MGroup
{
    private $db;
    private $table_name = 'm_group';

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
     * グループを追加
     */
    public function addGroup($data)
    {
        try {
            $sql = 'INSERT INTO m_group (name, m_rule_id) VALUES (:name, :m_rule_id)';
            $stmt = $this->db->prepare($sql);
            
            // 変数に代入してからbindParamに渡す
            $name = $data['name'];
            $m_rule_id = $data['m_rule_id'] ?? 0;
            
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':m_rule_id', $m_rule_id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('グループ追加エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * グループを更新
     */
    public function updateGroup($id, $data)
    {
        try {
            $sql = 'UPDATE m_group SET name = :name, m_rule_id = :m_rule_id WHERE m_group_id = :id';
            $stmt = $this->db->prepare($sql);
            
            // 変数に代入してからbindParamに渡す
            $name = $data['name'];
            $m_rule_id = $data['m_rule_id'] ?? 0;
            
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':m_rule_id', $m_rule_id);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('グループ更新エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * グループを削除
     */
    public function deleteGroup($id)
    {
        try {
            $sql = 'DELETE FROM m_group WHERE m_group_id = :id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('グループ削除エラー: ' . $e->getMessage());
            throw $e;
        }
    }
}

<?php

/**
 * ルールマスターモデルクラス
 * ルール情報の取得、更新、削除を行う
 */
class MRule
{
    private $db;
    private $table_name = 'm_rule';

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
     * ルールを追加
     */
    public function addRule($data)
    {
        try {
            $sql = 'INSERT INTO m_rule (name, start_score, end_score, point_1, point_2, point_3, point_4) 
                    VALUES (:name, :start_score, :end_score, :point_1, :point_2, :point_3, :point_4)';
            $stmt = $this->db->prepare($sql);
            
            // 変数に代入してからbindParamに渡す
            $name = $data['name'];
            $start_score = $data['start_score'] ?? 0;
            $end_score = $data['end_score'] ?? 0;
            $point_1 = $data['point_1'] ?? 0;
            $point_2 = $data['point_2'] ?? 0;
            $point_3 = $data['point_3'] ?? 0;
            $point_4 = $data['point_4'] ?? 0;
            
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':start_score', $start_score);
            $stmt->bindParam(':end_score', $end_score);
            $stmt->bindParam(':point_1', $point_1);
            $stmt->bindParam(':point_2', $point_2);
            $stmt->bindParam(':point_3', $point_3);
            $stmt->bindParam(':point_4', $point_4);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('ルール追加エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ルールを更新
     */
    public function updateRule($id, $data)
    {
        try {
            $sql = 'UPDATE m_rule SET name = :name, start_score = :start_score, end_score = :end_score, 
                    point_1 = :point_1, point_2 = :point_2, point_3 = :point_3, point_4 = :point_4 
                    WHERE m_rule_id = :id';
            $stmt = $this->db->prepare($sql);
            
            // 変数に代入してからbindParamに渡す
            $name = $data['name'];
            $start_score = $data['start_score'] ?? 0;
            $end_score = $data['end_score'] ?? 0;
            $point_1 = $data['point_1'] ?? 0;
            $point_2 = $data['point_2'] ?? 0;
            $point_3 = $data['point_3'] ?? 0;
            $point_4 = $data['point_4'] ?? 0;
            
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':start_score', $start_score);
            $stmt->bindParam(':end_score', $end_score);
            $stmt->bindParam(':point_1', $point_1);
            $stmt->bindParam(':point_2', $point_2);
            $stmt->bindParam(':point_3', $point_3);
            $stmt->bindParam(':point_4', $point_4);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('ルール更新エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ルールを削除
     */
    public function deleteRule($id)
    {
        try {
            $sql = 'DELETE FROM m_rule WHERE m_rule_id = :id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('ルール削除エラー: ' . $e->getMessage());
            throw $e;
        }
    }
}

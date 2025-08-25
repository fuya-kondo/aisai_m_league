<?php

/**
 * 方向マスターモデルクラス
 * 方向情報の取得、更新、削除を行う
 */
class MDirection
{
    private $db;
    private $table_name = 'm_direction';

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
     * 方向を追加
     */
    public function addDirection($data)
    {
        try {
            $sql = 'INSERT INTO m_direction (name) VALUES (:name)';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':name', $data['name']);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('方向追加エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 方向を更新
     */
    public function updateDirection($id, $data)
    {
        try {
            $sql = 'UPDATE m_direction SET name = :name WHERE m_direction_id = :id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('方向更新エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 方向を削除
     */
    public function deleteDirection($id)
    {
        try {
            $sql = 'DELETE FROM m_direction WHERE m_direction_id = :id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('方向削除エラー: ' . $e->getMessage());
            throw $e;
        }
    }
}

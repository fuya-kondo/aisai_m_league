<?php

/**
 * ティアマスターモデルクラス
 * ティア情報の取得、更新、削除を行う
 */
class MTier
{
    private $db;
    private $table_name = 'm_tier';

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
     * ティアを更新
     */
    public function updateTier($tierId, $data)
    {
        try {
            $sql = 'UPDATE m_tier SET 
                        name = :name,
                        color = :color
                    WHERE m_tier_id = :tier_id';
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':color', $data['color']);
            $stmt->bindParam(':tier_id', $tierId);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('ティア更新エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ティアを削除
     */
    public function deleteTier($tierId)
    {
        try {
            $sql = 'DELETE FROM m_tier WHERE m_tier_id = :tier_id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':tier_id', $tierId);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('ティア削除エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ティアを追加
     */
    public function addTier($data)
    {
        try {
            $sql = 'INSERT INTO m_tier (name, color) VALUES (:name, :color)';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':color', $data['color']);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('ティア追加エラー: ' . $e->getMessage());
            throw $e;
        }
    }
}
<?php

/**
 * バッジマスターモデルクラス
 * バッジ情報の取得、更新、削除を行う
 */
class MBadge
{
    private $db;
    private $table_name = 'm_badge';

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
     * バッジを更新
     */
    public function updateBadge($badgeId, $data)
    {
        try {
            $sql = 'UPDATE m_badge
                    SET name = :name, image = :image, flame = :flame, background = :background
                    WHERE m_badge_id = :badge_id';
            
            $stmt = $this->db->prepare($sql);
            
            // 変数に代入してからbindParamに渡す
            $name = $data['name'];
            $image = $data['image'] ?? '';
            $flame = $data['flame'] ?? '';
            $background = $data['background'] ?? '';
            
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':image', $image);
            $stmt->bindParam(':flame', $flame);
            $stmt->bindParam(':background', $background);
            $stmt->bindParam(':badge_id', $badgeId);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('バッジ更新エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * バッジを削除
     */
    public function deleteBadge($badgeId)
    {
        try {
            $sql = 'DELETE FROM m_badge 
                    WHERE m_badge_id = :badge_id';

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':badge_id', $badgeId);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('バッジ削除エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * バッジを追加
     */
    public function addBadge($data)
    {
        try {
            $sql = 'INSERT INTO m_badge (name, image, flame, background) 
                    VALUES (:name, :image, :flame, :background)';

            $stmt = $this->db->prepare($sql);
            
            // 変数に代入してからbindParamに渡す
            $name = $data['name'];
            $image = $data['image'] ?? '';
            $flame = $data['flame'] ?? '';
            $background = $data['background'] ?? '';
            
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':image', $image);
            $stmt->bindParam(':flame', $flame);
            $stmt->bindParam(':background', $background);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('バッジ追加エラー: ' . $e->getMessage());
            throw $e;
        }
    }
}
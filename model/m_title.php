<?php

/**
 * タイトルマスターモデルクラス
 * タイトル情報の取得、更新、削除を行う
 */
class MTitle
{
    private $db;
    private $table_name = 'm_title';

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
     * タイトルを追加
     */
    public function addTitle($data)
    {
        try {
            $sql = 'INSERT INTO m_title (name) VALUES (:name)';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':name', $data['name']);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('タイトル追加エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * タイトルを更新
     */
    public function updateTitle($id, $data)
    {
        try {
            $sql = 'UPDATE m_title SET name = :name WHERE m_title_id = :id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('タイトル更新エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * タイトルを削除
     */
    public function deleteTitle($id)
    {
        try {
            $sql = 'DELETE FROM m_title WHERE m_title_id = :id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('タイトル削除エラー: ' . $e->getMessage());
            throw $e;
        }
    }
}

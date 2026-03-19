<?php
/**
 * 方角マスターテーブル用のデータアクセス層。
 * 東南西北などの席情報を画面や集計で再利用できる形で取得・管理する。
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
     * 方角定義を ID 起点で扱える形に整えて返す。
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

        $result = [];
        foreach ($data as $value) {
            $result[$value[$this->table_name.'_id']] = $value;
        }

        return $result;
    }

    /**
     * 新しい方角定義を追加する。
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
     * 方角名の定義を更新する。
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
     * 指定 ID の方角定義を削除する。
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

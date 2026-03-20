<?php
/**
 * グループマスターテーブル用のデータアクセス層。
 * ルールとの関連を持つグループ定義の取得・更新・追加・削除を担う。
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
     * グループ定義を ID 起点で扱える形に整えて返す。
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
     * ルールとの関連を含むグループ定義を追加する。
     */
    public function addGroup($data)
    {
        try {
            $sql = 'INSERT INTO m_group (name, m_rule_id) VALUES (:name, :m_rule_id)';
            $stmt = $this->db->prepare($sql);

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
     * グループ名と紐づくルールを更新する。
     */
    public function updateGroup($id, $data)
    {
        try {
            $sql = 'UPDATE m_group SET name = :name, m_rule_id = :m_rule_id WHERE m_group_id = :id';
            $stmt = $this->db->prepare($sql);

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
     * 指定 ID のグループ定義を削除する。
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

<?php
/**
 * ルールマスターテーブル用のデータアクセス層。
 * 配点や持ち点など、成績計算に必要なルール設定を永続化する。
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
     * ルール定義を ID 起点で引ける形に整えて返す。
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
     * 点数計算に必要なルール定義を追加する。
     */
    public function addRule($data)
    {
        try {
            $sql = 'INSERT INTO m_rule (name, start_score, end_score, point_1, point_2, point_3, point_4) 
                    VALUES (:name, :start_score, :end_score, :point_1, :point_2, :point_3, :point_4)';
            $stmt = $this->db->prepare($sql);

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
     * 既存ルールの持ち点と順位点を更新する。
     */
    public function updateRule($id, $data)
    {
        try {
            $sql = 'UPDATE m_rule SET name = :name, start_score = :start_score, end_score = :end_score, 
                    point_1 = :point_1, point_2 = :point_2, point_3 = :point_3, point_4 = :point_4 
                    WHERE m_rule_id = :id';
            $stmt = $this->db->prepare($sql);

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
     * 指定 ID のルール定義を削除する。
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

<?php


/**
 * ユーザーテーブルモデルクラス
 * ユーザーテーブル情報の取得、更新、削除を行う
 */
class UTable
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * すべてのユーザーテーブルを取得
     */
    public function getAllUserTables()
    {
        try {
            $sql = 'SELECT * FROM u_table';
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('ユーザーテーブル取得エラー: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 指定されたIDのユーザーテーブルを取得
     */
    public function getUserTableById($tableId)
    {
        try {
            $sql = 'SELECT *
                    FROM u_table 
                    WHERE u_table_id = :table_id AND del_flag = 0';
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':table_id', $tableId);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('ユーザーテーブル取得エラー: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 指定された日付のユーザーテーブルを取得
     */
    public function getUserTablesByDate($playDate)
    {
        try {
            $sql = 'SELECT *
                    FROM u_table 
                    WHERE play_date = :play_date AND del_flag = 0
                    ORDER BY created_at DESC';
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':play_date', $playDate);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('ユーザーテーブル取得エラー: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ユーザーテーブルを更新
     */
    public function updateUserTable($tableId, $data)
    {
        try {
            $sql = 'UPDATE u_table SET 
                        m_game_day_id = :game_day_id,
                        m_direction_id = :direction_id,
                        m_group_id = :group_id,
                        m_rule_id = :rule_id,
                        play_date = :play_date
                    WHERE u_table_id = :table_id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':game_day_id', $data['game_day_id']);
            $stmt->bindParam(':direction_id', $data['direction_id']);
            $stmt->bindParam(':group_id', $data['group_id']);
            $stmt->bindParam(':rule_id', $data['rule_id']);
            $stmt->bindParam(':play_date', $data['play_date']);
            $stmt->bindParam(':table_id', $tableId);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('ユーザーテーブル更新エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ユーザーテーブルを削除（論理削除）
     */
    public function deleteUserTable($tableId)
    {
        try {
            $sql = 'UPDATE u_table SET del_flag = 1 WHERE u_table_id = :table_id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':table_id', $tableId);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('ユーザーテーブル削除エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 新しいユーザーテーブルを追加
     */
    public function addUserTable($data)
    {
        try {
            $sql = 'INSERT INTO u_table (m_game_day_id, m_direction_id, m_group_id, m_rule_id, play_date, created_at) 
                    VALUES (:game_day_id, :direction_id, :group_id, :rule_id, :play_date, NOW())';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':game_day_id', $data['game_day_id']);
            $stmt->bindParam(':direction_id', $data['direction_id']);
            $stmt->bindParam(':group_id', $data['group_id']);
            $stmt->bindParam(':rule_id', $data['rule_id']);
            $stmt->bindParam(':play_date', $data['play_date']);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('ユーザーテーブル追加エラー: ' . $e->getMessage());
            throw $e;
        }
    }
}

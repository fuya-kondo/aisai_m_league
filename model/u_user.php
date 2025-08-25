<?php


/**
 * ユーザーモデルクラス
 * ユーザー情報の取得、更新、削除を行う
 */
class UUser
{
    private $db;
    private $table_name = 'u_user';

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
     * 指定されたIDのユーザーを取得
     */
    public function getUserById($userId)
    {
        try {
            $sql = 'SELECT * FROM u_user WHERE u_user_id = :user_id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('ユーザー取得エラー: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ユーザー情報を更新
     */
    public function updateUser($userId, array $data)
    {
        try {
            $sql = 'UPDATE u_user SET 
                last_name = :last_name,
                first_name = :first_name,
                m_badge_id = :badge_id,
                m_tier_id = :tier_id
            WHERE u_user_id = :user_id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':last_name', $data['last_name']);
            $stmt->bindParam(':first_name', $data['first_name']);
            $stmt->bindParam(':badge_id', $data['m_badge_id']);
            $stmt->bindParam(':tier_id', $data['m_tier_id']);
            $stmt->bindParam(':user_id', $userId);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('ユーザー更新エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ユーザーを追加
     */
    public function addUser(array $data)
    {
        try {
            $sql = 'INSERT INTO u_user (last_name, first_name, m_badge_id, m_tier_id) VALUES (:last_name, :first_name, :badge_id, :tier_id)';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':last_name', $data['last_name']);
            $stmt->bindParam(':first_name', $data['first_name']);
            $stmt->bindParam(':badge_id', $data['m_badge_id']);
            $stmt->bindParam(':tier_id', $data['m_tier_id']);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('ユーザー追加エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ユーザーを削除
     */
    public function deleteUser($userId)
    {
        try {
            $sql = 'DELETE FROM u_user WHERE u_user_id = :user_id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('ユーザー削除エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * バッジを更新（既存の関数との互換性のため）
     */
    public function updateUserBadge($userId, $badgeId)
    {
        try {
            $sql = 'UPDATE u_user SET m_badge_id = :badge_id WHERE u_user_id = :user_id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':badge_id', $badgeId);
            $stmt->bindParam(':user_id', $userId);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('バッジ更新エラー: ' . $e->getMessage());
            return false;
        }
    }
}
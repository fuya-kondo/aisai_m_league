<?php


/**
 * ユーザータイトルモデルクラス
 * ユーザーのタイトル情報の取得、更新、削除を行う
 */
class UTitle
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * すべてのユーザータイトルを取得
     */
    public function getAllUserTitles()
    {
        try {
            $sql = 'SELECT * FROM u_title';
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('ユーザータイトル取得エラー: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 指定されたユーザーのタイトルを取得
     */
    public function getUserTitlesByUserId($userId)
    {
        try {
            $sql = 'SELECT 
                        u_title_id as id,
                        u_user_id as user_id,
                        m_title_id as title_id,
                        created_at
                    FROM u_title 
                    WHERE u_user_id = :user_id AND del_flag = 0
                    ORDER BY created_at DESC';
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('ユーザータイトル取得エラー: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 指定されたIDのユーザータイトルを取得
     */
    public function getUserTitleById($userTitleId)
    {
        try {
            $sql = 'SELECT 
                        u_title_id as id,
                        u_user_id as user_id,
                        m_title_id as title_id,
                        created_at
                    FROM u_title 
                    WHERE u_title_id = :user_title_id AND del_flag = 0';
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_title_id', $userTitleId);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('ユーザータイトル取得エラー: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ユーザータイトルを更新
     */
    public function updateUserTitle($userTitleId, $data)
    {
        try {
            $sql = 'UPDATE u_title SET m_title_id = :title_id WHERE u_title_id = :user_title_id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':title_id', $data['title_id']);
            $stmt->bindParam(':user_title_id', $userTitleId);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('ユーザータイトル更新エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ユーザータイトルを削除（論理削除）
     */
    public function deleteUserTitle($userTitleId)
    {
        try {
            $sql = 'UPDATE u_title SET del_flag = 1 WHERE u_title_id = :user_title_id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_title_id', $userTitleId);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('ユーザータイトル削除エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 新しいユーザータイトルを追加
     */
    public function addUserTitle($data)
    {
        try {
            $sql = 'INSERT INTO u_title (u_user_id, m_title_id, created_at) VALUES (:user_id, :title_id, NOW())';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':title_id', $data['title_id']);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('ユーザータイトル追加エラー: ' . $e->getMessage());
            throw $e;
        }
    }
}

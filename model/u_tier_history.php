<?php


/**
 * ユーザーティア履歴モデルクラス
 * ユーザーのティア履歴情報の取得、更新、削除を行う
 */
class UTierHistory
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * すべてのティア履歴を取得
     */
    public function getAllTierHistory()
    {
        try {
            $sql = 'SELECT * FROM u_tier_history';
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('ティア履歴取得エラー: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * マスターデータ管理用のフラットなティア履歴を取得
     */
    public function getAllTierHistoryFlat()
    {
        try {
            $sql = 'SELECT 
                        u_user_tier_history_id,
                        u_user_id,
                        m_tier_id,
                        change_date as year
                    FROM u_tier_history 
                    ORDER BY change_date DESC';
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('ティア履歴取得エラー: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 指定されたユーザーのティア履歴を取得
     */
    public function getTierHistoryByUserId($userId)
    {
        try {
            $sql = 'SELECT 
                        u_user_tier_history_id as id,
                        u_user_id as user_id,
                        m_tier_id as tier_id,
                        change_date as year
                    FROM u_tier_history 
                    WHERE u_user_id = :user_id
                    ORDER BY change_date DESC';
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            $tierHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 年でグループ化
            $groupedHistory = [];
            foreach ($tierHistory as $history) {
                $groupedHistory[$history['year']] = $history;
            }

            return $groupedHistory;
        } catch (Exception $e) {
            error_log('ユーザーティア履歴取得エラー: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 指定されたIDのティア履歴を取得
     */
    public function getTierHistoryById($tierHistoryId)
    {
        try {
            $sql = 'SELECT 
                        u_user_tier_history_id as id,
                        u_user_id as user_id,
                        m_tier_id as tier_id,
                        change_date as year
                    FROM u_tier_history 
                    WHERE u_user_tier_history_id = :tier_history_id';
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':tier_history_id', $tierHistoryId);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('ティア履歴取得エラー: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ティア履歴を更新
     */
    public function updateTierHistory($tierHistoryId, $data)
    {
        try {
            $sql = 'UPDATE u_tier_history SET 
                        u_user_id = :u_user_id,
                        m_tier_id = :m_tier_id,
                        change_date = :year
                    WHERE u_user_tier_history_id = :tier_history_id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':u_user_id', $data['u_user_id']);
            $stmt->bindParam(':m_tier_id', $data['m_tier_id']);
            $stmt->bindParam(':year', $data['year']);
            $stmt->bindParam(':tier_history_id', $tierHistoryId);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('ティア履歴更新エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ティア履歴を削除
     */
    public function deleteTierHistory($tierHistoryId)
    {
        try {
            $sql = 'DELETE FROM u_tier_history WHERE u_user_tier_history_id = :tier_history_id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':tier_history_id', $tierHistoryId);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('ティア履歴削除エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 新しいティア履歴を追加
     */
    public function addTierHistory($data)
    {
        try {
            $sql = 'INSERT INTO u_tier_history (u_user_id, m_tier_id, change_date) VALUES (:u_user_id, :m_tier_id, :year)';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':u_user_id', $data['u_user_id']);
            $stmt->bindParam(':m_tier_id', $data['m_tier_id']);
            $stmt->bindParam(':year', $data['year']);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('ティア履歴追加エラー: ' . $e->getMessage());
            throw $e;
        }
    }
}

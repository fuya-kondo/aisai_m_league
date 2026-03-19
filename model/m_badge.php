<?php
/**
 * バッジマスターテーブル用のデータアクセス層。
 * バッジ定義の取得・追加・更新・削除をデータベースに対して行う。
 */

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
     * バッジ定義を ID 起点で引ける形に整えて返す。
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
     * 管理画面から送られた入力値で既存バッジを更新する。
     */
    public function updateBadge($badgeId, $data)
    {
        try {
            $sql = 'UPDATE m_badge
                    SET name = :name, image = :image, flame = :flame, background = :background
                    WHERE m_badge_id = :badge_id';

            $stmt = $this->db->prepare($sql);

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
     * 指定 ID のバッジを削除する。
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
     * 新しいバッジ定義を追加する。
     */
    public function addBadge($data)
    {
        try {
            $sql = 'INSERT INTO m_badge (name, image, flame, background) 
                    VALUES (:name, :image, :flame, :background)';

            $stmt = $this->db->prepare($sql);

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

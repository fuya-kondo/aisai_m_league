<?php

/**
 * ゲーム日マスターモデルクラス
 */
class MGameDay
{
	private $db;
    private $table_name = 'm_game_day';

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
            $sql = 'SELECT * FROM '.$this->table_name.' ORDER BY game_day DESC';
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($this->table_name.'取得エラー: ' . $e->getMessage());
            return [];
        }

        // 日付をキーに整形
        $result = [];
        foreach ($data as $value) {
            $result[$value['game_day']] = $value;
        }

        return $result;
    }

	/** ゲーム日を追加 */
	public function addGameDay(array $data)
	{
		try {
			$sql = 'INSERT INTO m_game_day (game_day) VALUES (:game_day)';
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':game_day', $data['game_day']);
			return $stmt->execute();
		} catch (Exception $e) {
			error_log('ゲーム日追加エラー: ' . $e->getMessage());
			throw $e;
		}
	}

	/** ゲーム日を削除 */
	public function deleteGameDay(string $gameDay)
	{
		try {
			$sql = 'DELETE FROM m_game_day WHERE game_day = :game_day';
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':game_day', $gameDay);
			return $stmt->execute();
		} catch (Exception $e) {
			error_log('ゲーム日削除エラー: ' . $e->getMessage());
			throw $e;
		}
	}

	/** ゲーム日を更新 */
	public function updateGameDay(string $gameDay, array $data)
	{
		try {
			$sql = 'UPDATE m_game_day SET game_day = :new_game_day WHERE game_day = :game_day';
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':new_game_day', $data['game_day']);
			$stmt->bindParam(':game_day', $gameDay);
			return $stmt->execute();
		} catch (Exception $e) {
			error_log('ゲーム日更新エラー: ' . $e->getMessage());
			throw $e;
		}
	}

}

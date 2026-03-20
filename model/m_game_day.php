<?php
/**
 * 対局日マスターテーブル用のデータアクセス層。
 * 開催日の一覧取得と管理画面からのメンテナンスを担当する。
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
     * 開催日を日付文字列で引ける形に整えて返す。
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

        $result = [];
        foreach ($data as $value) {
            $result[$value['game_day']] = $value;
        }

        return $result;
    }

	/**
     * 新しい開催日を追加する。
     */
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

	/**
     * 指定した開催日を削除する。
     */
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

	/**
     * 既存開催日を別の日付へ更新する。
     */
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

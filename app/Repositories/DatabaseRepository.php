<?php
namespace App\Repositories;

use Database;
use PDO;

/**
 * 既存 PDO 接続を使う repository の共通基盤。
 * 基本的な select / execute を小さく揃え、各 repository の重複 SQL 実行コードを減らす。
 */
abstract class DatabaseRepository
{
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * 複数行を返す SELECT を共通化する。
     * repository 側では SQL とバインド値だけを決めればよいようにする。
     */
    protected function fetchAll(string $sql, array $params = []): array
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 単一行取得を共通化し、未取得時は null を返す。
     */
    protected function fetchOne(string $sql, array $params = []): ?array
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        $record = $statement->fetch(PDO::FETCH_ASSOC);
        return $record === false ? null : $record;
    }

    /**
     * INSERT / UPDATE / DELETE を共通の入口で実行する。
     */
    protected function execute(string $sql, array $params = []): bool
    {
        $statement = $this->db->prepare($sql);
        return $statement->execute($params);
    }

    /**
     * 一覧データを主キー相当の値で引き直し、呼び出し側の探索を簡単にする。
     */
    protected function indexById(array $records, string $idKey): array
    {
        $indexedRecords = [];
        foreach ($records as $record) {
            $indexedRecords[$record[$idKey]] = $record;
        }

        return $indexedRecords;
    }
}

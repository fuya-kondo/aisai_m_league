<?php
namespace App\Repositories;

/**
 * ティアマスター用 repository。
 * 色付きティア定義の一覧取得と CRUD を担当する。
 */
final class TierRepository extends DatabaseRepository
{
    private const TABLE_NAME = 'm_tier';
    private const PRIMARY_KEY = 'm_tier_id';

    public function findAllIndexed(): array
    {
        return $this->indexById($this->fetchAll('SELECT * FROM ' . self::TABLE_NAME), self::PRIMARY_KEY);
    }

    public function create(array $tierData): bool
    {
        return $this->execute(
            'INSERT INTO ' . self::TABLE_NAME . ' (name, color) VALUES (:name, :color)',
            [':name' => $tierData['name'], ':color' => $tierData['color']]
        );
    }

    public function update(int $tierId, array $tierData): bool
    {
        return $this->execute(
            'UPDATE ' . self::TABLE_NAME . ' SET name = :name, color = :color WHERE ' . self::PRIMARY_KEY . ' = :tier_id',
            [':name' => $tierData['name'], ':color' => $tierData['color'], ':tier_id' => $tierId]
        );
    }

    public function delete(int $tierId): bool
    {
        return $this->execute(
            'DELETE FROM ' . self::TABLE_NAME . ' WHERE ' . self::PRIMARY_KEY . ' = :tier_id',
            [':tier_id' => $tierId]
        );
    }
}

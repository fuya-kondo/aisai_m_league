<?php
namespace App\Repositories;

/**
 * ユーザーティア履歴用 repository。
 * 一覧取得、ユーザー別履歴、管理画面向けフラット一覧、CRUD を統一する。
 */
final class TierHistoryRepository extends DatabaseRepository
{
    private const TABLE_NAME = 'u_tier_history';
    private const PRIMARY_KEY = 'u_user_tier_history_id';

    public function findAll(): array
    {
        return $this->fetchAll('SELECT * FROM ' . self::TABLE_NAME);
    }

    public function findAllFlat(): array
    {
        return $this->fetchAll(
            'SELECT u_user_tier_history_id, u_user_id, m_tier_id, change_date as year FROM ' . self::TABLE_NAME . ' ORDER BY change_date DESC'
        );
    }

    public function findGroupedByUser(int $userId): array
    {
        $records = $this->fetchAll(
            'SELECT u_user_tier_history_id as id, u_user_id as user_id, m_tier_id as tier_id, change_date as year FROM ' . self::TABLE_NAME . ' WHERE u_user_id = :user_id ORDER BY change_date DESC',
            [':user_id' => $userId]
        );

        $groupedRecords = [];
        foreach ($records as $record) {
            $groupedRecords[$record['year']] = $record;
        }

        return $groupedRecords;
    }

    public function findById(int $tierHistoryId): ?array
    {
        return $this->fetchOne(
            'SELECT u_user_tier_history_id as id, u_user_id as user_id, m_tier_id as tier_id, change_date as year FROM ' . self::TABLE_NAME . ' WHERE ' . self::PRIMARY_KEY . ' = :tier_history_id',
            [':tier_history_id' => $tierHistoryId]
        );
    }

    public function create(array $tierHistoryData): bool
    {
        return $this->execute(
            'INSERT INTO ' . self::TABLE_NAME . ' (u_user_id, m_tier_id, change_date) VALUES (:u_user_id, :m_tier_id, :year)',
            [':u_user_id' => $tierHistoryData['u_user_id'], ':m_tier_id' => $tierHistoryData['m_tier_id'], ':year' => $tierHistoryData['year']]
        );
    }

    public function update(int $tierHistoryId, array $tierHistoryData): bool
    {
        return $this->execute(
            'UPDATE ' . self::TABLE_NAME . ' SET u_user_id = :u_user_id, m_tier_id = :m_tier_id, change_date = :year WHERE ' . self::PRIMARY_KEY . ' = :tier_history_id',
            [
                ':u_user_id' => $tierHistoryData['u_user_id'],
                ':m_tier_id' => $tierHistoryData['m_tier_id'],
                ':year' => $tierHistoryData['year'],
                ':tier_history_id' => $tierHistoryId,
            ]
        );
    }

    public function delete(int $tierHistoryId): bool
    {
        return $this->execute(
            'DELETE FROM ' . self::TABLE_NAME . ' WHERE ' . self::PRIMARY_KEY . ' = :tier_history_id',
            [':tier_history_id' => $tierHistoryId]
        );
    }
}

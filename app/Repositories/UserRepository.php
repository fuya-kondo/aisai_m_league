<?php
namespace App\Repositories;

/**
 * ユーザー関連の永続化を担当する repository。
 * u_user テーブルへの CRUD と一覧取得を、controller や legacy model から分離する。
 */
final class UserRepository extends DatabaseRepository
{
    private const TABLE_NAME = 'u_user';
    private const PRIMARY_KEY = 'u_user_id';

    public function findAllIndexed(): array
    {
        $records = $this->fetchAll('SELECT * FROM ' . self::TABLE_NAME);
        return $this->indexById($records, self::PRIMARY_KEY);
    }

    public function findById(int $userId): ?array
    {
        return $this->fetchOne(
            'SELECT * FROM ' . self::TABLE_NAME . ' WHERE ' . self::PRIMARY_KEY . ' = :user_id',
            [':user_id' => $userId]
        );
    }

    public function create(array $userData): bool
    {
        return $this->execute(
            'INSERT INTO ' . self::TABLE_NAME . ' (last_name, first_name, m_badge_id, m_tier_id) VALUES (:last_name, :first_name, :badge_id, :tier_id)',
            [
                ':last_name' => $userData['last_name'],
                ':first_name' => $userData['first_name'],
                ':badge_id' => $userData['m_badge_id'],
                ':tier_id' => $userData['m_tier_id'],
            ]
        );
    }

    public function update(int $userId, array $userData): bool
    {
        return $this->execute(
            'UPDATE ' . self::TABLE_NAME . ' SET last_name = :last_name, first_name = :first_name, m_badge_id = :badge_id, m_tier_id = :tier_id WHERE ' . self::PRIMARY_KEY . ' = :user_id',
            [
                ':last_name' => $userData['last_name'],
                ':first_name' => $userData['first_name'],
                ':badge_id' => $userData['m_badge_id'],
                ':tier_id' => $userData['m_tier_id'],
                ':user_id' => $userId,
            ]
        );
    }

    public function delete(int $userId): bool
    {
        return $this->execute(
            'DELETE FROM ' . self::TABLE_NAME . ' WHERE ' . self::PRIMARY_KEY . ' = :user_id',
            [':user_id' => $userId]
        );
    }

    public function updateBadge(int $userId, int $badgeId): bool
    {
        return $this->execute(
            'UPDATE ' . self::TABLE_NAME . ' SET m_badge_id = :badge_id WHERE ' . self::PRIMARY_KEY . ' = :user_id',
            [':badge_id' => $badgeId, ':user_id' => $userId]
        );
    }
}

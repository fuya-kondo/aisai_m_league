<?php
namespace App\Repositories;

/**
 * 設定マスター用 repository。
 * 一覧取得、トグル更新、CRUD を 1 か所に集約する。
 */
final class SettingRepository extends DatabaseRepository
{
    private const TABLE_NAME = 'm_setting';
    private const PRIMARY_KEY = 'm_setting_id';

    public function findAllIndexed(): array
    {
        return $this->indexById($this->fetchAll('SELECT * FROM ' . self::TABLE_NAME), self::PRIMARY_KEY);
    }

    public function toggle(int $settingId): bool
    {
        $record = $this->fetchOne(
            'SELECT value FROM ' . self::TABLE_NAME . ' WHERE ' . self::PRIMARY_KEY . ' = :setting_id',
            [':setting_id' => $settingId]
        );

        if ($record === null) {
            throw new \Exception('設定が見つかりません');
        }

        $newValue = (int) $record['value'] === 1 ? 0 : 1;
        return $this->execute(
            'UPDATE ' . self::TABLE_NAME . ' SET value = :value WHERE ' . self::PRIMARY_KEY . ' = :setting_id',
            [':value' => $newValue, ':setting_id' => $settingId]
        );
    }

    public function create(array $settingData): bool
    {
        return $this->execute(
            'INSERT INTO ' . self::TABLE_NAME . ' (name, value) VALUES (:name, :value)',
            [':name' => $settingData['name'], ':value' => $settingData['value']]
        );
    }

    public function update(int $settingId, array $settingData): bool
    {
        return $this->execute(
            'UPDATE ' . self::TABLE_NAME . ' SET name = :name, value = :value WHERE ' . self::PRIMARY_KEY . ' = :setting_id',
            [':name' => $settingData['name'], ':value' => $settingData['value'], ':setting_id' => $settingId]
        );
    }

    public function delete(int $settingId): bool
    {
        return $this->execute(
            'DELETE FROM ' . self::TABLE_NAME . ' WHERE ' . self::PRIMARY_KEY . ' = :setting_id',
            [':setting_id' => $settingId]
        );
    }
}

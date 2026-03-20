<?php
namespace App\Services;

use App\Repositories\GameHistoryRepository;
use RuntimeException;
use Throwable;

/**
 * 対局履歴の登録・更新・削除・参照をまとめるアプリケーション service。
 * 入力正規化とトランザクション制御を担い、repository には整形済み payload だけを渡す。
 */
final class GameHistoryService
{
    private GameHistoryRepository $repository;
    private GamePointCalculator $pointCalculator;
    private \UTable $tableModel;
    private \MGroup $groupModel;
    private \MRule $ruleModel;
    private ?array $groupData = null;
    private ?array $ruleData = null;

    public function __construct(?GameHistoryRepository $repository = null, ?GamePointCalculator $pointCalculator = null)
    {
        $this->repository = $repository ?? new GameHistoryRepository();
        $this->pointCalculator = $pointCalculator ?? new GamePointCalculator();
        $this->tableModel = new \UTable();
        $this->groupModel = new \MGroup();
        $this->ruleModel = new \MRule();
    }

    public function getGroupedHistory(): array
    {
        try {
            return $this->repository->getGroupedActiveHistory();
        } catch (Throwable $throwable) {
            error_log('ゲーム履歴取得エラー: ' . $throwable->getMessage());
            return [];
        }
    }

    public function getFlatHistory(): array
    {
        try {
            return $this->repository->getFlatActiveHistory();
        } catch (Throwable $throwable) {
            error_log('ゲーム履歴取得エラー: ' . $throwable->getMessage());
            return [];
        }
    }

    public function getLatestGameRowsByDate(int $tableId, string $playDate): array
    {
        try {
            return $this->repository->findLatestGameRowsByDate($tableId, $playDate);
        } catch (Throwable $throwable) {
            error_log('最新半荘取得エラー: ' . $throwable->getMessage());
            return [];
        }
    }

    public function getNextGameNumberByDate(int $tableId, string $playDate): int
    {
        try {
            return $this->repository->getNextGameNumberByDate($tableId, $playDate);
        } catch (Throwable $throwable) {
            error_log('次半荘番号取得エラー: ' . $throwable->getMessage());
            return 1;
        }
    }

    public function existsGameForDate(int $tableId, string $playDate, int $game): bool
    {
        try {
            return $this->repository->existsGameForDate($tableId, $playDate, $game);
        } catch (Throwable $throwable) {
            error_log('半荘重複チェックエラー: ' . $throwable->getMessage());
            return true;
        }
    }

    public function find(int $historyId): ?array
    {
        try {
            return $this->repository->findById($historyId);
        } catch (Throwable $throwable) {
            error_log('履歴取得エラー: ' . $throwable->getMessage());
            return null;
        }
    }

    public function findBatch(int $tableId, string $playDate, int $game): array
    {
        try {
            return $this->repository->findBatchByTableDateGame($tableId, $playDate, $game);
        } catch (Throwable $throwable) {
            error_log('半荘履歴取得エラー: ' . $throwable->getMessage());
            return [];
        }
    }

    public function create(array $historyData): bool
    {
        try {
            $normalized = $this->normalizeHistoryData($historyData);
            $this->repository->insert($normalized, $this->calculatePoint($normalized));
            return true;
        } catch (Throwable $throwable) {
            error_log('ゲーム履歴追加エラー: ' . $throwable->getMessage());
            return false;
        }
    }

    public function createBatch(array $records): bool
    {
        if (empty($records)) {
            return false;
        }

        $connection = $this->repository->connection();
        $startedTransaction = false;

        try {
            if (!$connection->inTransaction()) {
                $connection->beginTransaction();
                $startedTransaction = true;
            }

            $statement = $this->repository->prepareInsertStatement();
            foreach ($records as $record) {
                $normalized = $this->normalizeHistoryData($record);
                $this->repository->insert($normalized, $this->calculatePoint($normalized), $statement);
            }

            if ($startedTransaction) {
                $connection->commit();
            }

            return true;
        } catch (Throwable $throwable) {
            if ($startedTransaction && $connection->inTransaction()) {
                $connection->rollBack();
            }
            error_log('一括登録エラー: ' . $throwable->getMessage());
            return false;
        }
    }

    public function update(int $historyId, array $historyData): bool
    {
        try {
            $currentHistory = $this->repository->findById($historyId);
            if ($currentHistory === null) {
                throw new RuntimeException('更新対象のゲーム履歴が見つかりません。');
            }

            $mergedHistoryData = array_merge($currentHistory, $historyData);
            $normalized = $this->normalizeHistoryData($mergedHistoryData);
            $this->repository->update($historyId, $normalized, $this->calculatePoint($normalized));
            return true;
        } catch (Throwable $throwable) {
            error_log('ゲーム履歴更新エラー: ' . $throwable->getMessage());
            return false;
        }
    }

    public function updateBatch(array $records): bool
    {
        if (empty($records)) {
            return false;
        }

        $connection = $this->repository->connection();
        $startedTransaction = false;

        try {
            if (!$connection->inTransaction()) {
                $connection->beginTransaction();
                $startedTransaction = true;
            }

            foreach ($records as $record) {
                $historyId = (int)($record['historyId'] ?? 0);
                if ($historyId < 1) {
                    throw new RuntimeException('更新対象の履歴IDが不正です。');
                }

                $currentHistory = $this->repository->findById($historyId);
                if ($currentHistory === null) {
                    throw new RuntimeException('更新対象のゲーム履歴が見つかりません。');
                }

                $mergedHistoryData = array_merge($currentHistory, $record);
                $normalized = $this->normalizeHistoryData($mergedHistoryData);
                $this->repository->update($historyId, $normalized, $this->calculatePoint($normalized));
            }

            if ($startedTransaction) {
                $connection->commit();
            }

            return true;
        } catch (Throwable $throwable) {
            if ($startedTransaction && $connection->inTransaction()) {
                $connection->rollBack();
            }
            error_log('一括更新エラー: ' . $throwable->getMessage());
            return false;
        }
    }

    public function delete(int $historyId): bool
    {
        try {
            return $this->repository->softDelete($historyId);
        } catch (Throwable $throwable) {
            error_log('ゲーム履歴削除エラー: ' . $throwable->getMessage());
            return false;
        }
    }

    public function deleteBatch(int $tableId, string $playDate, int $game): bool
    {
        $rows = $this->findBatch($tableId, $playDate, $game);
        if (empty($rows)) {
            return false;
        }

        try {
            return $this->repository->softDeleteBatch(array_map(static fn(array $row): int => (int)$row['u_game_history_id'], $rows));
        } catch (Throwable $throwable) {
            error_log('一括削除エラー: ' . $throwable->getMessage());
            return false;
        }
    }

    public function getScoreTotalUnitsByTableId(int $tableId): int
    {
        $startScore = $this->getStartScoreByTableId($tableId);
        return (int)(($startScore * \App\Support\Constants\AppConstants::PLAYER_COUNT) / \App\Support\Constants\AppConstants::SCORE_INPUT_MULTIPLIER);
    }

    public function getScoreTotalByTableId(int $tableId): int
    {
        return $this->getStartScoreByTableId($tableId) * \App\Support\Constants\AppConstants::PLAYER_COUNT;
    }

    public function normalizeHistoryData(array $rawData): array
    {
        return [
            'play_date' => (string)($rawData['playDate'] ?? $rawData['play_date'] ?? ''),
            'game' => (int)($rawData['game'] ?? 0),
            'u_user_id' => (int)($rawData['userId'] ?? $rawData['u_user_id'] ?? 0),
            'u_table_id' => (int)($rawData['tableId'] ?? $rawData['u_table_id'] ?? 0),
            'rank' => (string)($rawData['rank'] ?? ''),
            'score' => (int)($rawData['score'] ?? 0),
            'm_direction_id' => (int)($rawData['direction'] ?? $rawData['m_direction_id'] ?? 0),
            'mistake_count' => (int)($rawData['mistakeCount'] ?? $rawData['mistake_count'] ?? 0),
        ];
    }

    private function calculatePoint(array $normalizedHistory): float|int
    {
        $ruleConfig = $this->resolveRuleConfig((int)$normalizedHistory['u_table_id']);

        return $this->pointCalculator->calculate(
            (string)$normalizedHistory['rank'],
            (int)$normalizedHistory['score'],
            $ruleConfig,
        );
    }

    private function getStartScoreByTableId(int $tableId): int
    {
        $ruleConfig = $this->resolveRuleConfig($tableId);
        return isset($ruleConfig['start_score']) ? (int)$ruleConfig['start_score'] : 25000;
    }

    private function resolveRuleConfig(int $tableId): array
    {
        if ($tableId < 1) {
            return [];
        }

        $table = $this->tableModel->getUserTableById($tableId);
        if (!is_array($table)) {
            return [];
        }

        $ruleId = (int)($table['m_rule_id'] ?? 0);
        if ($ruleId < 1) {
            $groupId = (int)($table['m_group_id'] ?? 0);
            if ($groupId > 0) {
                $groupData = $this->getGroupData();
                $ruleId = (int)($groupData[$groupId]['m_rule_id'] ?? 0);
            }
        }

        if ($ruleId < 1) {
            return [];
        }

        $ruleData = $this->getRuleData();
        return is_array($ruleData[$ruleId] ?? null) ? $ruleData[$ruleId] : [];
    }

    private function getGroupData(): array
    {
        if ($this->groupData === null) {
            $this->groupData = $this->groupModel->getAllData();
        }

        return $this->groupData;
    }

    private function getRuleData(): array
    {
        if ($this->ruleData === null) {
            $this->ruleData = $this->ruleModel->getAllData();
        }

        return $this->ruleData;
    }
}






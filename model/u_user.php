<?php
/**
 * ユーザーテーブル用のデータアクセス互換ラッパー。
 * 旧呼び出しを維持しつつ、実処理は UserService / UserRepository へ委譲する。
 */
class UUser
{
    private \App\Services\UserService $userService;

    public function __construct()
    {
        $this->userService = \App\Support\ServiceFactory::createUserService();
    }

    public function getAllData(): array
    {
        try {
            return $this->userService->getAllIndexed();
        } catch (Exception $exception) {
            error_log('u_user取得エラー: ' . $exception->getMessage());
            return [];
        }
    }

    public function getAllUsers(): array
    {
        return $this->getAllData();
    }

    public function getUserById(int $userId): ?array
    {
        try {
            return $this->userService->getById($userId);
        } catch (Exception $exception) {
            error_log('ユーザー取得エラー: ' . $exception->getMessage());
            return null;
        }
    }

    public function updateUser(int $userId, array $userData): bool
    {
        try {
            return $this->userService->update($userId, $userData);
        } catch (Exception $exception) {
            error_log('ユーザー更新エラー: ' . $exception->getMessage());
            throw $exception;
        }
    }

    public function addUser(array $userData): bool
    {
        try {
            return $this->userService->create($userData);
        } catch (Exception $exception) {
            error_log('ユーザー追加エラー: ' . $exception->getMessage());
            throw $exception;
        }
    }

    public function deleteUser(int $userId): bool
    {
        try {
            return $this->userService->delete($userId);
        } catch (Exception $exception) {
            error_log('ユーザー削除エラー: ' . $exception->getMessage());
            throw $exception;
        }
    }

    public function updateUserBadge(int $userId, int $badgeId): bool
    {
        try {
            return $this->userService->updateBadge($userId, $badgeId);
        } catch (Exception $exception) {
            error_log('バッジ更新エラー: ' . $exception->getMessage());
            return false;
        }
    }
}

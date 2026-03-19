<?php
namespace App\Services;

use App\Repositories\UserRepository;

/**
 * ユーザー操作の業務境界。
 * controller はこの service を呼ぶだけにし、legacy model 側も同じ実装を使う。
 */
final class UserService
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function getAllIndexed(): array
    {
        return $this->userRepository->findAllIndexed();
    }

    public function getById(int $userId): ?array
    {
        return $this->userRepository->findById($userId);
    }

    public function create(array $userData): bool
    {
        return $this->userRepository->create($userData);
    }

    public function update(int $userId, array $userData): bool
    {
        return $this->userRepository->update($userId, $userData);
    }

    public function delete(int $userId): bool
    {
        return $this->userRepository->delete($userId);
    }

    public function updateBadge(int $userId, int $badgeId): bool
    {
        return $this->userRepository->updateBadge($userId, $badgeId);
    }

    public function attachBadgeAndTier(array $userList, array $badgeList, array $tierList): array
    {
        $enrichedUsers = [];
        foreach ($userList as $userId => $userRecord) {
            if (!empty($userRecord['m_badge_id']) && isset($badgeList[$userRecord['m_badge_id']])) {
                $userRecord['badge'] = $badgeList[$userRecord['m_badge_id']];
            }
            if (!empty($userRecord['m_tier_id']) && isset($tierList[$userRecord['m_tier_id']])) {
                $userRecord['tier'] = $tierList[$userRecord['m_tier_id']];
            }
            $enrichedUsers[$userId] = $userRecord;
        }

        return $enrichedUsers;
    }
}

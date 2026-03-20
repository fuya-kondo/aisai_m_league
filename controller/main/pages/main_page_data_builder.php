<?php
/**
 * main 配下 page data builder の共通基盤。
 * マスターデータ、統計カラム、ページネーション、設定参照をまとめる。
 */
abstract class MainPageDataBuilder
{
    protected StatsService $statsService;
    protected array $masterData;
    protected array $statsColumn;
    protected \App\Services\GameHistoryService $gameHistoryService;

    public function __construct(StatsService $statsService, array $masterData, array $statsColumn, \App\Services\GameHistoryService $gameHistoryService)
    {
        $this->statsService = $statsService;
        $this->masterData = $masterData;
        $this->statsColumn = $statsColumn;
        $this->gameHistoryService = $gameHistoryService;
    }

    protected function withTitle(string $title, array $pageData = []): array
    {
        return array_merge($pageData, ['title' => $title]);
    }

    protected function getDirectionList(): array
    {
        return $this->masterData['mDirectionList'] ?? [];
    }

    protected function getRankConfig(): array
    {
        return $this->statsColumn['rankConfig'] ?? [];
    }

    protected function isScoreDisplayEnabled(string $selectedTerm): bool
    {
        $hideScoreEnabled = $this->findSettingValueByName(\App\Support\Constants\SettingNames::HIDE_SCORE, false);
        return !($hideScoreEnabled && ($selectedTerm === date('Y') || $selectedTerm === \App\Support\Constants\AppConstants::TODAY_TERM));
    }

    protected function buildPagination(int $currentPage, int $totalPages, array $queryParams = []): array
    {
        $links = [];
        if ($totalPages <= 1) {
            return ['currentPage' => $currentPage, 'totalPages' => $totalPages, 'links' => $links];
        }

        if ($currentPage > \App\Support\Constants\AppConstants::FIRST_PAGE) {
            $links[] = ['label' => '最初', 'href' => $this->buildPageHref(\App\Support\Constants\AppConstants::FIRST_PAGE, $queryParams), 'active' => false];
            $links[] = ['label' => '前へ', 'href' => $this->buildPageHref($currentPage - 1, $queryParams), 'active' => false];
        }

        $startPage = max(\App\Support\Constants\AppConstants::FIRST_PAGE, $currentPage - 2);
        $endPage = min($totalPages, $currentPage + 2);
        for ($page = $startPage; $page <= $endPage; $page++) {
            $links[] = ['label' => (string)$page, 'href' => $this->buildPageHref($page, $queryParams), 'active' => $page === $currentPage];
        }

        if ($currentPage < $totalPages) {
            $links[] = ['label' => '次へ', 'href' => $this->buildPageHref($currentPage + 1, $queryParams), 'active' => false];
            $links[] = ['label' => '最後', 'href' => $this->buildPageHref($totalPages, $queryParams), 'active' => false];
        }

        return ['currentPage' => $currentPage, 'totalPages' => $totalPages, 'links' => $links];
    }

    protected function buildPageHref(int $page, array $queryParams = []): string
    {
        $params = ['page' => $page];
        foreach ($queryParams as $key => $value) {
            if ($value !== null && $value !== '') {
                $params[$key] = $value;
            }
        }

        return '?' . http_build_query($params);
    }

    protected function findSettingValueByName(string $settingName, mixed $defaultValue = null): mixed
    {
        foreach (($this->masterData['mSettingList'] ?? []) as $settingRow) {
            if (($settingRow['name'] ?? null) === $settingName) {
                return isset($settingRow['value']) ? (int)$settingRow['value'] === 1 : $defaultValue;
            }
        }

        return $defaultValue;
    }
}

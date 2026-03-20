<?php
/**
 * ユーザー向けメイン画面の振る舞いをまとめるコントローラー。
 * action から page data builder を呼び出し、描画やリダイレクト判定へ責務を絞る。
 */
class MainController extends BaseController
{
    private array $masterData = [];
    private StatsService $statsService;
    private array $statsColumn = [];
    private StatsPageDataBuilder $statsPageDataBuilder;
    private HistoryPageDataBuilder $historyPageDataBuilder;
    private PersonalStatsPageDataBuilder $personalStatsPageDataBuilder;
    private AnalysisPageDataBuilder $analysisPageDataBuilder;
    private AddPageDataBuilder $addPageDataBuilder;
    private BulkAddPageDataBuilder $bulkAddPageDataBuilder;
    private BulkUpdatePageDataBuilder $bulkUpdatePageDataBuilder;
    private UpdatePageDataBuilder $updatePageDataBuilder;

    public function __construct()
    {
        parent::__construct();
        $this->masterData = $this->getMainMasterData();
        $this->statsService = \App\Support\StatsServiceFactory::create($this->masterData);
        $this->statsColumn = StatsColumn::getColumn();
        $this->initializePageDataBuilders();
    }

    public function top(): void
    {
        $this->renderView(__DIR__ . '/../../view/main/top.php', [
            'nextTwoGameDays' => $this->statsService->getNextTwoGameDays(),
        ]);
    }

    public function stats(): void
    {
        $selectedTerm = $this->readSelectedStatsTerm();
        $this->renderView(__DIR__ . '/../../view/main/stats.php', $this->statsPageDataBuilder->build($selectedTerm));
    }

    public function history(): void
    {
        $selectedUser = isset($_GET['userId']) ? (string) $_GET['userId'] : null;
        $selectedView = isset($_GET['view']) ? (string)$_GET['view'] : ($selectedUser !== null ? 'personal' : 'overview');
        $selectedYear = $this->readSelectedYear();
        $currentPage = $this->readCurrentPage();
        $deleteResult = $this->historyPageDataBuilder->handleDeleteRequest($_POST, $_SERVER['REQUEST_METHOD'] ?? 'GET');

        $this->redirectIfNeeded($deleteResult['redirectUrl'] ?? null);
        $this->renderView(
            __DIR__ . '/../../view/main/history.php',
            $this->historyPageDataBuilder->build($selectedUser, $selectedView, $selectedYear, $currentPage, $deleteResult['errorMessage'] ?? null)
        );
    }

    public function personalStats(): void
    {
        $this->renderView(
            __DIR__ . '/../../view/main/personal.php',
            $this->personalStatsPageDataBuilder->build($this->readSelectedStatsTerm(), isset($_GET['player']) ? (string) $_GET['player'] : null)
        );
    }

    public function analysis(): void
    {
        $selectedUser = isset($_GET['userId']) ? (string) $_GET['userId'] : null;
        $selectedTerm = isset($_GET['term']) ? (string)$_GET['term'] : null;
        $shouldRun = isset($_GET['run']) && (string)$_GET['run'] === '1';
        $pageData = $this->analysisPageDataBuilder->build($selectedUser, $selectedTerm, $shouldRun);
        $pageData['pageTabs'] = $this->buildOtherPageTabs('analysis');
        $this->renderView(__DIR__ . '/../../view/main/analysis.php', $pageData);
    }

    public function setting(): void
    {
        $errorMessage = null;

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $settingId = isset($_POST['settingId']) ? (int) $_POST['settingId'] : null;
            if ($settingId !== null) {
                try {
                    $this->settingService->toggle($settingId);
                    $this->redirectIfNeeded('setting');
                } catch (Exception $exception) {
                    $errorMessage = '処理中にエラーが発生しました: ' . $exception->getMessage();
                }
            } else {
                $errorMessage = '必要なパラメータが不足しています';
            }
        }

        $this->renderView(__DIR__ . '/../../view/main/setting.php', [
            'mSettingList' => $this->masterData['mSettingList'] ?? [],
            'error_msg' => $errorMessage,
            'title' => '設定',
            'pageTabs' => $this->buildOtherPageTabs('setting'),
        ]);
    }

    public function rule(): void
    {
        $this->renderStaticPage(
            __DIR__ . '/../../view/main/rule.php',
            'AISAI.M.LEAGUE 競技ルール規定',
            ['pageTabs' => $this->buildOtherPageTabs('rule')]
        );
    }

    public function badge(): void
    {
        $userId = isset($_GET['userId']) ? (string) $_GET['userId'] : null;
        $userList = $this->statsService->getUserList();
        $badgeDefinitions = $this->masterData['mBadgeList'] ?? [];
        $userPossessionBadgeIds = array_keys($badgeDefinitions);
        $currentUserBadgeId = $userList[$userId]['badge']['m_badge_id'] ?? 0;
        $successMessage = null;

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['m_badge_id'])) {
            $selectedBadgeId = (int) $_POST['m_badge_id'];
            if (in_array($selectedBadgeId, $userPossessionBadgeIds, true) && $userId !== null) {
                $this->userService->updateBadge((int) $userId, $selectedBadgeId);
                $currentUserBadgeId = $selectedBadgeId;
                $successMessage = '称号を「' . htmlspecialchars($badgeDefinitions[$selectedBadgeId]['name']) . '」に変更しました。';
            }
        }

        $this->renderView(__DIR__ . '/../../view/main/badge.php', [
            'userId' => $userId,
            'userList' => $userList,
            'mBadgeList' => $badgeDefinitions,
            'userPossessionBadgeIds' => $userPossessionBadgeIds,
            'currentUserBadgeId' => $currentUserBadgeId,
            'currentBadgeData' => $badgeDefinitions[$currentUserBadgeId] ?? null,
            'successMessage' => $successMessage,
            'title' => '称号',
        ]);
    }

    public function sound(): void
    {
        $this->renderStaticPage(__DIR__ . '/../../view/main/sound.php', 'サウンド');
    }

    public function add(): void
    {
        $errorMessage = null;

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $missingFields = mainFindMissingFields($_POST, ['userId', 'tableId', 'game', 'direction', 'rank', 'score', 'year', 'month', 'day']);
            if (empty($missingFields)) {
                $payload = $this->addPageDataBuilder->buildPayload($_POST);
                if ($this->gameHistoryService->create($payload)) {
                    $this->redirectIfNeeded('history?userId=' . urlencode((string) $payload['userId']));
                }

                $errorMessage = '登録処理中にエラーが発生しました。';
            } else {
                $errorMessage = '登録に失敗しました。以下のフィールドが不足しています: ' . implode(', ', $missingFields);
            }
        }

        $this->renderView(__DIR__ . '/../../view/main/add.php', $this->addPageDataBuilder->build($errorMessage));
    }

    public function bulkAdd(): void
    {
        $pageData = $this->bulkAddPageDataBuilder->build($_GET, $_POST, $_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->redirectIfNeeded($pageData['redirectUrl'] ?? null);
        unset($pageData['redirectUrl']);
        $this->renderView(__DIR__ . '/../../view/main/bulk-add.php', $pageData);
    }

    public function bulkUpdate(): void
    {
        $pageData = $this->bulkUpdatePageDataBuilder->build($_GET, $_POST, $_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->redirectIfNeeded($pageData['redirectUrl'] ?? null);
        unset($pageData['redirectUrl']);
        $this->renderView(__DIR__ . '/../../view/main/bulk-update.php', $pageData);
    }

    public function update(): void
    {
        $pageData = $this->updatePageDataBuilder->build($_POST, $_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->redirectIfNeeded($pageData['redirectUrl'] ?? null);
        unset($pageData['redirectUrl']);
        $this->renderView(__DIR__ . '/../../view/main/update.php', $pageData);
    }

    private function readSelectedYear(): string
    {
        return (string) ($_GET['year'] ?? date('Y'));
    }

    private function readSelectedStatsTerm(): string
    {
        $requestedTerm = isset($_GET['term']) ? (string)$_GET['term'] : null;
        if ($requestedTerm === null || $requestedTerm === '') {
            $requestedTerm = isset($_GET['year']) ? (string)$_GET['year'] : null;
        }

        if ($requestedTerm !== null && $requestedTerm !== '' && $this->statsService->isValidStatsTerm($requestedTerm)) {
            return $requestedTerm;
        }

        return $this->statsService->getDefaultStatsTerm();
    }

    private function readCurrentPage(): int
    {
        return max(\App\Support\Constants\AppConstants::FIRST_PAGE, (int) ($_GET['page'] ?? \App\Support\Constants\AppConstants::FIRST_PAGE));
    }

    private function renderStaticPage(string $viewPath, string $title, array $variables = []): void
    {
        $this->renderView($viewPath, array_merge(['title' => $title], $variables));
    }

    private function redirectIfNeeded(?string $redirectUrl): void
    {
        if (empty($redirectUrl)) {
            return;
        }

        header('Location: ' . $redirectUrl);
        exit();
    }

    private function buildOtherPageTabs(string $activeTab): array
    {
        return [
            [
                'label' => 'AI分析',
                'href' => 'analysis',
                'active' => $activeTab === 'analysis',
            ],
            [
                'label' => '競技規定',
                'href' => 'rule',
                'active' => $activeTab === 'rule',
            ],
            [
                'label' => '設定',
                'href' => 'setting',
                'active' => $activeTab === 'setting',
            ],
        ];
    }

    private function initializePageDataBuilders(): void
    {
        $this->statsPageDataBuilder = new StatsPageDataBuilder($this->statsService, $this->masterData, $this->statsColumn, $this->gameHistoryService);
        $this->historyPageDataBuilder = new HistoryPageDataBuilder($this->statsService, $this->masterData, $this->statsColumn, $this->gameHistoryService);
        $this->personalStatsPageDataBuilder = new PersonalStatsPageDataBuilder($this->statsService, $this->masterData, $this->statsColumn, $this->gameHistoryService);
        $this->analysisPageDataBuilder = new AnalysisPageDataBuilder($this->statsService, $this->masterData, $this->statsColumn, $this->gameHistoryService);
        $this->addPageDataBuilder = new AddPageDataBuilder($this->statsService, $this->masterData, $this->statsColumn, $this->gameHistoryService);
        $this->bulkAddPageDataBuilder = new BulkAddPageDataBuilder($this->statsService, $this->masterData, $this->statsColumn, $this->gameHistoryService);
        $this->bulkUpdatePageDataBuilder = new BulkUpdatePageDataBuilder($this->statsService, $this->masterData, $this->statsColumn, $this->gameHistoryService);
        $this->updatePageDataBuilder = new UpdatePageDataBuilder($this->statsService, $this->masterData, $this->statsColumn, $this->gameHistoryService);
    }
}


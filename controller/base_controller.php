<?php
/**
 * 各コントローラーの共通基盤となるベースクラス。
 * モデル初期化、画面データ取得、共通 service、JSON 応答などの横断処理を提供する。
 */

class BaseController
{
    protected UTitle $uTitle;
    protected UTable $uTable;
    protected MBadge $mBadge;
    protected MDirection $mDirection;
    protected MGameDay $mGameDay;
    protected MGroup $mGroup;
    protected MRule $mRule;
    protected MTitle $mTitle;
    protected \App\Services\UserService $userService;
    protected \App\Services\GameHistoryService $gameHistoryService;
    protected \App\Services\SettingService $settingService;
    protected \App\Services\TierService $tierService;
    protected \App\Services\TierHistoryService $tierHistoryService;
    protected \App\Support\MasterDataProvider $masterDataProvider;
    protected \App\Services\MasterDataService $masterDataService;

    public function __construct()
    {
        // 旧 model 互換層はまだ残っているため、初期化はここに閉じ込める。
        $this->initializeLegacyModels();
        $this->userService = \App\Support\ServiceFactory::createUserService();
        $this->gameHistoryService = \App\Support\ServiceFactory::createGameHistoryService();
        $this->settingService = \App\Support\ServiceFactory::createSettingService();
        $this->tierService = \App\Support\ServiceFactory::createTierService();
        $this->tierHistoryService = \App\Support\ServiceFactory::createTierHistoryService();
        $this->masterDataService = \App\Support\ServiceFactory::createMasterDataService(
            $this->mBadge,
            new MTier(),
            $this->mTitle,
            $this->mDirection,
            $this->mGameDay,
            $this->mGroup,
            $this->mRule,
            new MSetting(),
            new UTierHistory(),
        );
        $this->masterDataProvider = new \App\Support\MasterDataProvider(
            $this->userService,
            $this->settingService,
            $this->tierService,
            $this->tierHistoryService,
            $this->mBadge,
            $this->mDirection,
            $this->mGameDay,
            $this->mGroup,
            $this->mRule,
            $this->mTitle,
            $this->uTable,
            $this->uTitle,
            $this->gameHistoryService,
        );
    }

    /**
     * controller は描画先と view 変数だけを決め、実際の展開は ViewRenderer に委譲する。
     */
    protected function renderView(string $viewPath, array $variables = []): void
    {
        \App\Support\ViewRenderer::render($viewPath, $variables);
    }

    /**
     * main 画面向けのマスターデータ一式を取得する。
     */
    protected function getMainMasterData(): array
    {
        return $this->masterDataProvider->getMainData();
    }

    /**
     * admin 画面向けのマスターデータ一式を取得する。
     */
    protected function getAdminMasterData(): array
    {
        return $this->masterDataProvider->getAdminData();
    }

    protected function jsonResponse(array $payload, int $statusCode = 200): void
    {
        sendJsonPayload($payload, $statusCode);
    }

    protected function errorResponse(string $message, int $statusCode = 400, $data = null): void
    {
        $this->jsonResponse(buildJsonPayload(false, '', $data, $message), $statusCode);
    }

    protected function successResponse(string $message = '', $data = null): void
    {
        $this->jsonResponse(buildJsonPayload(true, $message, $data));
    }

    /**
     * service / repository 化が済んでいない既存 model の生成を 1 か所に寄せる。
     */
    private function initializeLegacyModels(): void
    {
        $this->uTitle = new UTitle();
        $this->uTable = new UTable();
        $this->mBadge = new MBadge();
        $this->mDirection = new MDirection();
        $this->mGameDay = new MGameDay();
        $this->mGroup = new MGroup();
        $this->mRule = new MRule();
        $this->mTitle = new MTitle();
    }
}

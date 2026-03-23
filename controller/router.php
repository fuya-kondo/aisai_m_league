<?php
/**
 * ルーティング定義の実体。
 * controller/action の組み合わせから、メイン画面と管理画面の処理へ振り分ける。
 */
class Router
{
    private MainController $mainController;
    private AdminController $adminController;

    public function __construct()
    {
        $this->mainController = new MainController();
        $this->adminController = new AdminController();
    }

    /**
     * リクエストを処理する。
     */
    public function handleRequest(): void
    {
        $controller = $this->resolveControllerName();
        $action = $this->resolveActionName();

        try {
            if ($controller === 'admin') {
                $this->handleAdminRequest($action);
                return;
            }

            $this->handleMainRequest($action);
        } catch (Exception $e) {
            error_log('Router error: ' . $e->getMessage());
            http_response_code(500);
            echo 'Internal Server Error';
        }
    }

    /**
     * メイン画面向けの action を解決して実行する。
     */
    private function handleMainRequest(string $action): void
    {
        $mainActionMap = [
            'top' => 'top',
            'stats' => 'stats',
            'history' => 'history',
            'personal' => 'personalStats',
            'analysis' => 'analysis',
            'analysis-history' => 'analysisHistoryDetail',
            'setting' => 'setting',
            'rule' => 'rule',
            'badge' => 'badge',
            'sound' => 'sound',
            'add' => 'add',
            'bulk-add' => 'bulkAdd',
            'bulk-update' => 'bulkUpdate',
            'update' => 'update',
        ];

        $this->dispatchAction($this->mainController, $action, $mainActionMap, 'top');
    }

    /**
     * 管理画面向けの action を解決して実行する。
     */
    private function handleAdminRequest(string $action): void
    {
        $adminActionMap = [
            'top' => 'top',
            'users' => 'user',
            'game_history' => 'history',
            'master_data' => 'master',
            'update_user' => 'updateUser',
            'add_user' => 'addUser',
            'delete_user' => 'deleteUser',
            'update_game_history' => 'updateGameHistory',
            'add_game_history' => 'addGameHistory',
            'update_master_data' => 'updateMasterData',
            'delete_data' => 'deleteData',
            'add_master_data' => 'addMasterData',
        ];

        $this->dispatchAction($this->adminController, $action, $adminActionMap, 'top');
    }

    /**
     * action 名から実行メソッドを引き当て、存在しなければ既定メソッドへフォールバックする。
     */
    private function dispatchAction(object $controller, string $action, array $actionMap, string $defaultMethod): void
    {
        $methodName = $actionMap[$action] ?? $defaultMethod;
        $controller->{$methodName}();
    }

    /**
     * controller 名は main を既定値とする。
     */
    private function resolveControllerName(): string
    {
        return (string)($_GET['controller'] ?? 'main');
    }

    /**
     * POST の action を優先し、未指定時は GET、さらに未指定なら top を採用する。
     */
    private function resolveActionName(): string
    {
        return (string)($_POST['action'] ?? $_GET['action'] ?? 'top');
    }
}



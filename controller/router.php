<?php

/**
 * アプリケーションルーター
 * リクエストに基づいて適切なコントローラーとアクションを呼び出す
 */
class Router
{
    private $mainController;
    private $adminController;
    private $authController;

    public function __construct()
    {
        $this->mainController = new MainController();
        $this->adminController = new AdminController();
        $this->authController = new AuthController();
    }

    /**
     * リクエストを処理
     */
    public function handleRequest()
    {
        $path = $this->resolvePath();

        // パスベースのAPIルーティング
        if ($path === '/auth/login') {
            $this->authController->login();
            return;
        }

        $controller = $_GET['controller'] ?? 'main';
        // POSTデータのactionを優先し、なければGETデータのactionを使用
        $action = $_POST['action'] ?? $_GET['action'] ?? 'top';

        debug_log('=== handleRequest called ===');
        debug_log('Path: ' . $path);
        debug_log('Controller: ' . $controller);
        debug_log('Action: ' . $action);
        debug_log('GET data: ' . print_r($_GET, true));
        debug_log('POST data: ' . print_r($_POST, true));

        try {
            switch ($controller) {
                case 'main':
                    debug_log('Handling main request');
                    $this->handleMainRequest($action);
                    break;
                case 'admin':
                    debug_log('Handling admin request');
                    // /admin/* 相当は role=admin を必須化
                    $this->adminController->enforceRole('admin');
                    $this->handleAdminRequest($action);
                    break;
                default:
                    debug_log('Default case - calling main top');
                    // デフォルトはメインコントローラーのtopアクション
                    $this->mainController->top();
                    break;
            }
        } catch (Exception $e) {
            // エラーハンドリング
            error_log('Router error: ' . $e->getMessage());
            http_response_code(500);
            echo 'Internal Server Error';
        }
    }

    /**
     * index.php配下でも扱えるようにリクエストパスを正規化。
     */
    private function resolvePath(): string
    {
        $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');

        if ($basePath !== '' && $basePath !== '/' && strpos($requestPath, $basePath) === 0) {
            $requestPath = substr($requestPath, strlen($basePath));
        }

        return $requestPath === '' ? '/' : $requestPath;
    }

    /**
     * メインコントローラーのリクエストを処理
     */
    private function handleMainRequest($action)
    {
        switch ($action) {
            case 'top':
                $this->mainController->top();
                break;
            case 'stats':
                $this->mainController->stats();
                break;
            case 'history':
                $this->mainController->history();
                break;
            case 'personal_stats':
                $this->mainController->personalStats();
                break;
            case 'analysis':
                $this->mainController->analysis();
                break;
            case 'setting':
                $this->mainController->setting();
                break;
            case 'rule':
                $this->mainController->rule();
                break;
            case 'badge':
                $this->mainController->badge();
                break;
            case 'sound':
                $this->mainController->sound();
                break;
            case 'add':
                $this->mainController->add();
                break;
            case 'update':
                $this->mainController->update();
                break;
            default:
                $this->mainController->top();
                break;
        }
    }

    /**
     * 管理コントローラーのリクエストを処理
     */
    private function handleAdminRequest($action)
    {
        debug_log('=== handleAdminRequest called ===');
        debug_log('Action: ' . $action);
        debug_log('POST data: ' . print_r($_POST, true));
        debug_log('GET data: ' . print_r($_GET, true));

        switch ($action) {
            case 'top':
                $this->adminController->top();
                break;
            case 'users':
                $this->adminController->user();
                break;
            case 'game_history':
                $this->adminController->history();
                break;
            case 'master_data':
                $this->adminController->master();
                break;
            case 'update_user':
                debug_log('Calling updateUser');
                $this->adminController->updateUser();
                break;
            case 'add_user':
                debug_log('Calling addUser');
                $this->adminController->addUser();
                break;
            case 'delete_user':
                debug_log('Calling deleteUser');
                $this->adminController->deleteUser();
                break;
            case 'update_game_history':
                $this->adminController->updateGameHistory();
                break;
            case 'add_game_history':
                $this->adminController->addGameHistory();
                break;
            case 'update_master_data':
                $this->adminController->updateMasterData();
                break;
            case 'delete_data':
                $this->adminController->deleteData();
                break;
            case 'add_master_data':
                $this->adminController->addMasterData();
                break;
            default:
                debug_log('Default case - calling top');
                $this->adminController->top();
                break;
        }
    }
}

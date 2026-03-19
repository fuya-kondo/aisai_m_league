<?php
namespace App\Http;

use App\Controllers\AdminController;
use App\Controllers\MainController;

/**
 * path ベースと legacy query ベースの両方を受け付ける新 router。
 * 入口を app 配下へ寄せ、dispatch 規則を map と RouteDefinition で明示する。
 */
final class Router
{
    private MainController $mainController;
    private AdminController $adminController;

    /** @var array<string, RouteDefinition> */
    private array $pathRoutes;

    public function __construct()
    {
        $this->mainController = new MainController();
        $this->adminController = new AdminController();
        $this->pathRoutes = $this->buildPathRoutes();
    }

    public function dispatch(Request $request): void
    {
        try {
            $route = $this->resolveRoute($request);
            if ($route === null) {
                Response::notFound();
                return;
            }

            $controller = $route->controller === 'admin' ? $this->adminController : $this->mainController;
            $controller->{$route->action}();
        } catch (\Throwable $throwable) {
            error_log('Application routing error: ' . $throwable->getMessage());
            Response::internalServerError();
        }
    }

    /** @return array<string, RouteDefinition> */
    private function buildPathRoutes(): array
    {
        $definitions = [
            new RouteDefinition('/', 'main', 'top'),
            new RouteDefinition('/top', 'main', 'top'),
            new RouteDefinition('/stats', 'main', 'stats'),
            new RouteDefinition('/history', 'main', 'history'),
            new RouteDefinition('/personal', 'main', 'personalStats'),
            new RouteDefinition('/analysis', 'main', 'analysis'),
            new RouteDefinition('/setting', 'main', 'setting'),
            new RouteDefinition('/rule', 'main', 'rule'),
            new RouteDefinition('/badge', 'main', 'badge'),
            new RouteDefinition('/sound', 'main', 'sound'),
            new RouteDefinition('/add', 'main', 'add'),
            new RouteDefinition('/bulk-add', 'main', 'bulkAdd'),
            new RouteDefinition('/bulk-update', 'main', 'bulkUpdate'),
            new RouteDefinition('/update', 'main', 'update'),
            new RouteDefinition('/admin', 'admin', 'top'),
            new RouteDefinition('/admin/users', 'admin', 'user'),
            new RouteDefinition('/admin/game-history', 'admin', 'history'),
            new RouteDefinition('/admin/game_history', 'admin', 'history'),
            new RouteDefinition('/admin/master-data', 'admin', 'master'),
            new RouteDefinition('/admin/master_data', 'admin', 'master'),
        ];

        $routeMap = [];
        foreach ($definitions as $definition) {
            $routeMap[$definition->path] = $definition;
        }

        return $routeMap;
    }

    private function resolveRoute(Request $request): ?RouteDefinition
    {
        $path = $request->path();
        if (isset($this->pathRoutes[$path])) {
            return $this->pathRoutes[$path];
        }

        if ($this->hasLegacyRoutingIntent($request)) {
            return $this->resolveLegacyRoute($request);
        }

        return null;
    }

    private function hasLegacyRoutingIntent(Request $request): bool
    {
        return array_key_exists('controller', $request->allQuery())
            || array_key_exists('action', $request->allQuery())
            || array_key_exists('action', $request->allPost());
    }

    private function resolveLegacyRoute(Request $request): RouteDefinition
    {
        $controllerName = $request->controller();
        $actionName = $request->action();

        $mainActionMap = [
            'top' => 'top',
            'stats' => 'stats',
            'history' => 'history',
            'personal' => 'personalStats',
            'analysis' => 'analysis',
            'setting' => 'setting',
            'rule' => 'rule',
            'badge' => 'badge',
            'sound' => 'sound',
            'add' => 'add',
            'bulk-add' => 'bulkAdd',
            'bulk-update' => 'bulkUpdate',
            'update' => 'update',
        ];

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

        if ($controllerName === 'admin') {
            $resolvedAction = $adminActionMap[$actionName] ?? 'top';
            return new RouteDefinition('/admin', 'admin', $resolvedAction);
        }

        $resolvedAction = $mainActionMap[$actionName] ?? 'top';
        return new RouteDefinition('/', 'main', $resolvedAction);
    }
}



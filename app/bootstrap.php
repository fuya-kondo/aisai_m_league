<?php
/**
 * 新しい app 配下のアプリケーション bootstrap。
 * runtime が必要とする設定・support・model・controller をここで一度だけ読み込む。
 */

require_once __DIR__ . '/../config/environment.php';
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/http/json_response.php';
require_once __DIR__ . '/../lib/validators/admin_request_validator.php';
require_once __DIR__ . '/../lib/validators/main_request_validator.php';
require_once __DIR__ . '/../lib/stats_column.php';

$applicationFiles = [
    '/Support/Constants/app_constants.php',
    '/Support/Constants/setting_names.php',
    '/Support/AppConfig.php',
    '/Support/ViewRenderer.php',
    '/Support/MasterDataProvider.php',
    '/Support/ServiceFactory.php',
    '/Support/StatsComponentFactory.php',
    '/Support/StatsServiceFactory.php',
    '/Support/MasterDataHandlerRegistry.php',
    '/Support/Admin/MasterDataViewConfig.php',
    '/Http/Request.php',
    '/Http/Response.php',
    '/Http/RouteDefinition.php',
    '/Repositories/DatabaseRepository.php',
    '/Repositories/GameHistoryRepository.php',
    '/Repositories/DailyAiCommentRepository.php',
    '/Repositories/AiAnalysisHistoryRepository.php',
    '/Repositories/UserRepository.php',
    '/Repositories/SettingRepository.php',
    '/Repositories/TierRepository.php',
    '/Repositories/TierHistoryRepository.php',
    '/Services/GamePointCalculator.php',
    '/Services/GameHistoryService.php',
    '/Services/GeminiTextGenerationService.php',
    '/Services/DailyAiCommentService.php',
    '/Services/AiAnalysisHistoryService.php',
    '/Services/MasterDataService.php',
    '/Services/ScheduleService.php',
    '/Services/StatsHistoryOrganizer.php',
    '/Services/StatsScoreAggregator.php',
    '/Services/UserService.php',
    '/Services/SettingService.php',
    '/Services/TierService.php',
    '/Services/TierHistoryService.php',
    '/../model/m_badge.php',
    '/../model/m_direction.php',
    '/../model/m_game_day.php',
    '/../model/m_group.php',
    '/../model/m_rule.php',
    '/../model/m_setting.php',
    '/../model/m_tier.php',
    '/../model/m_title.php',
    '/../model/u_game_history.php',
    '/../model/u_table.php',
    '/../model/u_tier_history.php',
    '/../model/u_title.php',
    '/../model/u_user.php',
    '/../controller/base_controller.php',
    '/../controller/admin/admin_controller.php',
    '/../controller/main/stats_source_set.php',
    '/../controller/main/stats_presenter.php',
    '/../controller/main/stats_service.php',
    '/../controller/main/pages/main_page_data_builder.php',
    '/../controller/main/pages/stats_page_data_builder.php',
    '/../controller/main/pages/history_page_data_builder.php',
    '/../controller/main/pages/personal_page_data_builder.php',
    '/../controller/main/pages/analysis_page_data_builder.php',
    '/../controller/main/pages/analysis_history_detail_page_data_builder.php',
    '/../controller/main/pages/add_page_data_builder.php',
    '/../controller/main/pages/bulk-add_page_data_builder.php',
    '/../controller/main/pages/bulk-update_page_data_builder.php',
    '/../controller/main/pages/update_page_data_builder.php',
    '/../controller/main/main_controller.php',
    '/Controllers/MainController.php',
    '/Controllers/AdminController.php',
    '/Http/Router.php',
];

foreach ($applicationFiles as $relativePath) {
    require_once __DIR__ . $relativePath;
}


<?php
use App\Support\AppConfig;

$baseUrl = rtrim((string) $baseUrl, '/');
$currentPath = AppConfig::relativeRequestPath();
$normalizePath = static fn (string $path): string => AppConfig::normalizePath($path);

$isActive = static function (array $paths) use ($currentPath, $normalizePath): bool {
    foreach ($paths as $path) {
        $targetPath = $normalizePath($path);

        if ($currentPath === $targetPath) {
            return true;
        }

        if ($targetPath !== '/' && str_starts_with($currentPath, $targetPath . '/')) {
            return true;
        }
    }

    return false;
};

$isTopActive = $isActive(['/', '/top']);
$isStatsActive = $isActive(['/stats', '/personal']);
$isAddActive = $isActive(['/bulk-add', '/add']);
$isHistoryActive = $isActive(['/history', '/update', '/bulk-update']);
$isOtherActive = $isActive(['/analysis', '/setting', '/rule']);
?>

<div class="bottom_navigation">
    <nav id="bottomNav" class="bottom_nav" aria-label="ボトムナビゲーション">
        <a
            href="<?= h($baseUrl) ?>/top"
            class="bottom_nav__item <?= $isTopActive ? 'is-active' : '' ?>"
            aria-current="<?= $isTopActive ? 'page' : 'false' ?>"
        >
            <span class="bottom_nav__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M3 10.5L12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/>
                </svg>
            </span>
            <span class="bottom_nav__label">TOP</span>
        </a>

        <a
            href="<?= h($baseUrl) ?>/stats"
            class="bottom_nav__item <?= $isStatsActive ? 'is-active' : '' ?>"
            aria-current="<?= $isStatsActive ? 'page' : 'false' ?>"
        >
            <span class="bottom_nav__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M5 19a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1zm6 0a1 1 0 0 1-1-1V10a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1zm6 0a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v15a1 1 0 0 1-1 1z"/>
                </svg>
            </span>
            <span class="bottom_nav__label">成績</span>
        </a>

        <a
            href="<?= h($baseUrl) ?>/bulk-add"
            class="bottom_nav__item <?= $isAddActive ? 'is-active' : '' ?>"
            aria-current="<?= $isAddActive ? 'page' : 'false' ?>"
        >
            <span class="bottom_nav__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M11 5a1 1 0 0 1 2 0v6h6a1 1 0 1 1 0 2h-6v6a1 1 0 1 1-2 0v-6H5a1 1 0 1 1 0-2h6z"/>
                </svg>
            </span>
            <span class="bottom_nav__label">登録</span>
        </a>

        <a
            href="<?= h($baseUrl) ?>/history"
            class="bottom_nav__item <?= $isHistoryActive ? 'is-active' : '' ?>"
            aria-current="<?= $isHistoryActive ? 'page' : 'false' ?>"
        >
            <span class="bottom_nav__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M12 4a8 8 0 1 1-7.75 10h2.1A6 6 0 1 0 12 6a5.96 5.96 0 0 0-4.24 1.76L10 10H4V4l2.34 2.34A7.96 7.96 0 0 1 12 4zm-1 4a1 1 0 0 1 2 0v3.59l2.7 2.7a1 1 0 0 1-1.4 1.42l-3-3A1 1 0 0 1 11 12z"/>
                </svg>
            </span>
            <span class="bottom_nav__label">履歴</span>
        </a>

        <a
            href="<?= h($baseUrl) ?>/analysis"
            class="bottom_nav__item <?= $isOtherActive ? 'is-active' : '' ?>"
            aria-current="<?= $isOtherActive ? 'page' : 'false' ?>"
        >
            <span class="bottom_nav__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M5 10a2 2 0 1 1 0 4 2 2 0 0 1 0-4zm7 0a2 2 0 1 1 0 4 2 2 0 0 1 0-4zm7 0a2 2 0 1 1 0 4 2 2 0 0 1 0-4z"/>
                </svg>
            </span>
            <span class="bottom_nav__label">その他</span>
        </a>
    </nav>
</div>

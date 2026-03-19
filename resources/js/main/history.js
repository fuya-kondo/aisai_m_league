/**
 * 履歴削除フォームの確認ダイアログ。
 */
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const isPersonalView = urlParams.get('view') === 'personal';
    const currentUserId = urlParams.get('userId');
    const userButtons = Array.from(document.querySelectorAll('.history-user-selector .stats-term-button'));
    const historyStorageKey = 'mah_personal_history_selected_user';

    if (isPersonalView && currentUserId) {
        window.localStorage.setItem(historyStorageKey, currentUserId);
    } else if (isPersonalView && userButtons.length > 0) {
        const storedUserId = window.localStorage.getItem(historyStorageKey);
        const fallbackButton = userButtons.find((button) => {
            try {
                return new URL(button.href, window.location.href).searchParams.get('userId') === storedUserId;
            } catch (error) {
                return false;
            }
        }) || userButtons[0];

        if (fallbackButton) {
            window.location.replace(fallbackButton.href);
            return;
        }
    }

    document.querySelectorAll('.stats-term-selector__scroll').forEach((scrollContainer) => {
        const activeButton = scrollContainer.querySelector('.stats-term-button.is-active');
        if (!activeButton) {
            return;
        }

        const targetLeft = activeButton.offsetLeft - Math.max((scrollContainer.clientWidth - activeButton.clientWidth) / 2, 0);
        scrollContainer.scrollLeft = Math.max(targetLeft, 0);
    });

    document.querySelectorAll('[data-history-delete-form]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const rank = form.dataset.rank || '';
            const score = form.dataset.score || '';
            const point = form.dataset.point || '';
            const shouldDelete = window.confirm(`${rank}位\n${score}点\n${point}Pts\n削除しますか？`);

            if (!shouldDelete) {
                window.alert('キャンセルされました');
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('[data-history-bulk-delete-form]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const playDate = form.dataset.playDate || '';
            const gameNumber = form.dataset.gameNumber || '';
            const shouldDelete = window.confirm(`${playDate} ${gameNumber}半荘目\n4件まとめて削除しますか？`);

            if (!shouldDelete) {
                window.alert('キャンセルされました');
                event.preventDefault();
            }
        });
    });
});

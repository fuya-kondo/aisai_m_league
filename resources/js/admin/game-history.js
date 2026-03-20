/**
 * ゲーム履歴管理画面のモーダル編集・削除処理。
 */
document.addEventListener('DOMContentLoaded', () => {
    const modalId = 'editModal';
    const form = document.getElementById('editForm');
    let currentGameId = null;

    if (!form || !window.AdminUi) {
        return;
    }

    const modalTitle = document.querySelector('.modal-title');
    const editGameIdInput = document.getElementById('editGameId');
    const editPlayDateInput = document.getElementById('editPlayDate');
    const editPlayTimeInput = document.getElementById('editPlayTime');
    const editGameInput = document.getElementById('editGame');
    const editUserSelect = document.getElementById('editUser');
    const editRankInput = document.getElementById('editRank');
    const editScoreInput = document.getElementById('editScore');
    const editDirectionSelect = document.getElementById('editDirection');
    const editMistakeCountInput = document.getElementById('editMistakeCount');

    function resetModal() {
        currentGameId = null;
        modalTitle.textContent = 'ゲーム履歴編集';
        form.reset();
        editGameIdInput.value = '';
        editMistakeCountInput.value = '0';
    }

    function openAddModal() {
        resetModal();
        modalTitle.textContent = 'ゲーム履歴追加';
        AdminUi.openModal(modalId);
    }

    function openEditModal(button) {
        const row = button.closest('tr[data-game-id]');
        if (!row) {
            return;
        }

        currentGameId = row.dataset.gameId;
        modalTitle.textContent = 'ゲーム履歴編集';
        editGameIdInput.value = currentGameId;
        editPlayDateInput.value = row.dataset.playDate || '';
        editPlayTimeInput.value = row.dataset.playTime || '';
        editGameInput.value = row.dataset.game || '';
        editUserSelect.value = row.dataset.userId || '';
        editRankInput.value = row.dataset.rank || '';
        editScoreInput.value = row.dataset.score || '';
        editDirectionSelect.value = row.dataset.directionId || '';
        editMistakeCountInput.value = row.dataset.mistakeCount || '0';
        AdminUi.openModal(modalId);
    }

    function isScoreInputValid() {
        const scoreValue = editScoreInput.value.trim();
        if (scoreValue !== '' && !/^-?\d*\.?\d*$/.test(scoreValue)) {
            window.alert('スコアには数値のみ入力してください。');
            editScoreInput.focus();
            return false;
        }

        return true;
    }

    AdminUi.collapseSections('.game-section');
    AdminUi.bindModalDismiss(modalId, resetModal);

    document.addEventListener('click', async (event) => {
        const addButton = event.target.closest('[data-game-add]');
        if (addButton) {
            event.preventDefault();
            openAddModal();
            return;
        }

        const editButton = event.target.closest('[data-game-edit]');
        if (editButton) {
            event.preventDefault();
            openEditModal(editButton);
            return;
        }

        const deleteButton = event.target.closest('[data-game-delete]');
        if (!deleteButton) {
            return;
        }

        event.preventDefault();
        const gameId = deleteButton.dataset.gameDelete;
        if (!window.confirm('このゲーム履歴を削除しますか？この操作は取り消せません。')) {
            return;
        }

        try {
            const payload = await AdminUi.submitAction('delete_data', { type: 'game_history', id: gameId }, {
                successMessage: '削除が完了しました',
                errorMessage: '削除に失敗しました',
            });
            if (payload.success) {
                const row = document.querySelector(`tr[data-game-id="${gameId}"]`);
                if (row) {
                    row.remove();
                }
            }
        } catch (error) {
            AdminUi.showMessage('エラーが発生しました', 'error');
        }
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!isScoreInputValid()) {
            return;
        }

        try {
            const payload = await AdminUi.submitAction(
                currentGameId ? 'update_game_history' : 'add_game_history',
                Object.fromEntries(new FormData(form).entries()),
                {
                    successMessage: '保存が完了しました',
                    errorMessage: '保存に失敗しました',
                }
            );
            if (payload.success) {
                AdminUi.closeModal(modalId);
                resetModal();
                window.location.reload();
            }
        } catch (error) {
            AdminUi.showMessage('エラーが発生しました', 'error');
        }
    });
});

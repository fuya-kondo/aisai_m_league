/**
 * ユーザー管理画面のモーダル編集・削除処理。
 */
document.addEventListener('DOMContentLoaded', () => {
    const modalId = 'editModal';
    const form = document.getElementById('editForm');
    let currentUserId = null;

    if (!form || !window.AdminUi) {
        return;
    }

    const modalTitle = document.querySelector('.modal-title');
    const editUserIdInput = document.getElementById('editUserId');
    const editLastNameInput = document.getElementById('editLastName');
    const editFirstNameInput = document.getElementById('editFirstName');
    const editBadgeSelect = document.getElementById('editBadge');
    const editTierSelect = document.getElementById('editTier');

    function resetModal() {
        currentUserId = null;
        modalTitle.textContent = 'ユーザー情報編集';
        editUserIdInput.value = '';
        editLastNameInput.value = '';
        editFirstNameInput.value = '';
        editBadgeSelect.value = '';
        editTierSelect.value = '';
    }

    function openAddModal() {
        resetModal();
        modalTitle.textContent = 'ユーザー追加';
        AdminUi.openModal(modalId);
    }

    function openEditModal(button) {
        const row = button.closest('tr[data-user-id]');
        if (!row) {
            return;
        }

        currentUserId = row.dataset.userId;
        modalTitle.textContent = 'ユーザー情報編集';
        editUserIdInput.value = currentUserId;
        editLastNameInput.value = row.dataset.lastName || '';
        editFirstNameInput.value = row.dataset.firstName || '';
        editBadgeSelect.value = row.dataset.badgeId || '';
        editTierSelect.value = row.dataset.tierId || '';
        AdminUi.openModal(modalId);
    }

    AdminUi.collapseSections('.user-section');
    AdminUi.bindModalDismiss(modalId, resetModal);

    document.addEventListener('click', async (event) => {
        const addButton = event.target.closest('[data-user-add]');
        if (addButton) {
            event.preventDefault();
            openAddModal();
            return;
        }

        const editButton = event.target.closest('[data-user-edit]');
        if (editButton) {
            event.preventDefault();
            openEditModal(editButton);
            return;
        }

        const deleteButton = event.target.closest('[data-user-delete]');
        if (!deleteButton) {
            return;
        }

        event.preventDefault();
        const userId = deleteButton.dataset.userDelete;
        if (!window.confirm('このユーザーを削除しますか？この操作は取り消せません。')) {
            return;
        }

        try {
            const payload = await AdminUi.submitAction('delete_user', { user_id: userId }, {
                successMessage: '削除が完了しました',
                errorMessage: '削除に失敗しました',
            });
            if (payload.success) {
                const row = document.querySelector(`tr[data-user-id="${userId}"]`);
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

        try {
            const payload = await AdminUi.submitAction(
                currentUserId ? 'update_user' : 'add_user',
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

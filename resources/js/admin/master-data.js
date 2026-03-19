/**
 * マスターデータ管理画面の単一モーダル制御と CRUD 送信処理。
 * 種別ごとの差分は MasterDataConfig に寄せる。
 */
document.addEventListener('DOMContentLoaded', () => {
    const modalId = 'editModal';
    const form = document.getElementById('editForm');
    const configMap = window.MasterDataConfig && typeof window.MasterDataConfig.getAll === 'function'
        ? window.MasterDataConfig.getAll()
        : {};
    const formIds = Object.values(configMap).map((config) => config.formId);
    let originalRequiredFields = [];

    if (!form || !window.AdminUi || !window.MasterDataConfig) {
        return;
    }

    function getConfig(type) {
        return window.MasterDataConfig.get(type);
    }

    // 非表示フォームの required を一時的に外し、単一モーダルでも通常 submit を使えるようにする。
    function setFieldStatesForHiddenForms() {
        originalRequiredFields = [];
        formIds.forEach((formId) => {
            const currentForm = document.getElementById(formId);
            if (!currentForm) {
                return;
            }

            currentForm.querySelectorAll('input, select, textarea').forEach((field) => {
                originalRequiredFields.push({
                    field,
                    hadRequired: field.hasAttribute('required'),
                    wasDisabled: field.disabled,
                });

                if (currentForm.style.display === 'none') {
                    field.removeAttribute('required');
                    field.disabled = true;
                } else {
                    field.disabled = false;
                }
            });
        });
    }

    function restoreFieldStates() {
        originalRequiredFields.forEach((item) => {
            if (item.hadRequired) {
                item.field.setAttribute('required', '');
            }
            item.field.disabled = item.wasDisabled;
        });
        originalRequiredFields = [];
    }

    // 見えているフォームだけを検証対象にし、最初のエラー項目へフォーカスを移す。
    function validateVisibleForm(visibleForm) {
        let isValid = true;
        let firstInvalidField = null;

        visibleForm.querySelectorAll('[required]').forEach((field) => {
            if (!field.disabled && !field.value.trim()) {
                field.style.borderColor = '#ff0000';
                isValid = false;
                if (!firstInvalidField) {
                    firstInvalidField = field;
                }
                return;
            }

            field.style.borderColor = '';
        });

        if (!isValid && firstInvalidField) {
            firstInvalidField.focus();
            AdminUi.showMessage('必須項目を入力してください', 'error');
        }

        return isValid;
    }

    function showFormByType(type) {
        formIds.forEach((formId) => {
            const currentForm = document.getElementById(formId);
            if (currentForm) {
                currentForm.style.display = 'none';
            }
        });

        const config = getConfig(type);
        if (!config) {
            return;
        }

        const targetForm = document.getElementById(config.formId);
        if (targetForm) {
            targetForm.style.display = 'block';
        }
    }

    function getVisibleForm() {
        for (const formId of formIds) {
            const currentForm = document.getElementById(formId);
            if (currentForm && window.getComputedStyle(currentForm).display !== 'none') {
                return currentForm;
            }
        }

        return null;
    }

    function resetModalState() {
        restoreFieldStates();
        document.getElementById('editId').value = '';
        document.getElementById('editTable').value = '';
    }

    // テーブル行に埋め込んだ JSON を編集フォームの初期値へ変換する。
    function parseRecord(row) {
        try {
            return JSON.parse(row.dataset.record || '{}');
        } catch (error) {
            return {};
        }
    }

    AdminUi.collapseSections('.master-section');
    AdminUi.bindModalDismiss(modalId, resetModalState);

    document.addEventListener('click', async (event) => {
        const addButton = event.target.closest('[data-master-add]');
        if (addButton) {
            event.preventDefault();
            const type = addButton.dataset.masterAdd;
            const config = getConfig(type);
            if (!config) {
                return;
            }

            document.getElementById('modalTitle').textContent = `${config.displayName}追加`;
            document.getElementById('editId').value = '';
            document.getElementById('editTable').value = type;
            showFormByType(type);
            config.reset();
            AdminUi.openModal(modalId);
            return;
        }

        const editButton = event.target.closest('[data-master-edit]');
        if (editButton) {
            event.preventDefault();
            const type = editButton.dataset.type;
            const id = editButton.dataset.id;
            const config = getConfig(type);
            const row = document.querySelector(`tr[data-type="${type}"][data-id="${id}"]`);
            if (!config || !row) {
                return;
            }

            document.getElementById('modalTitle').textContent = `${config.displayName}編集`;
            document.getElementById('editId').value = id;
            document.getElementById('editTable').value = type;
            showFormByType(type);
            config.hydrate(parseRecord(row));
            AdminUi.openModal(modalId);
            return;
        }

        const deleteButton = event.target.closest('[data-master-delete]');
        if (!deleteButton) {
            return;
        }

        event.preventDefault();
        const type = deleteButton.dataset.type;
        const id = deleteButton.dataset.id;
        const config = getConfig(type);
        const typeName = config ? config.displayName : 'データ';

        if (!window.confirm(`${typeName}を削除しますか？この操作は取り消せません。`)) {
            return;
        }

        try {
            const payload = await AdminUi.submitAction('delete_data', { type, id }, {
                successMessage: '削除が完了しました',
                errorMessage: '削除に失敗しました',
            });
            if (payload.success) {
                const row = document.querySelector(`tr[data-type="${type}"][data-id="${id}"]`);
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
        setFieldStatesForHiddenForms();

        const typeElement = document.getElementById('editTable');
        const idElement = document.getElementById('editId');
        if (!typeElement || !idElement) {
            AdminUi.showMessage('フォーム要素が見つかりません', 'error');
            restoreFieldStates();
            return;
        }

        const type = typeElement.value;
        const config = getConfig(type);
        const visibleForm = getVisibleForm();
        if (!config || !visibleForm) {
            AdminUi.showMessage('表示されているフォームが見つかりません', 'error');
            restoreFieldStates();
            return;
        }

        if (!validateVisibleForm(visibleForm)) {
            restoreFieldStates();
            return;
        }

        if (typeof config.validate === 'function' && !config.validate(visibleForm)) {
            restoreFieldStates();
            return;
        }

        const id = idElement.value;
        const isNew = !id;
        // 種別ごとの serialize 結果だけを送信し、画面側で生フォーム構造を意識させない。
        const fields = {
            type,
            data: JSON.stringify(config.serialize(visibleForm)),
        };
        if (!isNew) {
            fields.id = id;
        }

        try {
            const payload = await AdminUi.submitAction(
                isNew ? 'add_master_data' : 'update_master_data',
                fields,
                {
                    successMessage: '操作が完了しました',
                    errorMessage: 'エラーが発生しました',
                }
            );
            restoreFieldStates();

            if (payload.success) {
                AdminUi.closeModal(modalId);
                resetModalState();
                window.location.reload();
            }
        } catch (error) {
            restoreFieldStates();
            AdminUi.showMessage('エラーが発生しました', 'error');
        }
    });
});

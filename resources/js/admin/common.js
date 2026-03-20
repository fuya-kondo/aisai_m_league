/**
 * 管理画面のセクション開閉、モーダル、メッセージ表示をまとめる共通補助。
 */
(function () {
    // セクションヘッダーと本文の collapsed 状態を同時に切り替える。
    function toggleSection(header) {
        const section = header.parentElement;
        const content = section ? section.querySelector('.section-content') : null;
        if (!content) {
            return;
        }

        const isCollapsed = content.classList.contains('collapsed');
        content.classList.toggle('collapsed', !isCollapsed);
        header.classList.toggle('collapsed', !isCollapsed);
    }

    function collapseSections(sectionSelector) {
        document.querySelectorAll(sectionSelector).forEach((section) => {
            const header = section.querySelector('.section-header');
            const content = section.querySelector('.section-content');
            if (!header || !content) {
                return;
            }
            content.classList.add('collapsed');
            header.classList.add('collapsed');
        });
    }

    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'block';
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
        }
    }

    // 管理画面の成功・失敗メッセージは共通領域に集約して表示する。
    function showMessage(message, type) {
        const successElement = document.getElementById('successMessage');
        const errorElement = document.getElementById('errorMessage');

        if (!successElement || !errorElement) {
            return;
        }

        const target = type === 'success' ? successElement : errorElement;
        const other = type === 'success' ? errorElement : successElement;

        target.textContent = message;
        target.style.display = 'block';
        other.style.display = 'none';

        window.setTimeout(() => {
            target.style.display = 'none';
        }, 5000);
    }

    // AdminApiClient を経由して送信し、メッセージ表示の振る舞いもここで揃える。
    async function submitAction(action, fields = {}, options = {}) {
        if (!window.AdminApiClient) {
            throw new Error('AdminApiClient is not available');
        }

        const payload = await window.AdminApiClient.postForm(action, fields, options.endpoint || '');
        if (payload.success) {
            if (options.successMessage !== false) {
                showMessage(window.AdminApiClient.getMessage(payload, options.successMessage || '操作が完了しました'), 'success');
            }
            return payload;
        }

        if (options.errorMessage !== false) {
            showMessage(window.AdminApiClient.getMessage(payload, options.errorMessage || '操作に失敗しました'), 'error');
        }
        return payload;
    }

    function bindModalDismiss(modalId, onClose) {
        const modal = document.getElementById(modalId);
        if (!modal) {
            return;
        }

        document.addEventListener('click', (event) => {
            const closeTrigger = event.target.closest('[data-modal-close]');
            if (closeTrigger) {
                event.preventDefault();
                closeModal(modalId);
                if (typeof onClose === 'function') {
                    onClose();
                }
                return;
            }

            if (event.target === modal) {
                closeModal(modalId);
                if (typeof onClose === 'function') {
                    onClose();
                }
            }
        });
    }

    document.addEventListener('click', (event) => {
        const header = event.target.closest('[data-section-toggle]');
        if (!header) {
            return;
        }

        // ヘッダー内の操作ボタンを押したときは開閉を発火させない。
        if (event.target.closest('.section-header__actions')) {
            return;
        }

        toggleSection(header);
    });

    window.AdminUi = {
        bindModalDismiss,
        closeModal,
        collapseSections,
        openModal,
        showMessage,
        submitAction,
        toggleSection,
    };
})();

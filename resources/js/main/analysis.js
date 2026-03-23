/**
 * AI分析ページの選択状態管理とローディング制御。
 */
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('analysisForm');
    const termInput = document.getElementById('analysisTermInput');
    const userInput = document.getElementById('analysisUserInput');
    const loading = document.getElementById('analysisLoading');

    if (!form || !termInput || !userInput) {
        return;
    }

    const submitButton = form.querySelector('.analysis-submit-button');
    const selectorButtons = Array.from(document.querySelectorAll('[data-analysis-term-button], [data-analysis-user-button]'));
    const messageCards = Array.from(document.querySelectorAll('.analysis-message-card'));
    let isSubmitting = false;

    const scrollButtonIntoView = (button, behavior = 'smooth') => {
        const scrollContainer = button?.closest('.stats-term-selector__scroll');
        if (!scrollContainer || !button) {
            return;
        }

        const targetLeft = button.offsetLeft - Math.max((scrollContainer.clientWidth - button.clientWidth) / 2, 0);
        scrollContainer.scrollTo({
            left: Math.max(targetLeft, 0),
            behavior,
        });
    };

    const syncActiveState = (selector, value) => {
        document.querySelectorAll(selector).forEach((button) => {
            const isActive = button.dataset.value === value;
            button.classList.toggle('is-active', isActive);
            button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    };

    const setSelectorDisabled = (disabled) => {
        selectorButtons.forEach((button) => {
            button.disabled = disabled;
        });
    };

    const syncSubmitState = () => {
        if (!submitButton) {
            return;
        }

        submitButton.disabled = isSubmitting || !(termInput.value && userInput.value);
    };

    const scrollActiveButtonsIntoView = () => {
        document.querySelectorAll('.stats-term-selector__scroll').forEach((scrollContainer) => {
            const activeButton = scrollContainer.querySelector('.stats-term-button.is-active');
            if (!activeButton) {
                return;
            }

            scrollButtonIntoView(activeButton, 'auto');
        });
    };

    const clearRunQueryParameter = () => {
        const currentUrl = new URL(window.location.href);
        if (currentUrl.searchParams.get('run') !== '1') {
            return;
        }

        currentUrl.searchParams.delete('run');
        window.history.replaceState({}, '', currentUrl.toString());
    };

    document.querySelectorAll('[data-analysis-term-button]').forEach((button) => {
        button.addEventListener('click', (event) => {
            if (isSubmitting) {
                return;
            }

            event.preventDefault();
            termInput.value = button.dataset.value || '';
            syncActiveState('[data-analysis-term-button]', termInput.value);
            syncSubmitState();
            scrollButtonIntoView(button);
            navigateForSelection();
        });
    });

    document.querySelectorAll('[data-analysis-user-button]').forEach((button) => {
        button.addEventListener('click', (event) => {
            if (isSubmitting) {
                return;
            }

            event.preventDefault();
            userInput.value = button.dataset.value || '';
            syncActiveState('[data-analysis-user-button]', userInput.value);
            syncSubmitState();
            scrollButtonIntoView(button);
            navigateForSelection();
        });
    });

    const navigateForSelection = () => {
        const currentUrl = new URL(window.location.href);
        if (termInput.value) {
            currentUrl.searchParams.set('term', termInput.value);
        } else {
            currentUrl.searchParams.delete('term');
        }

        if (userInput.value) {
            currentUrl.searchParams.set('userId', userInput.value);
        } else {
            currentUrl.searchParams.delete('userId');
        }

        currentUrl.searchParams.delete('run');
        window.location.href = currentUrl.toString();
    };

    form.addEventListener('submit', (event) => {
        if (isSubmitting) {
            event.preventDefault();
            return;
        }

        if (!termInput.value) {
            window.alert('期間を選択してください。');
            event.preventDefault();
            return;
        }

        if (!userInput.value) {
            window.alert('選手を選択してください。');
            event.preventDefault();
            return;
        }

        isSubmitting = true;
        setSelectorDisabled(true);
        messageCards.forEach((card) => {
            card.classList.add('is-hidden');
        });
        syncSubmitState();

        if (loading) {
            loading.classList.remove('is-hidden');
        }
    });

    syncSubmitState();
    scrollActiveButtonsIntoView();
    clearRunQueryParameter();
    window.requestAnimationFrame(scrollActiveButtonsIntoView);
    window.addEventListener('load', scrollActiveButtonsIntoView, { once: true });
});

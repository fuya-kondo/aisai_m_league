/**
 * 単独成績登録フォームの席選択と入力補助。
 */
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('addForm');
    const userSelect = document.getElementById('userId');
    const gameInput = document.getElementById('game');
    const directionInput = document.getElementById('direction');
    const scoreInput = document.getElementById('score');

    if (!form || !userSelect || !gameInput || !directionInput || !scoreInput || !window.MainFormSupport) {
        return;
    }

    let gamesByUser = {};
    try {
        gamesByUser = JSON.parse(form.dataset.games || '{}');
    } catch (error) {
        gamesByUser = {};
    }

    MainFormSupport.bindDirectionButtons('[data-direction-button]', '#direction');

    userSelect.addEventListener('change', () => {
        const selectedUserId = userSelect.value;
        const currentGameValue = gamesByUser[selectedUserId] !== undefined ? Number(gamesByUser[selectedUserId]) : 0;
        gameInput.value = currentGameValue + 1;
    });

    form.addEventListener('submit', (event) => {
        if (directionInput.value === '') {
            window.alert('席を選択してください。');
            event.preventDefault();
            return;
        }

        const scoreValue = scoreInput.value.trim();
        if (scoreValue !== '' && !MainFormSupport.isDecimalScore(scoreValue)) {
            window.alert('スコアには数値のみ入力してください。');
            scoreInput.focus();
            event.preventDefault();
        }
    });
});

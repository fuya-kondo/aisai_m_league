/**
 * 履歴修正フォームの席選択と入力補助。
 */
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('updateForm');
    const directionInput = document.getElementById('direction');
    const scoreInput = document.getElementById('score');

    if (!form || !directionInput || !scoreInput || !window.MainFormSupport) {
        return;
    }

    MainFormSupport.bindDirectionButtons('[data-direction-button]', '#direction');

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

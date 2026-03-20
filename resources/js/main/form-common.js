/**
 * main 側フォームの共通補助。
 * 席ボタン、点数形式、順位パターン判定などを 1 か所へまとめる。
 */
(function () {
    const allowedRankPatterns = [
        ['1', '2', '3', '4'],
        ['1=1', '1=1', '3', '4'],
        ['1=1', '1=1', '3=3', '3=3'],
        ['1', '2=2', '2=2', '4'],
        ['1', '2', '3=3', '3=3'],
    ];

    function bindDirectionButtons(buttonSelector, hiddenInputSelector) {
        const buttons = document.querySelectorAll(buttonSelector);
        const hiddenInput = document.querySelector(hiddenInputSelector);
        if (!buttons.length || !hiddenInput) {
            return;
        }

        buttons.forEach((button) => {
            button.addEventListener('click', () => {
                buttons.forEach((target) => target.classList.remove('selected'));
                button.classList.add('selected');
                hiddenInput.value = button.value;
            });
        });
    }

    function isDecimalScore(value) {
        return /^-?\d*\.?\d*$/.test(value);
    }

    function isIntegerScore(value) {
        return /^-?\d+$/.test(value);
    }

    function isValidRankPattern(ranks) {
        const sortedRanks = [...ranks].sort();
        return allowedRankPatterns.some((pattern) => JSON.stringify(sortedRanks) === JSON.stringify([...pattern].sort()));
    }

    window.MainFormSupport = {
        bindDirectionButtons,
        isDecimalScore,
        isIntegerScore,
        isValidRankPattern,
    };
})();


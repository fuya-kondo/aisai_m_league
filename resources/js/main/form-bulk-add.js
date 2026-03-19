/**
 * 一括登録フォームの入力検証と合計点表示。
 */
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('bulkAddForm') || document.getElementById('bulkUpdateForm');
    const scoreInputs = document.querySelectorAll('.score-input');
    const totalElement = document.getElementById('scoreTotal');
    const directions = [1, 2, 3, 4];

    if (!form || !totalElement || !window.MainFormSupport) {
        return;
    }

    const scoreTotalUnits = parseInt(totalElement.dataset.scoreTotalUnits || '1000', 10);
    const scoreTotal = parseInt(totalElement.dataset.scoreTotal || String(scoreTotalUnits * 100), 10);

    function deriveRanksFromScores(scoresByDirection) {
        const entries = Object.entries(scoresByDirection).sort((a, b) => b[1] - a[1]);
        if (entries.length !== 4) {
            return null;
        }

        const groups = [];
        for (const [direction, score] of entries) {
            const lastGroup = groups[groups.length - 1];
            if (lastGroup && lastGroup.score === score) {
                lastGroup.directions.push(direction);
            } else {
                groups.push({ score, directions: [direction] });
            }
        }

        let currentRank = 1;
        const ranks = {};
        for (const group of groups) {
            if (group.directions.length > 2) {
                return null;
            }

            const rankLabel = group.directions.length === 2 ? `${currentRank}=${currentRank}` : String(currentRank);
            for (const direction of group.directions) {
                ranks[direction] = rankLabel;
            }
            currentRank += group.directions.length;
        }

        const rankValues = directions.map((direction) => ranks[String(direction)]);
        return MainFormSupport.isValidRankPattern(rankValues) ? ranks : null;
    }

    function updateScoreTotal() {
        let total = 0;
        scoreInputs.forEach((input) => {
            const value = input.value.trim();
            if (MainFormSupport.isIntegerScore(value)) {
                total += parseInt(value, 10);
            }
        });

        totalElement.textContent = `合計: ${(total * 100).toLocaleString()} 点`;
        totalElement.classList.toggle('ok', total === scoreTotalUnits);
        totalElement.classList.toggle('ng', total !== scoreTotalUnits);
    }

    scoreInputs.forEach((input) => input.addEventListener('input', updateScoreTotal));
    updateScoreTotal();

    form.addEventListener('submit', (event) => {
        const userIds = [];
        const scoresByDirection = {};
        let totalScore = 0;

        for (const direction of directions) {
            const userId = document.getElementById(`userId_${direction}`).value;
            const score = document.getElementById(`score_${direction}`).value.trim();
            const mistake = document.getElementById(`mistake_count_${direction}`).value.trim();

            if (!userId || score === '') {
                window.alert('東南西北すべてに選手・点数を入力してください。');
                event.preventDefault();
                return;
            }

            if (!MainFormSupport.isIntegerScore(score)) {
                window.alert('点数は100点単位の整数で入力してください。');
                event.preventDefault();
                return;
            }

            if (mistake !== '' && !/^\d+$/.test(mistake)) {
                window.alert('チョンボ回数は0〜99の整数で入力してください。');
                event.preventDefault();
                return;
            }

            const mistakeCount = mistake === '' ? 0 : parseInt(mistake, 10);
            if (mistakeCount < 0 || mistakeCount > 99) {
                window.alert('チョンボ回数は0〜99の整数で入力してください。');
                event.preventDefault();
                return;
            }

            userIds.push(userId);
            scoresByDirection[String(direction)] = parseInt(score, 10);
            totalScore += parseInt(score, 10);
        }

        if (new Set(userIds).size !== 4) {
            window.alert('選手は4席で重複できません。');
            event.preventDefault();
            return;
        }

        if (totalScore !== scoreTotalUnits) {
            window.alert(`4人の点数合計は${scoreTotalUnits.toLocaleString()}（=${scoreTotal.toLocaleString()}点）である必要があります。`);
            event.preventDefault();
            return;
        }

        if (!deriveRanksFromScores(scoresByDirection)) {
            window.alert('順位を自動判定できません。同点は2人までにしてください。');
            event.preventDefault();
        }
    });
});


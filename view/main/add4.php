<?php

// Include header
include __DIR__ . '/../header.php';

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <link rel="apple-touch-icon" href="<?= h($baseUrl) ?>/favicon.png">
    <link rel="icon" href="<?= h($baseUrl) ?>/favicon.ico" sizes="64x64" type="image/x-icon">
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/master.css">
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/header.css">
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/app.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;700&display=swap" rel="stylesheet">
    <title><?= h($title) ?></title>
</head>
<body>
<main>
    <div class="page-title"><?= h($title) ?></div>

    <?php if (!empty($errorMessages)): ?>
        <div class="error-box">
            <ul>
                <?php foreach ($errorMessages as $message): ?>
                    <li><?= h($message) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="form-container container">
        <form action="add4" method="post" class="registration-form" onsubmit="return validateAdd4Form()">
            <?= csrf_field() ?>
            <input type="hidden" name="tableId" value="<?= h($formData['table_id']) ?>">

            <div class="form-row meta-row">
                <div class="form-group game-group">
                    <input id="game" class="input" type="number" name="game" min="1" required value="<?= h($formData['game']) ?>">
                    <label for="game">半荘目</label>
                </div>
            </div>

            <div class="score-total" id="scoreTotal">合計: 0（= 0 点）</div>

            <div class="compact-table-wrap">
                <table class="compact-table">
                    <caption class="compact-caption">上から順に: 選手 / 順位 / 点数(100点単位) / チョンボ</caption>
                    <thead>
                        <tr>
                            <?php for ($direction = 1; $direction <= 4; $direction++): ?>
                                <th><?= h($directionLabels[$direction]) ?></th>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php for ($direction = 1; $direction <= 4; $direction++): ?>
                                <?php $seat = $formData['seats'][$direction] ?? ['user_id' => '', 'rank' => '', 'score' => '', 'mistake_count' => 0]; ?>
                                <td>
                                    <select id="userId_<?= h($direction) ?>" class="input" name="userId_<?= h($direction) ?>" required>
                                        <option value="">-</option>
                                        <?php foreach ($userList as $userId => $userData): ?>
                                            <option value="<?= h($userId) ?>" <?= (string)$seat['user_id'] === (string)$userId ? 'selected' : '' ?>>
                                                <?= h($userData['last_name'] . $userData['first_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            <?php endfor; ?>
                        </tr>
                        <tr>
                            <?php for ($direction = 1; $direction <= 4; $direction++): ?>
                                <?php $seat = $formData['seats'][$direction] ?? ['user_id' => '', 'rank' => '', 'score' => '', 'mistake_count' => 0]; ?>
                                <td>
                                    <select id="rank_<?= h($direction) ?>" class="input" name="rank_<?= h($direction) ?>" required>
                                        <option value="">-</option>
                                        <?php foreach ($rankConfig as $value => $name): ?>
                                            <option value="<?= h($value) ?>" <?= (string)$seat['rank'] === (string)$value ? 'selected' : '' ?>>
                                                <?= h($name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            <?php endfor; ?>
                        </tr>
                        <tr>
                            <?php for ($direction = 1; $direction <= 4; $direction++): ?>
                                <?php $seat = $formData['seats'][$direction] ?? ['user_id' => '', 'rank' => '', 'score' => '', 'mistake_count' => 0]; ?>
                                <td>
                                    <div class="score-input-wrap">
                                        <input
                                            id="score_<?= h($direction) ?>"
                                            class="input score-input"
                                            type="text"
                                            name="score_<?= h($direction) ?>"
                                            inputmode="numeric"
                                            pattern="-?[0-9]+"
                                            placeholder="250"
                                            required
                                            value="<?= h($seat['score']) ?>"
                                        >
                                        <span class="score-suffix">00</span>
                                    </div>
                                </td>
                            <?php endfor; ?>
                        </tr>
                        <tr>
                            <?php for ($direction = 1; $direction <= 4; $direction++): ?>
                                <?php $seat = $formData['seats'][$direction] ?? ['user_id' => '', 'rank' => '', 'score' => '', 'mistake_count' => 0]; ?>
                                <td>
                                    <input
                                        id="mistake_count_<?= h($direction) ?>"
                                        class="input"
                                        type="number"
                                        name="mistake_count_<?= h($direction) ?>"
                                        min="0"
                                        max="99"
                                        value="<?= h($seat['mistake_count']) ?>"
                                    >
                                </td>
                            <?php endfor; ?>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="form-group date-group date-group-bottom">
                <label>対局日</label>
                <div class="date-inputs">
                    <?php $currentYear = (int)date('Y'); ?>
                    <select id="year" class="input play_date year" name="year" required>
                        <?php for ($year = $currentYear - 1; $year <= $currentYear + 1; $year++): ?>
                            <option value="<?= h($year) ?>" <?= (int)$formData['year'] === $year ? 'selected' : '' ?>><?= h($year) ?></option>
                        <?php endfor; ?>
                    </select>
                    <span>年</span>
                    <select id="month" class="input play_date month" name="month" required>
                        <?php for ($month = 1; $month <= 12; $month++): ?>
                            <option value="<?= h($month) ?>" <?= (int)$formData['month'] === $month ? 'selected' : '' ?>><?= h($month) ?></option>
                        <?php endfor; ?>
                    </select>
                    <span>月</span>
                    <select id="day" class="input play_date day" name="day" required>
                        <?php for ($day = 1; $day <= 31; $day++): ?>
                            <option value="<?= h($day) ?>" <?= (int)$formData['day'] === $day ? 'selected' : '' ?>><?= h($day) ?></option>
                        <?php endfor; ?>
                    </select>
                    <span>日</span>
                </div>
            </div>

            <button class="submit-button btn-primary" type="submit">4人まとめて登録する</button>
        </form>
    </div>
</main>
</body>
</html>

<script>
    function validateAdd4Form() {
        const directions = [1, 2, 3, 4];
        const userIds = [];
        const ranks = [];
        let totalScore = 0;

        for (const direction of directions) {
            const userId = document.getElementById(`userId_${direction}`).value;
            const rank = document.getElementById(`rank_${direction}`).value;
            const score = document.getElementById(`score_${direction}`).value.trim();
            const mistake = document.getElementById(`mistake_count_${direction}`).value.trim();

            if (!userId || !rank || score === '') {
                alert('東南西北すべてに選手・順位・点数を入力してください。');
                return false;
            }

            if (!/^-?\d+$/.test(score)) {
                alert('点数は100点単位の整数で入力してください。');
                return false;
            }

            if (mistake !== '' && !/^\d+$/.test(mistake)) {
                alert('チョンボ回数は0〜99の整数で入力してください。');
                return false;
            }

            const mistakeCount = mistake === '' ? 0 : parseInt(mistake, 10);
            if (mistakeCount < 0 || mistakeCount > 99) {
                alert('チョンボ回数は0〜99の整数で入力してください。');
                return false;
            }

            userIds.push(userId);
            ranks.push(rank);
            totalScore += parseInt(score, 10);
        }

        if (new Set(userIds).size !== 4) {
            alert('選手は4席で重複できません。');
            return false;
        }

        if (totalScore !== 1000) {
            alert('4人の点数合計は1000（=100000点）である必要があります。');
            return false;
        }

        if (!isValidRankPattern(ranks)) {
            alert('順位の組み合わせが不正です。');
            return false;
        }

        return true;
    }

    function isValidRankPattern(ranks) {
        const sorted = [...ranks].sort();
        const allowedPatterns = [
            ['1', '2', '3', '4'],
            ['1=1', '1=1', '3', '4'],
            ['1', '2=2', '2=2', '4'],
            ['1', '2', '3=3', '3=3'],
        ];

        return allowedPatterns.some((pattern) => {
            const patternSorted = [...pattern].sort();
            return JSON.stringify(sorted) === JSON.stringify(patternSorted);
        });
    }

    function updateScoreTotal() {
        const scoreInputs = document.querySelectorAll('.score-input');
        let total = 0;

        scoreInputs.forEach((input) => {
            const value = input.value.trim();
            if (/^-?\d+$/.test(value)) {
                total += parseInt(value, 10);
            }
        });

        const totalEl = document.getElementById('scoreTotal');
        totalEl.textContent = `合計: ${total.toLocaleString()}（= ${(total * 100).toLocaleString()} 点）`;
        totalEl.classList.toggle('ok', total === 1000);
        totalEl.classList.toggle('ng', total !== 1000);
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.score-input').forEach((input) => {
            input.addEventListener('input', updateScoreTotal);
        });
        updateScoreTotal();
    });
</script>

<style>
    .form-container.container {
        max-width: 980px;
        margin: 0 auto;
    }

    .error-box {
        margin: 10px auto 20px auto;
        max-width: 980px;
        padding: 10px 14px;
        border: 1px solid #e74c3c;
        background: #fff4f2;
        border-radius: 6px;
        color: #c0392b;
    }

    .error-box ul {
        margin: 0;
        padding-left: 18px;
    }

    .meta-row {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 12px;
    }

    .date-inputs {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .play_date.year { width: 110px; }
    .play_date.month, .play_date.day { width: 84px; }

    .game-group {
        min-width: 180px;
        max-width: 220px;
        display: grid;
        grid-template-columns: 1fr 64px;
        gap: 8px;
        align-items: end;
    }

    .game-group > label {
        margin-bottom: 0;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        font-weight: 700;
    }

    .score-total {
        font-weight: 700;
        margin: 4px 0 12px 0;
        padding: 8px 10px;
        border-radius: 6px;
        background: #f8f8f8;
    }

    .score-total.ok {
        color: #0b7a38;
        background: #eaf8ef;
    }

    .score-total.ng {
        color: #b03a2e;
        background: #fdf0ee;
    }

    .compact-table-wrap {
        overflow-x: auto;
        margin-bottom: 12px;
    }

    .compact-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .compact-caption {
        caption-side: top;
        text-align: left;
        color: #666;
        font-size: 12px;
        margin-bottom: 6px;
    }

    .compact-table th,
    .compact-table td {
        border: 1px solid #ddd;
        padding: 6px;
        text-align: center;
        background: #fff;
    }

    .score-input-wrap {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 4px;
        align-items: center;
    }

    .score-suffix {
        font-size: 12px;
        color: #666;
        white-space: nowrap;
    }

    .compact-table thead th {
        background: #f3f9f5;
        font-weight: 700;
        color: #009944;
    }

    .form-group {
        margin-bottom: 10px;
    }

    .form-group > label {
        display: block;
        margin-bottom: 4px;
        font-weight: 600;
        color: #333;
    }

    .date-group-bottom {
        margin-bottom: 12px;
    }

    .input,
    select,
    input[type="text"],
    input[type="number"] {
        width: 100%;
        padding: 9px 10px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 6px;
        box-sizing: border-box;
        text-align: right;
    }

    .submit-button {
        width: 100%;
        margin-top: 6px;
    }

    @media (max-width: 768px) {
        .meta-row {
            justify-content: stretch;
        }
        .game-group {
            max-width: none;
            min-width: 0;
        }
        .compact-table th,
        .compact-table td {
            padding: 4px;
        }
        .compact-table .input {
            font-size: 12px;
            padding: 6px 4px;
        }
    }
</style>

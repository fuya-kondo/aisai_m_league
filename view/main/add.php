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
    <title><?= h($title) ?></title>
</head>
<body>
<main>
    <div class="page-title"><?= h($title) ?></div>
    <?php if (isset($error_msg)): ?><div class="error-message"><?= h($error_msg) ?></div><?php endif; ?>
    <div class="form-container container">
        <form action="add" method="post" class="registration-form" onsubmit="return validateForm()">
            <?= csrf_field() ?>
            <input type="hidden" name="tableId" value="1">
            <div class="form-group game">
                <input id="game" class="input" type="number" name="game" required value="1" min="1">
                <label for="game">半荘目</label>
            </div>

            <?php for ($seat = 1; $seat <= 4; $seat++): ?>
            <fieldset class="seat-block">
                <legend><?= h($mDirectionList[$seat]['name'] ?? ('席' . $seat)) ?></legend>
                <select class="input" name="participants[<?= $seat ?>][playerId]" required>
                    <option value="">選手を選択</option>
                    <?php foreach($userList as $userId => $userData): ?>
                        <option value="<?= h($userId) ?>"><?= h($userData['last_name'] . $userData['first_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select class="input" name="participants[<?= $seat ?>][rank]" required>
                    <option value="">順位</option>
                    <?php foreach($rankConfig as $value => $name): ?>
                        <option value="<?= h($value) ?>"><?= h($name) ?></option>
                    <?php endforeach; ?>
                </select>
                <input class="input" type="number" name="participants[<?= $seat ?>][score]" required placeholder="点数">
                <input class="input" type="number" name="participants[<?= $seat ?>][chombo]" min="0" max="99" value="0" placeholder="チョンボ">
            </fieldset>
            <?php endfor; ?>

            <div class="form-group date-group">
                <div class="date-inputs">
                    <?php $currentYear = (int)date('Y'); ?>
                    <select class="input play_date year" name="year" required><?php for ($year=$currentYear-1;$year<=$currentYear+1;$year++): ?><option value="<?= h($year) ?>" <?= $year === $currentYear ? 'selected' : '' ?>><?= h($year) ?></option><?php endfor; ?></select><span>年</span>
                    <select class="input play_date month" name="month" required><?php for ($month=1;$month<=12;$month++): ?><option value="<?= h($month) ?>" <?= $month == date('n') ? 'selected' : '' ?>><?= h($month) ?></option><?php endfor; ?></select><span>月</span>
                    <select class="input play_date day" name="day" required><?php for ($day=1;$day<=31;$day++): ?><option value="<?= h($day) ?>" <?= $day == date('j') ? 'selected' : '' ?>><?= h($day) ?></option><?php endfor; ?></select><span>日</span>
                </div>
            </div>
            <button class="submit-button btn-primary" type="submit">登録する</button>
        </form>
    </div>
</main>
<script>
function validateForm() {
  const blocks = document.querySelectorAll('.seat-block');
  for (const block of blocks) {
    const player = block.querySelector('select[name*="[playerId]"]');
    const rank = block.querySelector('select[name*="[rank]"]');
    const score = block.querySelector('input[name*="[score]"]');
    if (!player.value || !rank.value || score.value === '') {
      alert('4人分の選手・順位・点数をすべて入力してください。');
      return false;
    }
  }
  return true;
}
</script>
</body>
</html>

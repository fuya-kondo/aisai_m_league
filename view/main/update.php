<?php
include __DIR__ . '/../header.php';
?>
<!DOCTYPE html>
<html lang="ja"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/master.css">
<link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/header.css">
<link rel="stylesheet" href="<?= h($baseUrl) ?>/resources/css/app.css">
<title><?= h($title) ?></title>
</head><body><main>
<?php if (isset($error_msg)): ?><div class="error-message"><?= h($error_msg) ?></div><?php endif; ?>
<?php if ($isFix && $editGame): ?>
<div class="page-title"><?= h($title) ?></div>
<div class="container">
<form action="update" method="post" onsubmit="return validateForm()">
<?= csrf_field() ?>
<input type="hidden" name="original_game_id" value="<?= h($editGame['game_id']) ?>">
<input type="hidden" name="userId" value="<?= h($userId) ?>">
<div class="date-inputs">
<?php $ts = strtotime($editGame['play_date']); ?>
<input class="input" type="number" name="new_game" required value="<?= h($editGame['game']) ?>" min="1"><label>半荘目</label>
<select class="input" name="new_year" required><?php $cy=(int)date('Y'); for($y=$cy-1;$y<=$cy+1;$y++): ?><option value="<?=h($y)?>" <?= $y==(int)date('Y',$ts)?'selected':'' ?>><?=h($y)?></option><?php endfor; ?></select><span>年</span>
<select class="input" name="new_month" required><?php for($m=1;$m<=12;$m++): ?><option value="<?=h($m)?>" <?= $m==(int)date('n',$ts)?'selected':'' ?>><?=h($m)?></option><?php endfor; ?></select><span>月</span>
<select class="input" name="new_day" required><?php for($d=1;$d<=31;$d++): ?><option value="<?=h($d)?>" <?= $d==(int)date('j',$ts)?'selected':'' ?>><?=h($d)?></option><?php endfor; ?></select><span>日</span>
</div>
<?php foreach($editGame['participants'] as $p): $seat=(int)$p['seat']; ?>
<fieldset class="seat-block"><legend><?= h($mDirectionList[$seat]['name'] ?? ('席'.$seat)) ?></legend>
<select class="input" name="participants[<?= $seat ?>][playerId]" required>
<?php foreach($userList as $uid => $userData): ?>
<option value="<?= h($uid) ?>" <?= (int)$uid === (int)$p['playerId'] ? 'selected' : '' ?>><?= h($userData['last_name'] . $userData['first_name']) ?></option>
<?php endforeach; ?>
</select>
<select class="input" name="participants[<?= $seat ?>][rank]" required>
<?php foreach($rankConfig as $value => $name): ?><option value="<?= h($value) ?>" <?= (string)$value === (string)$p['rank'] ? 'selected' : '' ?>><?= h($name) ?></option><?php endforeach; ?>
</select>
<input class="input" type="number" name="participants[<?= $seat ?>][score]" required value="<?= h($p['score']) ?>">
<input class="input" type="number" name="participants[<?= $seat ?>][chombo]" min="0" max="99" value="<?= h($p['chombo']) ?>">
</fieldset>
<?php endforeach; ?>
<button class="submit-button btn-primary" type="submit">修正する</button>
</form></div>
<?php endif; ?>
</main>
<script>
function validateForm(){
 for(const b of document.querySelectorAll('.seat-block')){
   if(!b.querySelector('select[name*="[playerId]"]').value||!b.querySelector('select[name*="[rank]"]').value||b.querySelector('input[name*="[score]"]').value===''){alert('4人分を入力してください。');return false;}
 }
 return true;
}
</script>
</body></html>

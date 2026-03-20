<table class="score-table score-table--panel">
    <thead>
        <tr>
            <?php foreach ($tableColumns as $column): ?>
                <th><?= h($column['label']) ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tableRows as $row): ?>
            <tr align="center">
                <?php foreach ($row['cells'] as $cell): ?>
                    <?php if ($cell['type'] === 'ranking'): ?>
                        <td>
                            <span class="rank-column">
                                <span class="rank-icon rank-<?= h($cell['value']) ?>">
                                    <span class="stats-value"><?= h($cell['value']) ?></span>
                                </span>
                            </span>
                        </td>
                    <?php elseif ($cell['type'] === 'player'): ?>
                        <td
                            class="player-point-bar"
                            <?php if ($cell['barValue'] !== null): ?>
                                data-bar-direction="<?= h($cell['barDirection']) ?>"
                                data-bar-value="<?= h((string)$cell['barValue']) ?>"
                            <?php endif; ?>
                        >
                            <a href="<?= h($cell['href']) ?>">
                                <span class="stats-value"><?= h($cell['value']) ?></span>
                            </a>
                        </td>
                    <?php else: ?>
                        <td><span class="stats-value"><?= h($cell['value']) ?></span></td>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

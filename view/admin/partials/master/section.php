<div class="master-section">
    <div class="section-header" data-section-toggle>
        <h3 class="section-title"><?= h($section['title']) ?></h3>
        <div class="section-header__actions">
            <button class="add-button" type="button" data-master-add="<?= h($section['type']) ?>">追加</button>
            <span class="toggle-icon">▼</span>
        </div>
    </div>
    <div class="section-content">
        <div class="table-container">
            <table class="master-table">
                <thead>
                    <tr>
                        <?php foreach ($section['columns'] as $columnLabel): ?>
                            <th><?= h($columnLabel) ?></th>
                        <?php endforeach; ?>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($section['rows'] as $row): ?>
                        <tr data-id="<?= h($row['id']) ?>" data-type="<?= h($section['type']) ?>" data-record="<?= h($row['recordJson']) ?>">
                            <?php foreach ($row['cells'] as $cell): ?>
                                <td>
                                    <?php if ($cell['type'] === 'color'): ?>
                                        <span class="color-preview dynamic-color-bg" data-bg-color="<?= h($cell['value']) ?>"></span>
                                        <?= h($cell['value']) ?>
                                    <?php else: ?>
                                        <?= h($cell['value']) ?>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-edit" type="button" data-master-edit data-type="<?= h($section['type']) ?>" data-id="<?= h($row['id']) ?>">編集</button>
                                    <button class="btn-delete" type="button" data-master-delete data-type="<?= h($section['type']) ?>" data-id="<?= h($row['id']) ?>">削除</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

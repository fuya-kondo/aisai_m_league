<?php foreach ($masterForms as $form): ?>
    <div id="<?= h($form['formId']) ?>" class="is-hidden-form">
        <?php foreach ($form['fields'] as $field): ?>
            <div class="form-group">
                <label class="form-label" for="<?= h($field['id']) ?>"><?= h($field['label']) ?></label>
                <input
                    type="<?= h($field['type']) ?>"
                    id="<?= h($field['id']) ?>"
                    name="<?= h($field['name']) ?>"
                    class="<?= h($field['type'] === 'color' ? 'form-color' : 'form-input') ?>"
                    <?php if (!empty($field['required'])): ?>required<?php endif; ?>
                    <?php if (isset($field['min'])): ?>min="<?= h((string)$field['min']) ?>"<?php endif; ?>
                    <?php if (isset($field['max'])): ?>max="<?= h((string)$field['max']) ?>"<?php endif; ?>
                >
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

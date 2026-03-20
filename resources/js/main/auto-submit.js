/**
 * data-auto-submit を持つ入力項目の change 時に親フォームを送信する。
 */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-auto-submit]').forEach((field) => {
        field.addEventListener('change', () => {
            if (field.form) {
                field.form.submit();
            }
        });
    });
});

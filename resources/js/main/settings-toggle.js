/**
 * 設定トグル押下時の見た目だけを即時反映する。
 */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.toggle-button').forEach((button) => {
        const toggleText = button.querySelector('.toggle-text');
        button.addEventListener('click', () => {
            button.classList.toggle('on');
            button.classList.toggle('off');
            if (toggleText) {
                toggleText.textContent = button.classList.contains('on') ? 'ON' : 'OFF';
            }
        });
    });
});

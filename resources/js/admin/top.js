/**
 * 管理画面トップのカード押下表現。
 */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.nav-card').forEach((card) => {
        card.addEventListener('click', () => {
            card.style.transform = 'scale(0.98)';
            window.setTimeout(() => {
                card.style.transform = '';
            }, 150);
        });
    });
});

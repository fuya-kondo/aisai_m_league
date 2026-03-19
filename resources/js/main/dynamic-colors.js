/**
 * data-color / data-bg-color の値を CSS 変数へ反映する。
 * HTML の inline style を避けつつ、データ依存色を表示するために使う。
 */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-color]').forEach((element) => {
        element.style.setProperty('--dynamic-color', element.dataset.color || '');
    });

    document.querySelectorAll('[data-bg-color]').forEach((element) => {
        element.style.setProperty('--dynamic-bg-color', element.dataset.bgColor || '');
    });
});

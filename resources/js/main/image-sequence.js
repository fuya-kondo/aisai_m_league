/**
 * トップ・ルール画面で共通利用する画像順次表示。
 */
document.addEventListener('DOMContentLoaded', () => {
    const images = document.querySelectorAll('.playerImg img');
    if (images.length === 0) {
        return;
    }

    let currentIndex = -1;
    function showNextImage() {
        currentIndex += 1;
        if (currentIndex < images.length) {
            images[currentIndex].classList.add('active');
            window.setTimeout(showNextImage, 1000);
        }
    }

    window.setTimeout(showNextImage, 1000);
});

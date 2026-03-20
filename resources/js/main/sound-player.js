/**
 * 発声ボタンと audio 要素を接続する。
 */
document.addEventListener('DOMContentLoaded', () => {
    const audioPlayer = document.getElementById('audioPlayer');
    if (!audioPlayer) {
        return;
    }

    document.querySelectorAll('.sound-button').forEach((button) => {
        button.addEventListener('click', () => {
            const soundFile = button.dataset.sound;
            if (!soundFile) {
                return;
            }

            audioPlayer.src = soundFile;
            audioPlayer.play();
        });
    });
});

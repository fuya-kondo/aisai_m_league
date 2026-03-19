/**
 * 個人成績画面のチャート描画とスクロール補助。
 */
document.addEventListener('DOMContentLoaded', () => {
    const playerStorageKey = 'mah_personal_selected_player';
    const currentPlayer = new URLSearchParams(window.location.search).get('player');
    const playerButtons = Array.from(document.querySelectorAll("[aria-label=\"選手\"] .stats-term-button"));

    if (currentPlayer) {
        window.localStorage.setItem(playerStorageKey, currentPlayer);
    } else if (playerButtons.length > 0) {
        const storedPlayer = window.localStorage.getItem(playerStorageKey);
        const fallbackButton = playerButtons.find((button) => {
            try {
                return new URL(button.href, window.location.href).searchParams.get('player') === storedPlayer;
            } catch (error) {
                return false;
            }
        }) || playerButtons[0];

        if (fallbackButton) {
            window.location.replace(fallbackButton.href);
            return;
        }
    }

    const scrollActiveButtonsIntoView = () => {
        document.querySelectorAll('.stats-term-selector__scroll').forEach((scrollContainer) => {
            const activeButton = scrollContainer.querySelector('.stats-term-button.is-active');
            if (!activeButton) {
                return;
            }

            const targetLeft = activeButton.offsetLeft - Math.max((scrollContainer.clientWidth - activeButton.clientWidth) / 2, 0);
            scrollContainer.scrollLeft = Math.max(targetLeft, 0);
        });
    };

    scrollActiveButtonsIntoView();
    window.requestAnimationFrame(scrollActiveButtonsIntoView);
    window.addEventListener('load', scrollActiveButtonsIntoView, { once: true });

    const dataElement = document.getElementById('personal-stats-chart-data');
    if (dataElement && typeof Chart !== 'undefined') {
        const playerData = JSON.parse(dataElement.textContent || '{}');
        const rankingCanvas = document.getElementById('rankingChart');
        const performanceCanvas = document.getElementById('performanceRadarChart');

        if (rankingCanvas) {
            new Chart(rankingCanvas.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: ['1着', '2着', '3着', '4着'],
                    datasets: [{
                        data: [
                            parseFloat(playerData.rank_probability[1].replace('%', '')),
                            parseFloat(playerData.rank_probability[2].replace('%', '')),
                            parseFloat(playerData.rank_probability[3].replace('%', '')),
                            parseFloat(playerData.rank_probability[4].replace('%', '')),
                        ],
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
                    }],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label(context) {
                                    const label = context.label || '';
                                    const value = context.raw;
                                    const total = context.dataset.data.reduce((left, right) => left + right, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value}回 (${percentage}%)`;
                                },
                            },
                        },
                    },
                },
            });
        }

        if (performanceCanvas) {
            const averageScore = String(playerData.average_score).replace(/,/g, '');
            const baselineMax = 100;
            const averageData = {
                top_probability: 25,
                over_second_probability: 50,
                over_third_probability: 75,
                average_score: 25000,
            };

            function normalizeData(value, min, max) {
                const normalized = ((value - min) / (max - min)) * 100;
                return normalized < 0 ? 0 : normalized;
            }

            new Chart(performanceCanvas.getContext('2d'), {
                type: 'radar',
                data: {
                    labels: ['トップ率', '連対率', 'ラス回避率', '平均点'],
                    datasets: [
                        {
                            label: '平均値',
                            data: [
                                normalizeData(averageData.top_probability, 20, 30),
                                normalizeData(averageData.over_second_probability, 40, 60),
                                normalizeData(averageData.over_third_probability, 60, 85),
                                normalizeData(averageData.average_score, 20000, 30000),
                            ],
                            backgroundColor: 'rgba(100, 149, 237, 0.2)',
                            borderColor: 'rgba(100, 149, 237, 0.8)',
                            borderWidth: 1,
                            pointRadius: 2,
                            fill: true,
                            order: 2,
                        },
                        {
                            label: playerData.name,
                            data: [
                                normalizeData(parseFloat(playerData.rank_probability[1].replace('%', '')), 20, 30),
                                normalizeData(parseFloat(playerData.over_second_probability.replace('%', '')), 42, 58),
                                normalizeData(parseFloat(playerData.over_third_probability.replace('%', '')), 62, 82),
                                normalizeData(parseFloat(averageScore), 22000, 27000),
                            ],
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1,
                            fill: true,
                            order: 1,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        r: {
                            angleLines: { display: true },
                            suggestedMin: 0,
                            suggestedMax: baselineMax,
                            ticks: { display: false },
                            pointLabels: { fontSize: 14 },
                            grid: { color: 'rgba(0, 0, 0, 0.1)' },
                        },
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label(context) {
                                    const value = context.raw;
                                    let actualValue = '';

                                    switch (context.dataIndex) {
                                        case 0:
                                            actualValue = (value / 10 + 20).toFixed(2) + '%';
                                            break;
                                        case 1:
                                            actualValue = value.toFixed(2) + '%';
                                            break;
                                        case 2:
                                            actualValue = value.toFixed(2) + '%';
                                            break;
                                        case 3:
                                            actualValue = Math.round(value * (30000 - 20000) / 100 + 20000);
                                            break;
                                    }

                                    if (value > 100) {
                                        return `${context.dataset.label} ${context.label}: ${actualValue} (基準値を超過)`;
                                    }

                                    return `${context.dataset.label} ${context.label}: ${actualValue}`;
                                },
                            },
                        },
                    },
                },
            });
        }
    }

    document.querySelectorAll('.scroll-btn').forEach((button) => {
        button.addEventListener('click', () => {
            const target = document.getElementById(button.dataset.target || '');
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
});





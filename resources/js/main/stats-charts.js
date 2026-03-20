/**
 * 成績画面の累積ポイントグラフとポイントバー背景。
 */
document.addEventListener('DOMContentLoaded', () => {
    const dataElement = document.getElementById('stats-chart-data');
    const chartCanvas = document.getElementById('userPointsChart');
    const termScrollContainer = document.querySelector('.stats-term-selector__scroll');
    const activeTermButton = termScrollContainer?.querySelector('.stats-term-button.is-active');

    if (termScrollContainer && activeTermButton) {
        const targetLeft = activeTermButton.offsetLeft - Math.max((termScrollContainer.clientWidth - activeTermButton.clientWidth) / 2, 0);
        termScrollContainer.scrollLeft = Math.max(targetLeft, 0);
    }

    if (dataElement && chartCanvas && typeof Chart !== 'undefined') {
        const chartData = JSON.parse(dataElement.textContent || '{}');
        const datasets = chartData.datasets || [];
        const dates = chartData.dates || [];

        if (dates.length > 0) {
            const originDate = new Date(dates[0]);
            originDate.setMonth(originDate.getMonth() - 1);
            const originDateString = originDate.toISOString().split('T')[0];

            datasets.forEach((dataset) => {
                dataset.data.unshift({ x: originDateString, y: 0 });
            });

            datasets.forEach((dataset) => {
                const lastDate = new Date(dataset.data[dataset.data.length - 1].x);
                const emptyDate = new Date(lastDate.setMonth(lastDate.getMonth() + 1));
                dataset.data.push({ x: emptyDate.toISOString().split('T')[0], y: null });
            });
        }

        new Chart(chartCanvas.getContext('2d'), {
            type: 'line',
            data: { datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: false,
                    },
                },
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'month',
                            displayFormats: {
                                month: 'YY/M',
                            },
                        },
                    },
                },
                elements: {
                    line: {
                        spanGaps: true,
                    },
                },
            },
        });
    }

    document.querySelectorAll('.player-point-bar[data-bar-value]').forEach((cell) => {
        const value = Number(cell.dataset.barValue || 0);
        const direction = cell.dataset.barDirection === 'left' ? 'to left' : 'to right';
        const positive = 'rgba(0, 153, 68, 0.05)';
        const negative = 'rgba(153, 0, 0, 0.05)';
        const color = direction === 'to left' ? negative : positive;
        const fadeEnd = value + 10;

        cell.style.background = `linear-gradient(${direction},
            rgba(0, 0, 0, 0) 0%,
            rgba(0, 0, 0, 0) 50%,
            ${color} 50%,
            ${color} ${value}%,
            rgba(0, 0, 0, 0) ${fadeEnd}%)`;
    });
});


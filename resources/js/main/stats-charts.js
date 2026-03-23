/**
 * 成績画面の累積ポイントグラフとポイントバー背景。
 */
document.addEventListener('DOMContentLoaded', () => {
    const dataElement = document.getElementById('stats-chart-data');
    const chartCanvas = document.getElementById('userPointsChart');
    const termScrollContainer = document.querySelector('.stats-term-selector__scroll');
    const activeTermButton = termScrollContainer?.querySelector('.stats-term-button.is-active');
    const hideLegend = chartCanvas?.dataset.hideLegend === '1';

    if (termScrollContainer && activeTermButton) {
        const targetLeft = activeTermButton.offsetLeft - Math.max((termScrollContainer.clientWidth - activeTermButton.clientWidth) / 2, 0);
        termScrollContainer.scrollLeft = Math.max(targetLeft, 0);
    }

    if (dataElement && chartCanvas && typeof Chart !== 'undefined') {
        const chartData = JSON.parse(dataElement.textContent || '{}');
        const datasets = chartData.datasets || [];
        const dates = chartData.dates || [];
        const labels = chartData.labels || [];
        const xAxisType = chartData.xAxisType || 'date';

        if (xAxisType === 'date' && dates.length > 0) {
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

        const chartConfig = xAxisType === 'game'
            ? {
                data: {
                    labels,
                    datasets,
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: '半荘数',
                        },
                    },
                },
            }
            : {
                data: { datasets },
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
            };

        new Chart(chartCanvas.getContext('2d'), {
            type: 'line',
            data: chartConfig.data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: false,
                    },
                    legend: {
                        display: !hideLegend,
                        labels: hideLegend ? { generateLabels: () => [] } : undefined,
                    },
                },
                scales: chartConfig.scales,
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


import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    const chartElement = document.getElementById('uploadChart');

    if (chartElement) {
        const tanggal = JSON.parse(chartElement.dataset.tanggal);
        const jumlah = JSON.parse(chartElement.dataset.jumlah);

        const ctx = chartElement.getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: tanggal,
                datasets: [{
                    label: 'Jumlah Upload',
                    data: jumlah,
                    borderColor: '#050C9C',
                    backgroundColor: 'rgba(5, 12, 156, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#050C9C',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                animation: {
                    duration: 800,
                    easing: 'easeOutQuart'
                },
                scales: {
                    x: {
                        ticks: { color: '#6B7280' },
                        grid: { display: false }
                    },
                    y: {
                        ticks: { color: '#6B7280' },
                        grid: { color: 'rgba(229, 231, 235, 0.4)' }
                    }
                },
                plugins: { legend: { display: false } }
            }
        });
    }
});

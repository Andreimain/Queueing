import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    const monthSelect = document.getElementById('monthSelect');
    const yearSelect = document.getElementById('yearSelect');
    const combinedMonth = document.getElementById('combinedMonth');
    const form = document.getElementById('statisticsForm');

    if (monthSelect && yearSelect && combinedMonth && form) {
        const updateMonthValue = () => {
            combinedMonth.value = `${yearSelect.value}-${monthSelect.value}`;
            form.submit();
        };

        monthSelect.addEventListener('change', updateMonthValue);
        yearSelect.addEventListener('change', updateMonthValue);
    }

    const ctx = document.getElementById('statsChart');
    if (!ctx || !window.statisticsData) return;

    const { role, labels, counts } = window.statisticsData;

    if (role === 'staff') {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Number of Visitors',
                    data: counts,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    } else {
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Visitors Count Per Office',
                    data: counts
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
});

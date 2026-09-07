import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('statisticsForm');
    const rangeSelect = document.getElementById('rangeSelect');
    const monthSelect = document.getElementById('monthSelect');
    const yearSelect = document.getElementById('yearSelect');
    const weekSelect = document.getElementById('weekSelect');
    const weekWrapper = document.getElementById('weekWrapper');
    const combinedMonth = document.getElementById('combinedMonth');

    const submitFilters = () => {
        combinedMonth.value = `${yearSelect.value}-${monthSelect.value}`;
        form.submit();
    };

    if (rangeSelect) {
        rangeSelect.addEventListener('change', () => {
            if (rangeSelect.value === 'weekly') {
                weekWrapper.classList.remove('hidden');
            } else {
                weekWrapper.classList.add('hidden');
            }

            submitFilters();
        });
    }

    if (monthSelect) {
        monthSelect.addEventListener('change', submitFilters);
    }

    if (yearSelect) {
        yearSelect.addEventListener('change', submitFilters);
    }

    if (weekSelect) {
        weekSelect.addEventListener('change', submitFilters);
    }

    const canvas = document.getElementById('statsChart');

    if (!canvas || !window.statisticsData) {
        return;
    }

    const {
        role,
        labels,
        counts,
        totalRegistrations,
        totalTickets,
        completed,
        skipped,
        transferred,
        students,
        visitors
    } = window.statisticsData;

    if (role === 'staff') {
        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: [
                    'Total Registrations',
                    'Total Tickets',
                    'Complete',
                    'Skipped',
                    'Transferred',
                    'Students',
                    'Visitors'
                ],
                datasets: [{
                    label: 'Statistics',
                    data: [
                        totalRegistrations,
                        totalTickets,
                        completed,
                        skipped,
                        transferred,
                        students,
                        visitors
                    ],
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        callbacks: {
                            label: context => ` ${context.raw}`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        title: {
                            display: true,
                            text: 'Number'
                        }
                    }
                }
            }
        });

        return;
    }

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Registrations',
                data: counts,
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    callbacks: {
                        label: context => ` ${context.raw} registration(s)`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    title: {
                        display: true,
                        text: 'Number of Registrations'
                    }
                },
                x: {
                    ticks: {
                        autoSkip: false
                    }
                }
            }
        }
    });
});

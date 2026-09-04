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
        const year = yearSelect.value;
        const month = monthSelect.value;

        combinedMonth.value = `${year}-${month}`;
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
        monthSelect.addEventListener('change', () => {
            submitFilters();
        });
    }

    if (yearSelect) {
        yearSelect.addEventListener('change', () => {
            submitFilters();
        });
    }

    if (weekSelect) {
        weekSelect.addEventListener('change', () => {
            submitFilters();
        });
    }

    const canvas = document.getElementById('statsChart');

    if (!canvas || !window.statisticsData) {
        return;
    }

    const {
        role,
        range,
        labels,
        counts,
        totalTickets,
        completed,
        skipped,
        transferred
    } = window.statisticsData;

    if (role === 'staff') {
        new Chart(canvas, {
            type: 'bar',

            data: {
                labels: [
                    'Total Tickets',
                    'Completed',
                    'Skipped',
                    'Transferred'
                ],

                datasets: [
                    {
                        label: 'Tickets',
                        data: [
                            totalTickets,
                            completed,
                            skipped,
                            transferred
                        ],
                        borderWidth: 1,
                        borderRadius: 6
                    }
                ]
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
                            label: function (context) {
                                return ` ${context.raw} ticket(s)`;
                            }
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
                            text: 'Number of Tickets'
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

            datasets: [
                {
                    label: 'Visitors per Office',
                    data: counts,
                    borderWidth: 1,
                    borderRadius: 6
                }
            ]
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
                        label: function (context) {
                            return ` ${context.raw} ticket(s)`;
                        }
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
                        text: 'Number of Visitors'
                    }
                }
            }
        }
    });
});

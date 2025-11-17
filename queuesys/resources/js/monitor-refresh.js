document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.officeId === 'undefined') {
        console.error('officeId is not defined for monitor refresh.');
        return;
    }

    function refreshMonitor() {
        fetch(`/monitor/${window.officeId}/data`)
            .then(res => res.json())
            .then(data => {
                // Update each cashier container
                document.querySelectorAll('[data-cashier-id]').forEach(container => {
                    const cashierId = container.getAttribute('data-cashier-id');
                    const cashier = data.cashiers.find(c => c.id == cashierId);
                    if (!cashier) return;

                    const servingDiv = container.querySelector('.serving');
                    if (cashier.serving) {
                        servingDiv.innerHTML = `
                            <div class="text-3xl sm:text-4xl font-bold text-emerald-600 break-words">
                                ${cashier.serving.ticket_number}
                            </div>
                            <div class="text-gray-700 mt-1 text-lg">
                                #${cashier.serving.queue_number}
                            </div>`;
                    } else {
                        servingDiv.innerHTML = `<div class="text-2xl sm:text-3xl text-gray-400">Idle</div>`;
                    }
                });

                // Update upcoming queues
                const upcomingList = document.getElementById('upcoming-queues');
                if (upcomingList) {
                    if (data.upcomingQueues.length > 0) {
                        upcomingList.innerHTML = data.upcomingQueues.map(q =>
                            `<li class="text-2xl sm:text-3xl break-words text-gray-800">${q.ticket_number}</li>`
                        ).join('');
                    } else {
                        upcomingList.innerHTML = `<li class="text-gray-500">No upcoming queues</li>`;
                    }
                }
            })
            .catch(err => console.error('Error refreshing monitor:', err));
    }

    setInterval(refreshMonitor, 3000);
});

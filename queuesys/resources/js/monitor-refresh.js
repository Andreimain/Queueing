import Echo from 'laravel-echo';

document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.officeId === 'undefined') {
        console.error('officeId not found');
        return;
    }

    window.Echo
        .channel(`queues.office.${window.officeId}`)
        .listen('.queue.updated', (e) => {
            const payload = e.payload;

            if (!payload || typeof payload !== 'object') {
                console.warn('Invalid payload received:', payload);
                return;
            }

            // Update serving per cashier
            document.querySelectorAll('[data-cashier-id]').forEach(container => {
                const cashierId = container.dataset.cashierId;
                const serving = payload.serving?.[cashierId] || null;
                const servingDiv = container.querySelector('.serving');

                if (!servingDiv) return;

                if (serving) {
                    servingDiv.innerHTML = `
                        <div class="text-3xl sm:text-4xl font-bold text-emerald-600">
                            ${serving.ticket}
                        </div>
                        <div class="text-gray-700 mt-1 text-lg">
                            #${serving.queue}
                        </div>
                    `;
                } else {
                    servingDiv.innerHTML =
                        `<div class="text-2xl sm:text-3xl text-gray-400">Idle</div>`;
                }
            });

            // Update upcoming queues
            const upcoming = document.getElementById('upcoming-queues');
            if (!upcoming) return;

            const upcomingList = Array.isArray(payload.upcoming) ? payload.upcoming : [];
            if (!upcomingList.length) {
                upcoming.innerHTML = `<li class="text-gray-500">No upcoming queues</li>`;
                return;
            }

            upcoming.innerHTML = upcomingList
                .map(ticket =>
                    `<li class="text-2xl sm:text-3xl text-gray-800">${ticket}</li>`
                )
                .join('');
        });
});

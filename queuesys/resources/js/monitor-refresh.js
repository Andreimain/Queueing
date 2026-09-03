import Echo from 'laravel-echo';

function formatTicketForSpeech(ticket) {
    return ticket
        .split('')
        .map(char => {
            if (char === '-') {
                return 'dash';
            }

            return char;
        })
        .join(' ');
}

document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.officeId === 'undefined') {
        console.error('officeId not found');
        return;
    }

    // Keep track of the last ticket announced for each cashier
    const lastAnnouncedTickets = {};

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
                    `;

                    // Announce only if this is a new ticket
                    if (lastAnnouncedTickets[cashierId] !== serving.ticket) {
                        lastAnnouncedTickets[cashierId] = serving.ticket;

                        const cashierName =
                            container.querySelector('h2')?.textContent.trim() || 'cashier';

                        const spokenTicket =
                            formatTicketForSpeech(serving.ticket);

                        const announcement = new SpeechSynthesisUtterance(
                            `Now serving ${spokenTicket}. Please proceed to ${cashierName}.`
                        );

                        announcement.rate = 0.9;
                        announcement.pitch = 1;

                        speechSynthesis.cancel();
                        speechSynthesis.speak(announcement);
                    }

                } else {
                    servingDiv.innerHTML =
                        `<div class="text-2xl sm:text-3xl text-gray-400">Idle</div>`;
                }
            });

            // Update upcoming queues
            const upcoming = document.getElementById('upcoming-queues');

            if (!upcoming) return;

            const upcomingList = Array.isArray(payload.upcoming)
                ? payload.upcoming
                : [];

            if (!upcomingList.length) {
                upcoming.innerHTML =
                    `<li class="text-gray-500">No upcoming queues</li>`;
                return;
            }

            upcoming.innerHTML = upcomingList
                .map(ticket =>
                    `<li class="text-2xl sm:text-3xl text-gray-800">${ticket}</li>`
                )
                .join('');
        })

        .listen('.queue.announce', (e) => {
            if (!e.ticket) {
                console.warn('No ticket received for announcement.');
                return;
            }

            const spokenTicket =
                formatTicketForSpeech(e.ticket);

            const announcement = new SpeechSynthesisUtterance(
                `Now serving ${spokenTicket}. Please proceed to ${e.cashier || 'the cashier'}.`
            );

            announcement.rate = 0.9;
            announcement.pitch = 1;

            speechSynthesis.cancel();
            speechSynthesis.speak(announcement);
        });
});

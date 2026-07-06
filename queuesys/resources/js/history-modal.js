document.addEventListener("DOMContentLoaded", () => {

    const modal = document.getElementById("infoModal");
    const closeBtn = document.getElementById("closeModal");

    const mName = document.getElementById("mName");
    const mContact = document.getElementById("mContact");
    const timelineEl = document.getElementById("timeline");

    const statusColors = {
        done: 'bg-emerald-500',
        skipped: 'bg-amber-500',
        transferred: 'bg-blue-500'
    };

    document.querySelectorAll(".infoBtn").forEach(btn => {

        btn.addEventListener("click", () => {

            const name = btn.dataset.name;
            const contact = btn.dataset.contact;
            const tickets = JSON.parse(btn.dataset.tickets);

            mName.textContent = name;
            mContact.textContent = contact;

            timelineEl.innerHTML = "";

            tickets.forEach((ticket, index) => {

                const created = new Date(ticket.created_at).toLocaleString();
                const office = ticket.office.name;

                const statusRaw = ticket.status ?? '';
                const status = statusRaw.toLowerCase().trim();

                const ticketNumber = ticket.ticket_number;
                const isLast = index === tickets.length - 1;

                const dotColor =
                    statusColors[status] ?? 'bg-gray-400';

                timelineEl.innerHTML += `
                    <div class="flex gap-6 relative">

                        <!-- DOT COLUMN -->
                        <div class="flex flex-col items-center">

                            <!-- DOT -->
                            <div class="w-4 h-4 rounded-full border-2 border-white shadow ${dotColor}">
                            </div>

                            <!-- CONNECTOR -->
                            ${!isLast ? `
                                <div class="w-0.5 bg-gray-300 h-20 mt-3"></div>
                            ` : ''}

                        </div>

                        <!-- CARD -->
                        <div class="flex-1">

                            <div class="bg-white border rounded-xl shadow-sm p-4 hover:shadow-md transition">

                                <div class="font-semibold text-sm text-gray-800 capitalize">
                                    ${ticketNumber} — ${status} — ${office}
                                </div>

                                <div class="text-gray-500 text-xs mt-1">
                                    ${created}
                                </div>

                            </div>

                        </div>

                    </div>
                `;
            });
3
            modal.classList.remove("hidden");
            modal.classList.add("flex");

        });

    });

    // Close modal
    closeBtn.addEventListener("click", () => {
        modal.classList.add("hidden");
        modal.classList.remove("flex");
    });

    // Close on backdrop
    modal.addEventListener("click", e => {
        if (e.target === modal) {
            modal.classList.add("hidden");
            modal.classList.remove("flex");
        }
    });

});

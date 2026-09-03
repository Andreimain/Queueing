document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("infoModal");
    const closeBtn = document.getElementById("closeModal");
    const mName = document.getElementById("mName");
    const mContact = document.getElementById("mContact");
    const timelineEl = document.getElementById("timeline");

    document.querySelectorAll(".infoBtn").forEach(btn => {
        btn.addEventListener("click", () => {

            const tickets = JSON.parse(
                btn.dataset.tickets || "[]"
            );

            const transfers = JSON.parse(
                btn.dataset.transfers || "[]"
            );

            const registrationOfficeId = Number(
                btn.dataset.registrationOfficeId
            );

            const registrationOffice =
                btn.dataset.registrationOffice || "—";

            const isStaff = window.isStaff === true;
            const staffOfficeId = Number(window.staffOfficeId);

            mName.textContent =
                btn.dataset.name || "—";

            mContact.textContent =
                btn.dataset.contact || "—";

            timelineEl.innerHTML = "";

            const events = [];

            /*
             * REGISTERED
             */
            if (
                !isStaff ||
                registrationOfficeId === staffOfficeId
            ) {
                const firstTicket = tickets[0];

                if (firstTicket) {
                    events.push({
                        type: "registered",
                        date: firstTicket.created_at,
                        ticket: firstTicket.ticket_number,
                        queue: firstTicket.queue_number,
                        office: registrationOffice,
                        officeId: registrationOfficeId
                    });
                }
            }

            /*
             * TRANSFERS
             */
            transfers.forEach(transfer => {

                const fromOfficeId =
                    Number(transfer.from_office_id);

                const toOfficeId =
                    Number(transfer.to_office_id);

                const belongsToStaff =
                    fromOfficeId === staffOfficeId ||
                    toOfficeId === staffOfficeId;

                if (isStaff && !belongsToStaff) {
                    return;
                }

                events.push({
                    type: "transferred",

                    date:
                        transfer.transferred_at,

                    fromOffice:
                        transfer.from_office?.name ?? "—",

                    toOffice:
                        transfer.to_office?.name ?? "—",

                    fromQueue:
                        transfer.from_queue_number,

                    toQueue:
                        transfer.to_queue_number,

                    transferredBy:
                        transfer.transferred_by?.name ??
                        "Unknown",

                    fromOfficeId,
                    toOfficeId
                });
            });

            /*
             * FINAL STATUS
             */
            if (tickets.length) {

                const final =
                    tickets[tickets.length - 1];

                const finalOfficeId =
                    Number(final.office_id);

                if (
                    !isStaff ||
                    finalOfficeId === staffOfficeId
                ) {
                    events.push({
                        type: final.status,

                        date:
                            final.updated_at,

                        ticket:
                            final.ticket_number,

                        queue:
                            final.queue_number,

                        office:
                            final.office?.name ??
                            registrationOffice,

                        cashier:
                            final.cashier?.name ??
                            null,

                        officeId:
                            finalOfficeId
                    });
                }
            }

            /*
             * SORT TIMELINE
             */
            events.sort((a, b) =>
                new Date(a.date) -
                new Date(b.date)
            );

            /*
             * RENDER TIMELINE
             */
            events.forEach((event, index) => {

                const isLast =
                    index === events.length - 1;

                const date =
                    new Date(event.date).toLocaleString(
                        "en-PH",
                        {
                            dateStyle: "medium",
                            timeStyle: "short"
                        }
                    );

                /*
                 * Default
                 */
                let dotColor = "#9ca3af";
                let title = "";
                let content = "";

                /*
                 * REGISTERED
                 */
                if (event.type === "registered") {

                    dotColor = "#9ca3af";
                    title = "Registered";

                    content = `
                        <div class="text-sm text-gray-700">
                            <strong>${event.ticket}</strong>
                            — Queue #${event.queue}
                        </div>

                        <div class="text-xs text-gray-500 mt-1">
                            ${event.office}
                        </div>
                    `;
                }

                /*
                 * TRANSFERRED
                 */
                if (event.type === "transferred") {

                    dotColor = "#3b82f6";
                    title = "Transferred";

                    content = `
                        <div class="text-sm text-gray-700">
                            ${event.fromOffice}

                            <span class="mx-2 text-gray-400">
                                →
                            </span>

                            ${event.toOffice}
                        </div>

                        <div class="text-xs text-gray-500 mt-1">
                            Queue #${event.fromQueue}

                            <span class="mx-1">
                                →
                            </span>

                            Queue #${event.toQueue}
                        </div>

                        <div class="text-xs text-gray-500 mt-1">
                            Transferred by:
                            <strong>
                                ${event.transferredBy}
                            </strong>
                        </div>
                    `;
                }

                /*
                 * DONE
                 */
                if (event.type === "done") {

                    dotColor = "#10b981";
                    title = "Completed";

                    content = `
                        <div class="text-sm text-gray-700">
                            <strong>${event.ticket}</strong>
                            — Queue #${event.queue}
                        </div>

                        <div class="text-xs text-gray-500 mt-1">
                            ${event.office}
                        </div>

                        <div class="text-xs text-gray-500 mt-1">
                            Completed by:
                            <strong>
                                ${event.cashier ?? "—"}
                            </strong>
                        </div>
                    `;
                }

                /*
                 * SKIPPED
                 */
                if (event.type === "skipped") {

                    dotColor = "#f59e0b";
                    title = "Skipped";

                    content = `
                        <div class="text-sm text-gray-700">
                            <strong>${event.ticket}</strong>
                            — Queue #${event.queue}
                        </div>

                        <div class="text-xs text-gray-500 mt-1">
                            ${event.office}
                        </div>

                        <div class="text-xs text-gray-500 mt-1">
                            Handled by:
                            <strong>
                                ${event.cashier ?? "—"}
                            </strong>
                        </div>
                    `;
                }

                /*
                 * TIMELINE HTML
                 */
                timelineEl.innerHTML += `
                    <div class="flex gap-6 relative">

                        <div class="flex flex-col items-center">

                            <div
                                class="w-4 h-4 rounded-full border-2 border-white shadow"
                                style="
                                    background-color: ${dotColor};
                                    width: 16px;
                                    height: 16px;
                                    flex-shrink: 0;
                                "
                            ></div>

                            ${!isLast ? `
                                <div
                                    class="w-0.5 bg-gray-300 flex-1 mt-3"
                                ></div>
                            ` : ""}

                        </div>

                        <div class="flex-1 pb-6">

                            <div
                                class="bg-white border rounded-xl shadow-sm p-4"
                            >

                                <div
                                    class="font-semibold text-sm text-gray-800"
                                >
                                    ${title}
                                </div>

                                <div
                                    class="text-xs text-gray-500 mt-1"
                                >
                                    ${date}
                                </div>

                                <div class="mt-3">
                                    ${content}
                                </div>

                            </div>

                        </div>

                    </div>
                `;
            });

            modal.classList.remove("hidden");
            modal.classList.add("flex");
        });
    });

    /*
     * CLOSE MODAL
     */
    closeBtn.addEventListener("click", () => {
        modal.classList.add("hidden");
        modal.classList.remove("flex");
    });

    /*
     * CLOSE WHEN CLICKING BACKDROP
     */
    modal.addEventListener("click", e => {
        if (e.target === modal) {
            modal.classList.add("hidden");
            modal.classList.remove("flex");
        }
    });
});

import Echo from "laravel-echo";

function setText(id, value, fallback = "—") {
    const el = document.getElementById(id);
    if (el) el.textContent = value ?? fallback;
}

function setStatByKey(key, value) {
    const el = document.querySelector(`[data-stat="${key}"]`);
    if (el) el.textContent = value ?? 0;
}

// Admin live updates
if (window.isAdmin) {
    window.Echo.channel("queues.admin").listen(".queue.stats.updated", (e) => {
        const data = e.admin;
        if (!data) return;

        // Top stat cards
        setStatByKey("visitorsToday", data.visitorsToday);
        setStatByKey("activeQueues", data.activeQueues);

        // Per-office table updates
        data.offices.forEach((office) => {
            const row = document.querySelector(`[data-office-id="${office.id}"]`);
            if (!row) return;

            const badge = row.querySelector(".waiting-badge");
            const status = row.querySelector(".office-status");

            if (badge) {
                badge.textContent = office.waiting;
                badge.className =
                    "waiting-badge inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-sm font-medium " +
                    (office.waiting > 0
                        ? "bg-emerald-100 text-emerald-800"
                        : "bg-gray-100 text-gray-600");
            }

            if (status) {
                if (office.waiting > 5) {
                    status.textContent = "Busy";
                    status.className =
                        "office-status text-xs font-medium text-amber-600";
                } else if (office.waiting > 0) {
                    status.textContent = "Active";
                    status.className =
                        "office-status text-xs font-medium text-emerald-600";
                } else {
                    status.textContent = "Idle";
                    status.className = "office-status text-xs text-gray-400";
                }
            }
        });
    });
}

// Staff live updates
if (window.isStaff && window.officeId) {
    window.Echo.channel(`queues.office.${window.officeId}`).listen(
        ".queue.stats.updated",
        (e) => {
            const data = e.office;
            if (!data) return;

            setText("currentServing", data.currentServing);
            setText("waitingCount", data.waiting, 0);
            setText("skippedCount", data.skipped, 0);
        }
    );
}

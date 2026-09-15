document.addEventListener("DOMContentLoaded", () => {
    const REFRESH_INTERVAL = 3000;

    function refreshVisitors() {
        fetch("/guard/visitors/data", {
            method: "GET",
            headers: {
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
            cache: "no-store",
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                return response.json();
            })
            .then((visitors) => {
                updateGuardMonitoring(visitors);
            })
            .catch((error) => {
                console.error("Failed to refresh Guard Monitoring:", error);
            });
    }

    function updateGuardMonitoring(visitors) {
        const container = document.getElementById("visitorCards");
        const count = document.getElementById("visitorCount");

        if (!container) {
            return;
        }

        if (count) {
            count.textContent = `${visitors.length} visitor${
                visitors.length !== 1 ? "s" : ""
            }`;
        }

        if (!visitors.length) {
            container.innerHTML = `
                <div
                    class="col-span-full py-12 text-center text-gray-500"
                >
                    No visitors are currently waiting or being served.
                </div>
            `;

            return;
        }

        container.innerHTML = "";

        visitors.forEach((visitor) => {
            const card = document.createElement("div");

            card.className =
                "visitor-card bg-white border border-gray-200 rounded-2xl shadow-sm p-5 flex flex-col items-center text-center";

            const photo = visitor.photo
                ? `
                    <img
                        src="${escapeHtml(visitor.photo)}"
                        alt="Visitor photo"
                        class="w-full h-full object-cover"
                    >
                `
                : `
                    <span class="text-sm text-gray-400">
                        No Photo
                    </span>
                `;

            const status =
                visitor.status === "serving"
                    ? `
                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                        ● Serving
                    </span>
                `
                    : `
                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                        ● Waiting
                    </span>
                `;

            card.innerHTML = `

                <!-- Photo Box -->
                <div
                    class="w-40 h-48 rounded-lg border-2 border-gray-300 bg-gray-100 overflow-hidden flex items-center justify-center"
                >
                    ${photo}
                </div>

                <!-- Ticket Number -->
                <div class="mt-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Ticket Number
                    </p>

                    <p class="mt-1 text-2xl font-bold text-gray-900">
                        ${escapeHtml(visitor.ticket_number ?? "—")}
                    </p>
                </div>

                <!-- Visitor ID -->
                <div class="mt-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Visitor ID
                    </p>

                    <p class="mt-1 text-base font-semibold text-gray-800">
                        ${escapeHtml(visitor.id_number ?? "—")}
                    </p>
                </div>

                <!-- Office -->
                <div class="mt-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Office
                    </p>

                    <p class="mt-1 text-base font-semibold text-gray-800">
                        ${escapeHtml(visitor.office ?? "N/A")}
                    </p>
                </div>

                <!-- Status -->
                <div class="mt-5">
                    ${status}
                </div>
            `;

            container.appendChild(card);
        });
    }

    function escapeHtml(value) {
        const div = document.createElement("div");

        div.textContent = String(value);

        return div.innerHTML;
    }

    // Load immediately
    refreshVisitors();

    // Background refresh every 3 seconds
    setInterval(refreshVisitors, REFRESH_INTERVAL);
});

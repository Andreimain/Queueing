document.addEventListener("DOMContentLoaded", () => {
    const idInput = document.getElementById("visitorIdInput");
    const rfidScannerInput = document.getElementById("rfidScannerInput");
    const findButton = document.getElementById("findVisitorButton");
    const visitorResult = document.getElementById("visitorResult");
    const scannerStatus = document.getElementById("scannerStatus");

    /*
     * Stores the last accepted scan time for each RFID ID.
     *
     * Each ID has its own 10-second cooldown.
     */
    const scanCooldowns = new Map();

    const SCAN_COOLDOWN = 10000;

    function findVisitor() {
        const idNumber = idInput?.value.trim();

        if (!idNumber) {
            showResult("Please enter a visitor ID.", "error");
            return;
        }

        findButton.disabled = true;
        findButton.textContent = "Searching...";

        fetch("/guard/visitors/find", {
            method: "POST",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": getCsrfToken(),
            },
            body: JSON.stringify({
                id_number: idNumber,
            }),
        })
            .then(async (response) => {
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(
                        data.message || "Visitor could not be found."
                    );
                }

                return data;
            })
            .then((visitor) => {
                showVisitorResult(visitor);
            })
            .catch((error) => {
                showResult(error.message, "error");
            })
            .finally(() => {
                findButton.disabled = false;
                findButton.textContent = "Find Visitor";
            });
    }

    function showVisitorResult(visitor) {
        if (!visitorResult) {
            return;
        }

        const photo = visitor.photo
            ? `
                <img
                    src="${escapeHtml(visitor.photo)}"
                    alt="Visitor photo"
                    style="width: 96px !important; height: 112px !important; max-width: 96px !important; max-height: 112px !important; min-width: 96px !important; min-height: 112px !important; object-fit: cover; display: block;"
                >
            `
            : `
                <div
                    style="width: 96px; height: 112px; min-width: 96px; min-height: 112px;"
                    class="rounded-lg border border-gray-300 bg-gray-100 flex items-center justify-center"
                >
                    <span class="text-xs text-gray-400">
                        No Photo
                    </span>
                </div>
            `;

        const ticket = visitor.ticket_number
            ? `
                <p class="text-sm text-gray-500 mt-1">
                    Ticket: ${escapeHtml(visitor.ticket_number)}
                </p>
            `
            : "";

        let queueStatus = "";

        if (visitor.ticket_number) {
            if (visitor.status === "serving") {
                queueStatus = `
                    <p class="text-sm text-gray-500 mt-1">
                        Queue Status: Serving
                    </p>
                `;
            } else if (visitor.status === "waiting") {
                queueStatus = `
                    <p class="text-sm text-gray-500 mt-1">
                        Queue Status: Waiting
                    </p>
                `;
            } else if (visitor.status === "skipped") {
                queueStatus = `
                    <p class="text-sm text-gray-500 mt-1">
                        Queue Status: Skipped
                    </p>
                `;
            } else if (visitor.status === "transferred") {
                queueStatus = `
                    <p class="text-sm text-gray-500 mt-1">
                        Queue Status: Transferred
                    </p>
                `;
            }
        }

        visitorResult.innerHTML = `
            <div class="border border-gray-200 rounded-xl p-5 bg-white">
                <div class="flex flex-col sm:flex-row items-center gap-6">

                    <div
                        style="width: 96px; min-width: 96px; height: 112px; min-height: 112px;"
                        class="rounded-lg border border-gray-300 bg-gray-100 overflow-hidden flex items-center justify-center"
                    >
                        ${photo}
                    </div>

                    <div class="flex-1 text-center sm:text-left">
                        <p class="text-lg font-semibold text-gray-900">
                            ${escapeHtml(visitor.name)}
                        </p>

                        <p class="text-sm text-gray-500 mt-1">
                            Visitor ID: ${escapeHtml(visitor.id_number)}
                        </p>

                        <p class="text-sm text-gray-500 mt-1">
                            Office: ${escapeHtml(visitor.office)}
                        </p>

                        ${ticket}

                        ${queueStatus}

                        <span
                            class="inline-flex items-center mt-3 px-4 py-1.5 rounded-full text-sm font-semibold bg-green-100 text-green-800"
                        >
                            ● IN
                        </span>
                    </div>

                    <button
                        type="button"
                        id="checkoutVisitorButton"
                        class="shrink-0 px-5 py-2.5 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition"
                    >
                        Record OUT
                    </button>

                </div>
            </div>
        `;

        const checkoutButton = document.getElementById("checkoutVisitorButton");

        if (checkoutButton) {
            checkoutButton.addEventListener("click", () => {
                checkoutVisitor(visitor.id_number);
            });
        }
    }

    function checkoutVisitor(idNumber, automatic = false) {
        if (!automatic) {
            const confirmed = window.confirm(
                "Are you sure you want to record this visitor as OUT?"
            );

            if (!confirmed) {
                return;
            }
        }

        const checkoutButton = document.getElementById("checkoutVisitorButton");

        if (checkoutButton) {
            checkoutButton.disabled = true;
            checkoutButton.textContent = "Recording...";
        }

        if (automatic && scannerStatus) {
            scannerStatus.textContent = "Recording visitor as OUT...";
        }

        fetch("/guard/visitors/checkout", {
            method: "POST",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": getCsrfToken(),
            },
            body: JSON.stringify({
                id_number: idNumber,
            }),
        })
            .then(async (response) => {
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(
                        data.message || "Failed to record visitor as OUT."
                    );
                }

                return data;
            })
            .then((data) => {
                showResult(data.message, "success");

                if (idInput) {
                    idInput.value = "";
                }

                if (automatic && scannerStatus) {
                    scannerStatus.textContent =
                        "RFID Scanner Ready";
                }

                focusRfidScanner();
            })
            .catch((error) => {
                showResult(error.message, "error");

                if (checkoutButton) {
                    checkoutButton.disabled = false;
                    checkoutButton.textContent = "Record OUT";
                }

                if (automatic && scannerStatus) {
                    scannerStatus.textContent =
                        "RFID Scanner Ready";
                }

                focusRfidScanner();
            });
    }

    function processRfidScan() {
        const idNumber = rfidScannerInput?.value.trim();

        if (!idNumber) {
            return;
        }

        const now = Date.now();
        const lastScanTime = scanCooldowns.get(idNumber);

        /*
         * Ignore the same ID if it was already scanned
         * within the last 10 seconds.
         */
        if (
            lastScanTime &&
            now - lastScanTime < SCAN_COOLDOWN
        ) {
            if (scannerStatus) {
                scannerStatus.textContent =
                    "Scan ignored. This ID was already scanned recently.";
            }

            if (rfidScannerInput) {
                rfidScannerInput.value = "";
            }

            focusRfidScanner();

            return;
        }

        /*
         * Accept this RFID ID and start its own
         * 10-second cooldown.
         */
        scanCooldowns.set(idNumber, now);

        if (rfidScannerInput) {
            rfidScannerInput.value = "";
        }

        if (scannerStatus) {
            scannerStatus.textContent =
                "RFID scan received. Processing...";
        }

        checkoutVisitor(idNumber, true);
    }

    function focusRfidScanner() {
        if (!rfidScannerInput) {
            return;
        }

        rfidScannerInput.focus();
    }

    function showResult(message, type) {
        if (!visitorResult) {
            return;
        }

        const classes =
            type === "success"
                ? "bg-green-100 text-green-800 border-green-200"
                : "bg-red-100 text-red-800 border-red-200";

        visitorResult.innerHTML = `
            <div class="border rounded-xl p-4 ${classes}">
                ${escapeHtml(message)}
            </div>
        `;
    }

    function getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');

        return token ? token.getAttribute("content") : "";
    }

    function escapeHtml(value) {
        const div = document.createElement("div");

        div.textContent = String(value);

        return div.innerHTML;
    }

    /*
     * Manual visitor search.
     */
    if (findButton) {
        findButton.addEventListener("click", findVisitor);
    }

    if (idInput) {
        idInput.addEventListener("keydown", (event) => {
            if (event.key === "Enter") {
                event.preventDefault();
                findVisitor();
            }
        });
    }

    /*
     * RFID scanner.
     *
     * USB HID RFID readers normally send the card ID
     * followed by Enter.
     */
    if (rfidScannerInput) {
        rfidScannerInput.addEventListener("keydown", (event) => {
            if (event.key === "Enter") {
                event.preventDefault();

                processRfidScan();
            }
        });

        focusRfidScanner();
    }

    /*
     * Keep the RFID scanner input focused when the guard
     * clicks elsewhere on the page.
     */
    document.addEventListener("click", (event) => {
        /*
         * Do not steal focus while the guard is manually
         * typing in the Visitor ID field.
         */
        if (idInput && event.target === idInput) {
            return;
        }

        if (findButton && event.target === findButton) {
            return;
        }

        focusRfidScanner();
    });
});

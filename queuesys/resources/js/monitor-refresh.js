import Echo from "laravel-echo";

function formatTicketForSpeech(ticket) {
    return ticket
        .split("")
        .map((char) => {
            if (char === "-") {
                return "dash";
            }

            return char;
        })
        .join(" ");
}

document.addEventListener("DOMContentLoaded", () => {
    if (typeof window.officeId === "undefined") {
        console.error("officeId not found");
        return;
    }

    // Keep track of the last ticket announced for each cashier
    const lastAnnouncedTickets = {};

    const speechQueue = [];
    let isSpeaking = false;

    function speakNext() {
        if (!speechQueue.length) {
            isSpeaking = false;
            return;
        }

        isSpeaking = true;
        const text = speechQueue.shift();
        const announcement = new SpeechSynthesisUtterance(text);
        announcement.rate = 0.9;
        announcement.pitch = 1;
        announcement.onend = () => {
            isSpeaking = false;
            speakNext();
        };

        announcement.onerror = (event) => {
            console.error("Speech synthesis error:", event);
            isSpeaking = false;
            speakNext();
        };

        speechSynthesis.speak(announcement);
    }

    function queueAnnouncement(ticket, cashierName, type = 'now-serving') {
        const spokenTicket =
            formatTicketForSpeech(ticket);
        let message;

        if (type === 'call-again') {
            message =
                `Calling for ${spokenTicket}. Please proceed to ${cashierName}.`;
        } else {
            message =
                `Now serving ${spokenTicket}. Please proceed to ${cashierName}.`;
        }

        speechQueue.push(message);

        if (!isSpeaking) {
            speakNext();
        }
    }

    window.Echo.channel(`queues.office.${window.officeId}`)

        .listen(".queue.updated", (e) => {
            const payload = e.payload;

            if (!payload || typeof payload !== "object") {
                console.warn("Invalid payload received:", payload);
                return;
            }

            document
                .querySelectorAll("[data-cashier-id]")
                .forEach((container) => {
                    const cashierId = container.dataset.cashierId;
                    const serving = payload.serving?.[cashierId] || null;
                    const servingDiv = container.querySelector(".serving");

                    if (!servingDiv) {
                        return;
                    }

                    if (serving) {
                        servingDiv.innerHTML = `
                            <div class="text-3xl sm:text-4xl font-bold text-emerald-600">
                                ${serving.ticket}
                            </div>
                        `;

                        if (
                            lastAnnouncedTickets[cashierId] !== serving.ticket
                        ) {
                            lastAnnouncedTickets[cashierId] = serving.ticket;
                            const cashierName =
                                container
                                    .querySelector("h2")
                                    ?.textContent.trim() || "cashier";
                            queueAnnouncement(serving.ticket, cashierName);
                        }
                    } else {
                        servingDiv.innerHTML = `<div class="text-2xl sm:text-3xl text-gray-400">
                                Idle
                            </div>`;
                    }
                });

            const upcoming = document.getElementById("upcoming-queues");

            if (!upcoming) {
                return;
            }

            const upcomingList = Array.isArray(payload.upcoming)
                ? payload.upcoming
                : [];

            if (!upcomingList.length) {
                upcoming.innerHTML = `<li class="text-gray-500">
                        No upcoming queues
                    </li>`;

                return;
            }

            upcoming.innerHTML = upcomingList
                .map(
                    (ticket) =>
                        `<li class="text-2xl sm:text-3xl text-gray-800">
                            ${ticket}
                        </li>`
                )
                .join("");
        })

        .listen('.queue.announce', (e) => {

            if (!e.ticket) {
                console.warn(
                    'No ticket received for announcement.'
                );

                return;
            }

            queueAnnouncement(
                e.ticket,
                e.cashier || 'the cashier',
                'call-again'
            );
        });
});

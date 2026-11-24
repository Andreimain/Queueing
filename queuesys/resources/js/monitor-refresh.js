function formatTicketForSpeech(ticket) {
    return String(ticket)
        .split("")
        .map((char) => {
            return char === "-" ? "dash" : char;
        })
        .join(" ");
}

document.addEventListener("DOMContentLoaded", () => {
    if (typeof window.officeId === "undefined") {
        console.error("officeId not found");
        return;
    }

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

    function queueAnnouncement(ticket, cashierName, type = "now-serving") {
        const spokenTicket = formatTicketForSpeech(ticket);

        const message =
            type === "call-again"
                ? `Calling for ${spokenTicket}. Please proceed to ${cashierName}.`
                : `Now serving ${spokenTicket}. Please proceed to ${cashierName}.`;

        speechQueue.push(message);

        if (!isSpeaking) {
            speakNext();
        }
    }

    function updateServingDisplay(container, serving) {
        const servingDiv = container.querySelector(".serving");

        if (!servingDiv) {
            return;
        }

        const display = document.createElement("div");

        if (serving) {
            display.className =
                "text-4xl sm:text-5xl lg:text-6xl font-bold text-emerald-600 break-words";

            display.textContent = serving.ticket;
        } else {
            display.className = "text-2xl sm:text-3xl text-gray-400";
            display.textContent = "Idle";
        }

        servingDiv.replaceChildren(display);
    }

    function updateUpcomingQueues(upcomingList) {
        const upcoming = document.getElementById("upcoming-queues");

        if (!upcoming) {
            return;
        }

        if (!upcomingList.length) {
            const emptyMessage = document.createElement("li");

            emptyMessage.className =
                "col-span-full text-gray-500 text-center py-8";

            emptyMessage.textContent = "No upcoming queues";

            upcoming.replaceChildren(emptyMessage);
            return;
        }

        const fragment = document.createDocumentFragment();

        upcomingList.forEach((ticket) => {
            const item = document.createElement("li");

            item.className =
                "rounded-xl bg-emerald-50 border border-emerald-100 px-5 py-5 text-2xl sm:text-3xl font-bold text-emerald-800 text-center break-words";

            item.textContent = ticket;
            fragment.appendChild(item);
        });

        upcoming.replaceChildren(fragment);
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

                    updateServingDisplay(container, serving);

                    if (serving) {
                        if (
                            lastAnnouncedTickets[cashierId] !== serving.ticket
                        ) {
                            lastAnnouncedTickets[cashierId] = serving.ticket;

                            const cashierName =
                                container
                                    .querySelector("h3")
                                    ?.textContent.trim() || "cashier";

                            queueAnnouncement(
                                serving.ticket,
                                cashierName
                            );
                        }
                    } else {
                        lastAnnouncedTickets[cashierId] = null;
                    }
                });

            const upcomingList = Array.isArray(payload.upcoming)
                ? payload.upcoming
                : [];

            updateUpcomingQueues(upcomingList);
        })
        .listen(".queue.announce", (e) => {
            if (!e.ticket) {
                console.warn("No ticket received for announcement.");
                return;
            }

            queueAnnouncement(
                e.ticket,
                e.cashier || "the cashier",
                "call-again"
            );
        });
});

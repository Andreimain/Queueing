
document.addEventListener('DOMContentLoaded', () => {
    let pauseRefresh = false;
    let isRefreshing = false;

    const updateSkipButton = () => {
        const checked = document.querySelectorAll(
            'input[name="selected_visitors[]"]:checked'
        ).length;

        const btn = document.getElementById('skipSelectedBtn');

        if (btn) {
            btn.disabled = checked === 0;
        }

        pauseRefresh = checked > 0;
    };

    // Event delegation: works even after queue-area is replaced.
    document.addEventListener('change', (event) => {
        if (event.target.matches('input[name="selected_visitors[]"]')) {
            updateSkipButton();
        }
    });

    const refreshQueue = async () => {
        const queueArea = document.querySelector('#queue-area');

        if (!queueArea || isRefreshing) return;

        // Pause if a visitor is selected for multi-skip.
        if (pauseRefresh) return;

        // Pause if the current staff member is serving a visitor.
        const servingVisitor = queueArea.querySelector(
            '[data-serving="true"]'
        );

        if (servingVisitor) return;

        isRefreshing = true;

        try {
            const response = await fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error: ${response.status}`);
            }

            const html = await response.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const newQueueArea = doc.querySelector('#queue-area');

            if (newQueueArea) {
                // Check again before replacing the content.
                if (pauseRefresh) return;

                const currentServing = queueArea.querySelector(
                    '[data-serving="true"]'
                );

                if (currentServing) return;

                queueArea.innerHTML = newQueueArea.innerHTML;

                updateSkipButton();
            }
        } catch (error) {
            console.error('Error refreshing queue:', error);
        } finally {
            isRefreshing = false;
        }
    };

    updateSkipButton();

    setInterval(refreshQueue, 3000);
});

document.addEventListener('DOMContentLoaded', () => {
    let pauseRefresh = false;

    // Detect checkbox changes
    document.addEventListener('change', () => {
        const checked = document.querySelectorAll('input[name="selected_visitors[]"]:checked').length;
        const btn = document.getElementById('skipSelectedBtn');
        if (btn) btn.disabled = checked === 0;

        pauseRefresh = checked > 0;
    });

    // Auto-refresh queue area
    setInterval(() => {
        if (pauseRefresh) return;

        fetch(window.location.href)
            .then(res => res.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newContent = doc.querySelector('#queue-area').innerHTML;
                const queueArea = document.querySelector('#queue-area');
                if (queueArea) queueArea.innerHTML = newContent;
            })
            .catch(err => console.error('Error refreshing queue:', err));
    }, 3000);
});

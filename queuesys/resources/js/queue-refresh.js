document.addEventListener('DOMContentLoaded', () => {
    let pauseRefresh = false;

    // Multi-skip checkboxes
    const checkboxes = document.querySelectorAll('input[name="selected_visitors[]"]');
    checkboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            const checked = document.querySelectorAll('input[name="selected_visitors[]"]:checked').length;
            const btn = document.getElementById('skipSelectedBtn');
            if (btn) btn.disabled = checked === 0;

            pauseRefresh = checked > 0;
        });
    });

    const refreshQueue = () => {
        // Pause if staff is serving a visitor
        const servingVisitor = document.querySelectorAll('[data-serving="true"]').length > 0;

        if (pauseRefresh || servingVisitor) return; // pause refresh
        fetch(window.location.href)
            .then(res => res.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newContent = doc.querySelector('#queue-area').innerHTML;
                const queueArea = document.querySelector('#queue-area');
                if (queueArea) queueArea.innerHTML = newContent;
            })
            .catch(err => console.error('Error refreshing queue:', err));
    };

    setInterval(refreshQueue, 3000);
});

document.addEventListener('DOMContentLoaded', () => {
    const currentTimeElem = document.getElementById("current-time");

    function updateTime() {
        if (!currentTimeElem) return;

        const options = {
            timeZone: "Asia/Manila",
            year: "numeric",
            month: "long",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit"
        };
        currentTimeElem.innerText = new Date().toLocaleString("en-US", options);
    }

    setInterval(updateTime, 1000);
    updateTime();

    const selectAllCheckbox = document.getElementById('select-all');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', (e) => {
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = e.target.checked);
        });
    }

    const restoreForm = document.getElementById("restoreForm");
    if (restoreForm) {
        restoreForm.addEventListener("submit", (e) => {
            const selected = Array.from(document.querySelectorAll(".row-checkbox:checked"))
                .map(cb => cb.value);

            if (selected.length === 0) {
                e.preventDefault();
                alert("Please select at least one visitor to restore.");
                return false;
            }

            const selectedIdsElem = document.getElementById("selectedIds");
            if (selectedIdsElem) {
                selectedIdsElem.value = selected.join(",");
            }
        });
    }

    const searchInput = document.getElementById("searchInput");
    const searchForm = document.getElementById("searchForm");
    let typingTimer;

    if (searchInput && searchForm) {
        searchInput.addEventListener("keyup", () => {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => searchForm.submit(), 500);
        });
    }
});

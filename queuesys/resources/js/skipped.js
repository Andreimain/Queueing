document.addEventListener("DOMContentLoaded", () => {
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
            second: "2-digit",
        };

        currentTimeElem.innerText = new Date().toLocaleString("en-US", options);
    }

    setInterval(updateTime, 1000);
    updateTime();

    const selectAllCheckbox = document.getElementById("select-all");
    const rowCheckboxes = document.querySelectorAll(".row-checkbox");

    const restoreButton = document.getElementById("restoreButton");
    const swapButton = document.getElementById("swapButton");

    function getSelected() {
        return Array.from(document.querySelectorAll(".row-checkbox:checked"));
    }

    function updateButtons() {
        const selected = getSelected();

        if (restoreButton) {
            restoreButton.disabled = selected.length === 0;
        }

        if (swapButton) {
            swapButton.disabled = selected.length !== 1;
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener("change", (e) => {
            rowCheckboxes.forEach((checkbox) => {
                checkbox.checked = e.target.checked;
            });

            updateButtons();
        });
    }

    rowCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener("change", () => {
            if (selectAllCheckbox && !checkbox.checked) {
                selectAllCheckbox.checked = false;
            }

            if (
                selectAllCheckbox &&
                rowCheckboxes.length > 0 &&
                Array.from(rowCheckboxes).every((cb) => cb.checked)
            ) {
                selectAllCheckbox.checked = true;
            }

            updateButtons();
        });
    });

    const restoreForm = document.getElementById("restoreForm");

    if (restoreForm) {
        restoreForm.addEventListener("submit", (e) => {
            const selected = getSelected();

            if (selected.length === 0) {
                e.preventDefault();

                alert("Please select at least one visitor to restore.");

                return;
            }

            const selectedIdsElem = document.getElementById("selectedIds");

            if (selectedIdsElem) {
                selectedIdsElem.value = selected
                    .map((cb) => cb.value)
                    .join(",");
            }
        });
    }

    const swapForm = document.getElementById("swapForm");

    if (swapForm) {
        swapForm.addEventListener("submit", (e) => {
            const selected = getSelected();

            if (selected.length !== 1) {
                e.preventDefault();

                alert("Please select exactly one visitor to swap.");

                return;
            }

            const swapSelectedId = document.getElementById("swapSelectedId");

            if (swapSelectedId) {
                swapSelectedId.value = selected[0].value;
            }
        });
    }

    const searchInput = document.getElementById("searchInput");
    const skippedTable = document.getElementById("skippedTable");

    if (searchInput && skippedTable) {
        searchInput.addEventListener("input", () => {
            const searchTerm = searchInput.value.trim().toLowerCase();

            const rows = skippedTable.querySelectorAll("tbody tr");

            rows.forEach((row) => {
                const text = row.textContent.toLowerCase();

                row.style.display = text.includes(searchTerm) ? "" : "none";
            });

            updateButtons();
        });
    }

    updateButtons();
});

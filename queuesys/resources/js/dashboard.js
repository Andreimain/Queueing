if (document.getElementById('totalOffices')) {
    function refreshDashboard() {
        fetch("/dashboard/data")
            .then(res => res.json())
            .then(data => {
                document.getElementById('totalOffices').textContent = data.totalOffices;
                document.getElementById('totalStaff').textContent = data.totalStaff;
                document.getElementById('visitorsToday').textContent = data.visitorsToday;
                document.getElementById('activeQueues').textContent = data.activeQueues;

                const tableBody = document.getElementById('queueTableBody');
                tableBody.innerHTML = data.offices.map(office => `
                    <tr class="hover:bg-green-100">
                        <td class="px-4 py-2 border border-green-300">${office.name}</td>
                        <td class="px-4 py-2 border border-green-300 text-center">${office.waiting_count}</td>
                    </tr>
                `).join('');
            });
    }
    setInterval(refreshDashboard, 3000);
}

if (document.getElementById('currentServing')) {
    function refreshStaffDashboard() {
        fetch("/dashboard/staff-data")
            .then(res => res.json())
            .then(data => {
                if (data.error) return;
                document.getElementById('currentServing').textContent = data.current_serving;
                document.getElementById('waitingCount').textContent = data.waiting_count;
                document.getElementById('skippedCount').textContent = data.skipped_count;
            });
    }
    setInterval(refreshStaffDashboard, 3000);
}

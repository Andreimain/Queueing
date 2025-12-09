// Helper function to update any stats card by data-key
function updateStatCard(key, value) {
    const el = document.querySelector(`[data-key="${key}"]`);
    if (el) el.textContent = value;
}

// Admin dashboard live update
if (document.querySelector('[data-key="totalOffices"]')) {
    const refreshAdminDashboard = async () => {
        try {
            const res = await fetch("/dashboard/data");
            const data = await res.json();

            // Update stat cards dynamically
            updateStatCard('totalOffices', data.totalOffices);
            updateStatCard('totalStaff', data.totalStaff);
            updateStatCard('visitorsToday', data.visitorsToday);
            updateStatCard('activeQueues', data.activeQueues);

            // Update live queue table
            const tableBody = document.getElementById('queueTableBody');
            tableBody.innerHTML = data.offices.map(office => {
                let statusText = 'Idle';
                let statusClass = 'text-gray-400';

                if (office.waiting_count > 5) {
                    statusText = 'Busy';
                    statusClass = 'text-amber-600';
                } else if (office.waiting_count > 0) {
                    statusText = 'Active';
                    statusClass = 'text-emerald-600';
                }

                const waitingClass = office.waiting_count > 0
                    ? 'bg-emerald-100 text-emerald-800'
                    : 'bg-gray-100 text-gray-600';

                return `
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900">${office.name}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-sm font-medium ${waitingClass}">
                                ${office.waiting_count}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-xs font-medium ${statusClass}">${statusText}</span>
                        </td>
                    </tr>
                `;
            }).join('');

        } catch (error) {
            console.error('Error refreshing admin dashboard:', error);
        }
    };

    setInterval(refreshAdminDashboard, 3000);
}

// Staff dashboard live update
if (document.getElementById('currentServing')) {
    const refreshStaffDashboard = async () => {
        try {
            const res = await fetch("/dashboard/staff-data");
            const data = await res.json();
            if (data.error) return;

            document.getElementById('currentServing').textContent = data.current_serving || '—';
            document.getElementById('waitingCount').textContent = data.waiting_count ?? 0;
            document.getElementById('skippedCount').textContent = data.skipped_count ?? 0;
        } catch (error) {
            console.error('Error refreshing staff dashboard:', error);
        }
    };

    setInterval(refreshStaffDashboard, 3000);
}

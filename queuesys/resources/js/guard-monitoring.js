document.addEventListener('DOMContentLoaded', () => {

    const REFRESH_INTERVAL = 3000;

    function refreshVisitors() {

        fetch('/guard/visitors/data', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            cache: 'no-store',
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                return response.json();
            })
            .then(visitors => {
                updateGuardMonitoring(visitors);
            })
            .catch(error => {
                console.error(
                    'Failed to refresh Guard Monitoring:',
                    error
                );
            });
    }

    function updateGuardMonitoring(visitors) {

        const tbody = document.getElementById('visitorTableBody');
        const count = document.getElementById('visitorCount');

        if (!tbody) {
            return;
        }

        if (count) {
            count.textContent =
                `${visitors.length} visitor${visitors.length !== 1 ? 's' : ''}`;
        }

        if (!visitors.length) {

            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                        No visitors are currently waiting or being served.
                    </td>
                </tr>
            `;

            return;
        }

        tbody.innerHTML = '';

        visitors.forEach(visitor => {

            const row = document.createElement('tr');

            row.className = 'hover:bg-gray-50';

            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="font-bold text-gray-900">
                        ${escapeHtml(visitor.ticket_number ?? '—')}
                    </span>
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                    ${escapeHtml(visitor.name ?? '—')}
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                    ${escapeHtml(visitor.office ?? 'N/A')}
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                    ${escapeHtml(visitor.cashier ?? 'Waiting')}
                </td>

                <td class="px-6 py-4 whitespace-nowrap">
                    ${
                        visitor.status === 'serving'
                            ? `
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    Serving
                                </span>
                            `
                            : `
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                    Waiting
                                </span>
                            `
                    }
                </td>
            `;

            tbody.appendChild(row);
        });
    }

    function escapeHtml(value) {

        const div = document.createElement('div');

        div.textContent = String(value);

        return div.innerHTML;
    }

    // Load immediately
    refreshVisitors();

    // Background refresh every 3 seconds
    setInterval(refreshVisitors, REFRESH_INTERVAL);
});

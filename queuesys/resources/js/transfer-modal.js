document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('transferModal');
    const ticketSpan = document.getElementById('newTicketNumber');
    const closeBtn = document.getElementById('closeModalBtn');

    if (!modal) return;

    // Handle all transfer forms (class: transfer-form)
    document.querySelectorAll('.transfer-form').forEach(form => {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Set ticket text
                    ticketSpan.textContent = data.ticket || data.new_ticket || data.ticket_number || '';

                    // Show modal: remove hidden, add flex
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                } else {
                    alert(data.message || 'Transfer failed.');
                }
            } catch (error) {
                console.error(error);
                alert('Something went wrong.');
            }
        });
    });

    // Close modal — hide and remove flex, then reload
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');

            // small delay so user sees it closing; then refresh to update queue
            setTimeout(() => window.location.reload(), 200);
        });
    }
});

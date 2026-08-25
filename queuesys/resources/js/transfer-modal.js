document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('transferModal');
    const ticketSpan = document.getElementById('newTicketNumber');
    const closeBtn = document.getElementById('closeModalBtn');
    const currentOfficeId = String(window.currentOfficeId);
    const currentUserId = String(window.currentUserId);

    document.querySelectorAll('.transfer-office').forEach(officeSelect => {

        officeSelect.addEventListener('change', () => {

            const visitorId = officeSelect.dataset.visitorId;

            const staffSelect = document.querySelector(
                `.transfer-staff[data-visitor-id="${visitorId}"]`
            );

            if (!staffSelect) return;

            const selectedOfficeId = String(officeSelect.value);

            // Reset staff dropdown
            staffSelect.innerHTML = `
                <option value="" disabled selected>
                    -- Transfer to Staff --
                </option>
            `;

            if (selectedOfficeId === currentOfficeId) {

                const staff = window.transferStaff?.[selectedOfficeId] || [];

                staff.forEach(user => {

                    // Don't allow transferring to yourself
                    if (String(user.id) === currentUserId) {
                        return;
                    }

                    staffSelect.innerHTML += `
                        <option value="${user.id}">
                            ${user.name}
                        </option>
                    `;
                });

                staffSelect.disabled = false;
                staffSelect.required = true;

            }

            else {
                staffSelect.disabled = true;
                staffSelect.required = false;
                staffSelect.value = '';
            }
        });
    });

    document.querySelectorAll('.transfer-form').forEach(form => {

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            try {

                const response = await fetch(this.action, {
                    method: 'POST',

                    headers: {
                        'X-CSRF-TOKEN': document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,

                        'X-Requested-With': 'XMLHttpRequest',

                        'Accept': 'application/json',
                    },

                    body: formData
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    alert(data.message || 'Transfer failed.');
                    return;
                }

                if (data.new_ticket) {

                    if (ticketSpan) {
                        ticketSpan.textContent = data.new_ticket;
                    }

                    if (modal) {
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    }

                    return;
                }

                alert(
                    data.message ||
                    'Visitor transferred successfully.'
                );

                setTimeout(() => {
                    window.location.reload();
                }, 200);


            } catch (error) {

                console.error('Transfer error:', error);

                alert(
                    'Something went wrong while transferring the visitor.'
                );
            }
        });
    });

    if (closeBtn) {

        closeBtn.addEventListener('click', () => {

            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            // Refresh after closing modal
            setTimeout(() => {
                window.location.reload();
            }, 200);
        });
    }
});

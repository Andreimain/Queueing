document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('officeForm');

    const nameInput = document.getElementById('name');
    const abbrInput = document.getElementById('abbreviation');
    const officeIdInput = document.getElementById('office_id');

    const submitBtn = document.getElementById('submitBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const formTitle = document.getElementById('formTitle');

    const storeRoute = '/admin/offices';

    document.querySelectorAll('.editOfficeBtn').forEach(btn => {
        btn.addEventListener('click', () => {

            const id = btn.dataset.id;
            const name = btn.dataset.name;
            const abbreviation = btn.dataset.abbreviation;
            officeIdInput.value = id;
            nameInput.value = name;
            abbrInput.value = abbreviation;
            form.action = `/admin/offices/${id}`;
            let methodInput = document.getElementById('methodInput');

            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                methodInput.id = 'methodInput';
                form.appendChild(methodInput);
            }

            submitBtn.textContent = 'Update Office';
            cancelBtn.classList.remove('hidden');
            formTitle.textContent = 'Edit Office';
        });
    });

    cancelBtn.addEventListener('click', () => {
        form.reset();
        officeIdInput.value = '';
        form.action = storeRoute;
        const methodInput = document.getElementById('methodInput');

        if (methodInput) {
            methodInput.remove();
        }
        submitBtn.textContent = 'Add Office';
        cancelBtn.classList.add('hidden');
        formTitle.textContent = 'Add New Office';
    });
});

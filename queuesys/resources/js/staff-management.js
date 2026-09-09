document.addEventListener('DOMContentLoaded', () => {
    const roleSelect = document.getElementById('roleSelect');
    const officeSelect = document.getElementById('officeSelect');
    const officeContainer = document.getElementById('officeContainer');
    const courseAssignment = document.getElementById('courseAssignment');

    if (!officeSelect || !courseAssignment) {
        return;
    }

    function updateFields() {
        const role = roleSelect?.value;
        const selectedOption = officeSelect.options[officeSelect.selectedIndex];

        const noOffice = role === 'guard' || role === 'admin';
        const isRegistrar =
            role === 'staff' &&
            selectedOption?.dataset.abbreviation === 'RO';

        if (noOffice) {
            officeContainer?.classList.add('hidden');
            officeSelect.disabled = true;
            officeSelect.required = false;
            officeSelect.value = '';
        } else {
            officeContainer?.classList.remove('hidden');
            officeSelect.disabled = false;
            officeSelect.required = true;
        }

        courseAssignment.classList.toggle('hidden', !isRegistrar);

        if (!isRegistrar) {
            courseAssignment
                .querySelectorAll('input[type="checkbox"]')
                .forEach(checkbox => {
                    checkbox.checked = false;
                });
        }
    }

    roleSelect?.addEventListener('change', updateFields);
    officeSelect.addEventListener('change', updateFields);

    updateFields();
});

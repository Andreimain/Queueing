document.addEventListener('DOMContentLoaded', () => {
    const roleSelect = document.getElementById('roleSelect');
    const officeSelect = document.getElementById('officeSelect');
    const officeContainer = document.getElementById('officeContainer');
    const handleContainer = document.getElementById('handleContainer');
    const handleSelect = document.getElementById('handleSelect');
    const courseAssignment = document.getElementById('courseAssignment');

    if (!officeSelect) {
        return;
    }

    function updateFields() {
        const role = roleSelect?.value ?? 'staff';
        const selectedOption = officeSelect.options[officeSelect.selectedIndex];
        const noOffice = role === 'guard' || role === 'admin';

        const isRegistrar =
            role === 'staff' &&
            selectedOption?.dataset.abbreviation?.toUpperCase() === 'RO';

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

        handleContainer?.classList.toggle('hidden', !isRegistrar);

        if (handleSelect) {
            handleSelect.required = isRegistrar;

            if (!isRegistrar) {
                handleSelect.value = '';
            }
        }

        const showCourses = isRegistrar && handleSelect?.value === 'Enrollment';

        courseAssignment?.classList.toggle('hidden', !showCourses);
    }

    roleSelect?.addEventListener('change', updateFields);
    officeSelect.addEventListener('change', updateFields);
    handleSelect?.addEventListener('change', updateFields);

    updateFields();
});

document.addEventListener('DOMContentLoaded', () => {
    const officeSelect = document.getElementById('officeSelect');
    const courseAssignment = document.getElementById('courseAssignment');

    if (!officeSelect || !courseAssignment) {
        return;
    }

    function toggleCourseAssignment() {
        const selectedOption = officeSelect.options[officeSelect.selectedIndex];

        const isRegistrar =
            selectedOption?.dataset.abbreviation === 'RO';

        courseAssignment.classList.toggle('hidden', !isRegistrar);

        if (!isRegistrar) {
            courseAssignment
                .querySelectorAll('input[type="checkbox"]')
                .forEach(checkbox => {
                    checkbox.checked = false;
                });
        }
    }

    officeSelect.addEventListener('change', toggleCourseAssignment);

    toggleCourseAssignment();
});

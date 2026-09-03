document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('visitorForm');
    const button = document.getElementById('submitButton');

    const studentType = document.getElementById('studentType');
    const visitorType = document.getElementById('visitorType');

    const idNumberLabel = document.getElementById('idNumberLabel');
    const idNumber = document.getElementById('idNumber');

    const courseField = document.getElementById('courseField');
    const courseSelect = document.getElementById('courseSelect');

    /*
    |--------------------------------------------------------------------------
    | Student / Visitor Registration Type
    |--------------------------------------------------------------------------
    */

    function updateRegistrationType() {
        if (studentType?.checked) {
            // Student
            idNumberLabel.textContent = 'Student ID No.';
            idNumber.required = true;

            courseField.classList.remove('hidden');
            courseSelect.required = true;

        } else if (visitorType?.checked) {
            // Visitor
            idNumberLabel.textContent = 'Visitor ID No.';
            idNumber.required = false;

            courseField.classList.add('hidden');
            courseSelect.required = false;
            courseSelect.value = '';
        }
    }

    studentType?.addEventListener('change', updateRegistrationType);
    visitorType?.addEventListener('change', updateRegistrationType);

    // Restore the correct fields if validation fails
    updateRegistrationType();


    /*
    |--------------------------------------------------------------------------
    | Submit Button
    |--------------------------------------------------------------------------
    */

    if (!form || !button) return;

    form.addEventListener('submit', function () {
        button.disabled = true;
        button.classList.add('opacity-70', 'cursor-not-allowed');

        button.innerHTML = `
            <svg class="animate-spin h-5 w-5 text-emerald-300"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24">

                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4">
                </circle>

                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                </path>

            </svg>

            Processing...
        `;
    });
});

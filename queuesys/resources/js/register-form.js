document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('visitorForm');
    const button = document.getElementById('submitButton');

    if (!form || !button) return;

    form.addEventListener('submit', function () {
        button.disabled = true;
        button.classList.add('opacity-70', 'cursor-not-allowed');

        button.innerHTML = `
            <svg class="animate-spin h-5 w-5 text-emerald-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                </path>
            </svg>
            Processing...
        `;
    });
});

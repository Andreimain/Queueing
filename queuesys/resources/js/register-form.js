document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("visitorForm");
    const button = document.getElementById("submitButton");

    const studentType = document.getElementById("studentType");
    const visitorType = document.getElementById("visitorType");

    const idNumberLabel = document.getElementById("idNumberLabel");
    const idNumber = document.getElementById("idNumber");

    const courseField = document.getElementById("courseField");
    const courseSelect = document.getElementById("courseSelect");

    const officeSelect = document.getElementById("officeSelect");
    const otherOfficeField = document.getElementById("otherOfficeField");
    const otherOfficeInput = document.getElementById("otherOfficeInput");

    // Photo elements
    const photoField = document.getElementById("photoField");
    const cameraContainer = document.getElementById("cameraContainer");
    const cameraPreview = document.getElementById("cameraPreview");
    const photoCanvas = document.getElementById("photoCanvas");

    const openCameraButton = document.getElementById("openCameraButton");
    const capturePhotoButton = document.getElementById("capturePhotoButton");
    const stopCameraButton = document.getElementById("stopCameraButton");

    const photoPreviewContainer = document.getElementById(
        "photoPreviewContainer"
    );
    const photoPreview = document.getElementById("photoPreview");
    const retakePhotoButton = document.getElementById("retakePhotoButton");

    const photoOptions = document.getElementById("photoOptions");
    const photoInput = document.getElementById("photo");

    let cameraStream = null;
    let capturedPhoto = null;
    let previewUrl = null;

    function updateRegistrationType() {
        if (studentType?.checked) {
            idNumberLabel.textContent = "Student ID No.";
            idNumber.required = true;

            courseField.classList.remove("hidden");
            courseSelect.required = true;

            // Hide visitor photo section
            photoField?.classList.add("hidden");

            // Clear any captured photo
            clearPhoto();

            // Stop camera if running
            stopCamera();
        } else if (visitorType?.checked) {
            idNumberLabel.textContent = "Visitor ID No.";
            idNumber.required = false;

            courseField.classList.add("hidden");
            courseSelect.required = false;
            courseSelect.value = "";

            // Show visitor photo section
            photoField?.classList.remove("hidden");
        }
    }

    function updateOfficeSelection() {
        const isOthers = officeSelect?.value === "others";

        if (isOthers) {
            otherOfficeField?.classList.remove("hidden");

            if (otherOfficeInput) {
                otherOfficeInput.required = true;
            }
        } else {
            otherOfficeField?.classList.add("hidden");

            if (otherOfficeInput) {
                otherOfficeInput.required = false;
                otherOfficeInput.value = "";
            }
        }
    }

    function stopCamera() {
        if (cameraStream) {
            cameraStream.getTracks().forEach((track) => {
                track.stop();
            });

            cameraStream = null;
        }

        if (cameraPreview) {
            cameraPreview.srcObject = null;
        }

        cameraContainer?.classList.add("hidden");
    }

    function clearPhoto() {
        capturedPhoto = null;

        if (photoInput) {
            photoInput.value = "";
        }

        if (photoPreview) {
            photoPreview.src = "";
        }

        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
            previewUrl = null;
        }

        photoPreviewContainer?.classList.add("hidden");
        photoOptions?.classList.remove("hidden");
    }

    async function openCamera() {
        try {
            stopCamera();

            cameraStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: "user",
                },
                audio: false,
            });

            cameraPreview.srcObject = cameraStream;

            cameraContainer?.classList.remove("hidden");
            photoOptions?.classList.add("hidden");
            photoPreviewContainer?.classList.add("hidden");
        } catch (error) {
            console.error("Camera error:", error);

            alert(
                "Unable to access the camera. Please allow camera access and try again."
            );
        }
    }

    function capturePhoto() {
        if (!cameraStream || !cameraPreview) {
            return;
        }

        const width = cameraPreview.videoWidth;
        const height = cameraPreview.videoHeight;

        if (!width || !height) {
            alert("Camera is not ready yet. Please try again.");
            return;
        }

        photoCanvas.width = width;
        photoCanvas.height = height;

        const context = photoCanvas.getContext("2d");

        context.drawImage(cameraPreview, 0, 0, width, height);

        photoCanvas.toBlob(
            function (blob) {
                if (!blob) {
                    alert("Failed to capture the photo. Please try again.");
                    return;
                }

                capturedPhoto = new File([blob], "visitor-photo.jpg", {
                    type: "image/jpeg",
                });

                /*
                 * Put the captured camera photo into the
                 * hidden file input so Laravel receives it
                 * as a normal uploaded file.
                 */
                const dataTransfer = new DataTransfer();

                dataTransfer.items.add(capturedPhoto);

                photoInput.files = dataTransfer.files;

                // Show preview
                if (previewUrl) {
                    URL.revokeObjectURL(previewUrl);
                }

                previewUrl = URL.createObjectURL(capturedPhoto);

                photoPreview.src = previewUrl;

                photoPreviewContainer?.classList.remove("hidden");
                cameraContainer?.classList.add("hidden");
                photoOptions?.classList.add("hidden");

                // Camera no longer needed
                stopCamera();
            },
            "image/jpeg",
            0.9
        );
    }

    function retakePhoto() {
        clearPhoto();

        openCamera();
    }

    // Registration type
    studentType?.addEventListener("change", updateRegistrationType);

    visitorType?.addEventListener("change", updateRegistrationType);

    // Office selection
    officeSelect?.addEventListener("change", updateOfficeSelection);

    // Camera buttons
    openCameraButton?.addEventListener("click", openCamera);

    capturePhotoButton?.addEventListener("click", capturePhoto);

    stopCameraButton?.addEventListener("click", function () {
        stopCamera();

        photoOptions?.classList.remove("hidden");
    });

    retakePhotoButton?.addEventListener("click", retakePhoto);

    // Initial state
    updateRegistrationType();
    updateOfficeSelection();

    if (!form || !button) {
        return;
    }

    form.addEventListener("submit", function (event) {
        /*
         * Visitors MUST have a camera-captured photo.
         */
        if (visitorType?.checked && !capturedPhoto) {
            event.preventDefault();

            alert("Please take a photo before registering.");

            return;
        }

        // Stop camera before submitting
        stopCamera();

        // Prevent double submission
        button.disabled = true;

        button.classList.add("opacity-70", "cursor-not-allowed");

        button.innerHTML = `
            <svg
                class="animate-spin h-5 w-5 text-emerald-300"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                ></circle>

                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                ></path>
            </svg>

            Processing...
        `;
    });
});

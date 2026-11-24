document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("visitorForm");
    const button = document.getElementById("submitButton");

    const studentType = document.getElementById("studentType");
    const visitorType = document.getElementById("visitorType");

    const idNumberLabel = document.getElementById("idNumberLabel");
    const idNumber = document.getElementById("idNumber");
    const scanIdButton = document.getElementById("scanIdButton");
    const scanStatus = document.getElementById("scanStatus");

    const courseField = document.getElementById("courseField");
    const courseSelect = document.getElementById("courseSelect");

    const officeSelect = document.getElementById("officeSelect");
    const otherOfficeField = document.getElementById("otherOfficeField");
    const otherOfficeInput = document.getElementById("otherOfficeInput");

    const registrarPurposeField = document.getElementById("registrarPurposeField");
    const purposeSelect = document.getElementById("purposeSelect");

    const photoField = document.getElementById("photoField");
    const cameraContainer = document.getElementById("cameraContainer");
    const cameraPreview = document.getElementById("cameraPreview");
    const photoCanvas = document.getElementById("photoCanvas");

    const openCameraButton = document.getElementById("openCameraButton");
    const capturePhotoButton = document.getElementById("capturePhotoButton");
    const stopCameraButton = document.getElementById("stopCameraButton");

    const photoPreviewContainer = document.getElementById("photoPreviewContainer");
    const photoPreview = document.getElementById("photoPreview");
    const retakePhotoButton = document.getElementById("retakePhotoButton");

    const photoOptions = document.getElementById("photoOptions");
    const photoInput = document.getElementById("photo");

    let cameraStream = null;
    let capturedPhoto = null;
    let previewUrl = null;

    function hideIdScanning() {
        scanIdButton?.classList.add("hidden");
        scanStatus?.classList.add("hidden");
    }

    function stopCamera() {
        if (cameraStream) {
            cameraStream.getTracks().forEach((track) => track.stop());
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

    function updateRegistrationType() {
        hideIdScanning();

        if (studentType?.checked) {
            idNumberLabel.textContent = "Student ID No.";
            idNumber.required = true;

            courseField?.classList.remove("hidden");

            if (courseSelect) {
                courseSelect.required = true;
            }

            photoField?.classList.add("hidden");

            clearPhoto();
            stopCamera();
        } else if (visitorType?.checked) {
            idNumberLabel.textContent = "Visitor ID No.";
            idNumber.required = false;

            courseField?.classList.add("hidden");

            if (courseSelect) {
                courseSelect.required = false;
                courseSelect.value = "";
            }

            photoField?.classList.remove("hidden");
        } else {
            idNumberLabel.textContent = "ID Number";
            idNumber.required = false;

            courseField?.classList.add("hidden");
            photoField?.classList.add("hidden");

            if (courseSelect) {
                courseSelect.required = false;
            }

            stopCamera();
        }
    }

    function isRegistrarSelected() {
        const selectedOption =
            officeSelect?.options[officeSelect.selectedIndex];

        return selectedOption?.dataset.abbreviation?.toUpperCase() === "RO";
    }

    function updateRegistrarPurpose() {
        const isRegistrar = isRegistrarSelected();

        registrarPurposeField?.classList.toggle("hidden", !isRegistrar);

        if (purposeSelect) {
            purposeSelect.required = isRegistrar;

            if (!isRegistrar) {
                purposeSelect.value = "";
            }
        }
    }

    function updateOfficeSelection() {
        const isOthers = officeSelect?.value === "others";

        otherOfficeField?.classList.toggle("hidden", !isOthers);

        if (otherOfficeInput) {
            otherOfficeInput.required = isOthers;

            if (!isOthers) {
                otherOfficeInput.value = "";
            }
        }

        updateRegistrarPurpose();
    }

    async function openCamera() {
        try {
            stopCamera();

            if (!navigator.mediaDevices?.getUserMedia) {
                alert("Camera access is unavailable. Please use a supported browser and a secure connection.");
                return;
            }

            cameraStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: "user" },
                audio: false,
            });

            cameraPreview.srcObject = cameraStream;

            cameraContainer?.classList.remove("hidden");
            photoOptions?.classList.add("hidden");
            photoPreviewContainer?.classList.add("hidden");
        } catch (error) {
            console.error("Camera error:", error);

            alert("Unable to access the camera. Please allow camera access and try again.");
        }
    }

    function capturePhoto() {
        if (!cameraStream || !cameraPreview || !photoCanvas || !photoInput) {
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

        if (!context) {
            alert("Unable to capture the photo. Please try again.");
            return;
        }

        context.drawImage(cameraPreview, 0, 0, width, height);

        photoCanvas.toBlob(function (blob) {
            if (!blob) {
                alert("Failed to capture the photo. Please try again.");
                return;
            }

            capturedPhoto = new File([blob], "visitor-photo.jpg", {
                type: "image/jpeg",
            });

            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(capturedPhoto);
            photoInput.files = dataTransfer.files;

            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
            }

            previewUrl = URL.createObjectURL(capturedPhoto);
            photoPreview.src = previewUrl;

            photoPreviewContainer?.classList.remove("hidden");
            cameraContainer?.classList.add("hidden");
            photoOptions?.classList.add("hidden");

            stopCamera();
        }, "image/jpeg", 0.9);
    }

    function retakePhoto() {
        clearPhoto();
        openCamera();
    }

    studentType?.addEventListener("change", updateRegistrationType);
    visitorType?.addEventListener("change", updateRegistrationType);

    officeSelect?.addEventListener("change", updateOfficeSelection);

    // Scan ID stays hidden on the Registration page.
    hideIdScanning();

    openCameraButton?.addEventListener("click", openCamera);
    capturePhotoButton?.addEventListener("click", capturePhoto);

    stopCameraButton?.addEventListener("click", function () {
        stopCamera();
        photoOptions?.classList.remove("hidden");
    });

    retakePhotoButton?.addEventListener("click", retakePhoto);

    updateRegistrationType();
    updateOfficeSelection();

    if (!form || !button) {
        return;
    }

    form.addEventListener("submit", function (event) {
        if (isRegistrarSelected() && !purposeSelect?.value) {
            event.preventDefault();

            alert("Please select a Registrar purpose.");
            purposeSelect?.focus();

            return;
        }

        if (visitorType?.checked && !capturedPhoto) {
            event.preventDefault();

            alert("Please take a photo before registering.");

            return;
        }

        stopCamera();
        hideIdScanning();

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

// ============================================
// REPORT MODAL FUNCTIONALITY
// ============================================

let currentStep = 1;
const totalSteps = 3;
let selectedPhotos = [];
let searchTimeout = null;

// Initialize
document.addEventListener("DOMContentLoaded", function () {
    console.log("Report modal initialized");
    initializeReportModal();
});

function initializeReportModal() {
    // Character counter for description
    const descriptionField = document.querySelector(
        'textarea[name="description"]',
    );
    if (descriptionField) {
        descriptionField.addEventListener("input", function () {
            const charCount = document.getElementById("charCount");
            if (charCount) {
                charCount.textContent = this.value.length;
            }
        });
    }

    // Location search with debounce
    const locationSearch = document.getElementById("locationSearch");
    if (locationSearch) {
        locationSearch.addEventListener("input", function () {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                hideLocationResults();
                return;
            }

            searchTimeout = setTimeout(() => {
                searchLocations(query);
            }, 300);
        });

        // Hide results when clicking outside
        document.addEventListener("click", function (e) {
            if (!e.target.closest(".location-search-wrapper")) {
                hideLocationResults();
            }
        });
    }

    // Form submission
    const reportForm = document.getElementById("reportForm");
    if (reportForm) {
        reportForm.addEventListener("submit", function (e) {
            e.preventDefault();
            submitReport();
        });
    }
}

// ============================================
// MODAL CONTROLS
// ============================================

function openReportModal() {
    const modal = document.getElementById("reportModal");
    if (modal) {
        modal.style.display = "flex";
        document.body.style.overflow = "hidden";
        resetForm();

        // Auto-set current location if available
        if (window.currentLocationData && window.currentLocationData.id > 0) {
            console.log("Auto-setting location:", window.currentLocationData);
            setLocation(
                window.currentLocationData.id,
                window.currentLocationData.name,
            );
        }
    }
}

function closeReportModal() {
    const modal = document.getElementById("reportModal");
    if (modal) {
        modal.style.display = "none";
        document.body.style.overflow = "";
        resetForm();
    }
}

function closeSuccessModal() {
    const modal = document.getElementById("successModal");
    if (modal) {
        modal.style.display = "none";
        document.body.style.overflow = "";
    }
}

function resetForm() {
    currentStep = 1;
    selectedPhotos = [];

    const form = document.getElementById("reportForm");
    if (form) {
        form.reset();
    }

    updateStepDisplay();
    hideLocationResults();
    clearPhotoPreview();

    // Reset location display
    const selectedDisplay = document.getElementById("selectedLocationDisplay");
    if (selectedDisplay) {
        selectedDisplay.style.display = "none";
    }

    const locationSearch = document.getElementById("locationSearch");
    if (locationSearch) {
        locationSearch.style.display = "block";
    }
}

// ============================================
// STEP NAVIGATION
// ============================================

function nextStep() {
    if (!validateCurrentStep()) {
        return;
    }

    if (currentStep < totalSteps) {
        // Mark current step as completed
        const currentStepEl = document.querySelector(
            `.report-step[data-step="${currentStep}"]`,
        );
        if (currentStepEl) {
            currentStepEl.classList.add("completed");
            currentStepEl.classList.remove("active");
        }

        currentStep++;
        updateStepDisplay();
    }
}

function previousStep() {
    if (currentStep > 1) {
        currentStep--;
        updateStepDisplay();

        // Remove completed class from current step
        const currentStepEl = document.querySelector(
            `.report-step[data-step="${currentStep}"]`,
        );
        if (currentStepEl) {
            currentStepEl.classList.remove("completed");
        }
    }
}

function updateStepDisplay() {
    // Hide all steps
    document.querySelectorAll(".report-form-step").forEach((step) => {
        step.style.display = "none";
    });

    // Show current step
    const currentStepEl = document.getElementById(`step-${currentStep}`);
    if (currentStepEl) {
        currentStepEl.style.display = "block";
    }

    // Update step indicators
    document.querySelectorAll(".report-step").forEach((step, index) => {
        const stepNum = index + 1;
        if (stepNum < currentStep) {
            step.classList.add("completed");
            step.classList.remove("active");
        } else if (stepNum === currentStep) {
            step.classList.add("active");
            step.classList.remove("completed");
        } else {
            step.classList.remove("active", "completed");
        }
    });

    // Update buttons
    const btnPrev = document.getElementById("btnPrev");
    const btnNext = document.getElementById("btnNext");
    const btnSubmit = document.getElementById("btnSubmit");

    if (btnPrev) {
        btnPrev.style.display = currentStep === 1 ? "none" : "inline-flex";
    }

    if (btnNext) {
        btnNext.style.display =
            currentStep === totalSteps ? "none" : "inline-flex";
    }

    if (btnSubmit) {
        btnSubmit.style.display =
            currentStep === totalSteps ? "inline-flex" : "none";
    }
}

function validateCurrentStep() {
    if (currentStep === 1) {
        // Validate location selection
        const locationId = document.getElementById("selectedLocationId");
        if (!locationId || !locationId.value) {
            showError("Silakan pilih lokasi mangrove terlebih dahulu");
            return false;
        }
    } else if (currentStep === 2) {
        // Validate report details
        const reportType = document.querySelector('select[name="report_type"]');
        const urgencyLevel = document.querySelector(
            'input[name="urgency_level"]:checked',
        );
        const description = document.querySelector(
            'textarea[name="description"]',
        );

        if (!reportType || !reportType.value) {
            showError("Silakan pilih jenis laporan");
            return false;
        }

        if (!urgencyLevel) {
            showError("Silakan pilih tingkat urgensi");
            return false;
        }

        if (!description || description.value.trim().length < 20) {
            showError("Deskripsi minimal 20 karakter");
            return false;
        }
    } else if (currentStep === 3) {
        // Validate reporter information
        const name = document.querySelector('input[name="reporter_name"]');
        const email = document.querySelector('input[name="reporter_email"]');
        const phone = document.querySelector('input[name="reporter_phone"]');

        if (!name || !name.value.trim()) {
            showError("Nama lengkap wajib diisi");
            return false;
        }

        if (!email || !email.value.trim() || !isValidEmail(email.value)) {
            showError("Email tidak valid");
            return false;
        }

        if (!phone || !phone.value.trim()) {
            showError("Nomor telepon wajib diisi");
            return false;
        }
    }

    return true;
}

function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// ============================================
// LOCATION SEARCH
// ============================================

async function searchLocations(query) {
    try {
        const response = await fetch(
            `/reports/search-locations?q=${encodeURIComponent(query)}`,
        );
        const locations = await response.json();

        displayLocationResults(locations);
    } catch (error) {
        console.error("Location search error:", error);
        showError("Gagal mencari lokasi");
    }
}

function displayLocationResults(locations) {
    const resultsDiv = document.getElementById("locationResults");
    if (!resultsDiv) return;

    if (locations.length === 0) {
        resultsDiv.innerHTML =
            '<div class="location-result-item">Tidak ada lokasi ditemukan</div>';
        resultsDiv.style.display = "block";
        return;
    }

    resultsDiv.innerHTML = locations
        .map(
            (location) => `
        <div class="location-result-item" onclick="selectLocation(${location.id}, '${escapeHtml(location.display_name)}')">
            <strong>${escapeHtml(location.name)}</strong>
            ${location.display_name !== location.name ? `<small>${escapeHtml(location.display_name)}</small>` : ""}
        </div>
    `,
        )
        .join("");

    resultsDiv.style.display = "block";
}

function hideLocationResults() {
    const resultsDiv = document.getElementById("locationResults");
    if (resultsDiv) {
        resultsDiv.style.display = "none";
    }
}

function selectLocation(id, name) {
    setLocation(id, name);
    hideLocationResults();
}

function setLocation(id, name) {
    const locationId = document.getElementById("selectedLocationId");
    const locationSearch = document.getElementById("locationSearch");
    const selectedDisplay = document.getElementById("selectedLocationDisplay");
    const selectedName = document.getElementById("selectedLocationName");

    if (locationId) locationId.value = id;
    if (locationSearch) locationSearch.style.display = "none";
    if (selectedDisplay) selectedDisplay.style.display = "flex";
    if (selectedName) selectedName.textContent = name;
}

function clearLocationSelection() {
    const locationId = document.getElementById("selectedLocationId");
    const locationSearch = document.getElementById("locationSearch");
    const selectedDisplay = document.getElementById("selectedLocationDisplay");

    if (locationId) locationId.value = "";
    if (locationSearch) {
        locationSearch.style.display = "block";
        locationSearch.value = "";
    }
    if (selectedDisplay) selectedDisplay.style.display = "none";
}

// ============================================
// PHOTO HANDLING
// ============================================

function previewPhotos(event) {
    const files = Array.from(event.target.files);

    // Validate number of files
    if (files.length > 5) {
        showError("Maksimal 5 foto");
        event.target.value = "";
        return;
    }

    // Validate file sizes
    const oversizedFiles = files.filter((file) => file.size > 5 * 1024 * 1024);
    if (oversizedFiles.length > 0) {
        showError("Beberapa foto melebihi ukuran maksimal 5MB");
        event.target.value = "";
        return;
    }

    // Store files
    selectedPhotos = files;

    // Display previews
    const previewDiv = document.getElementById("photoPreview");
    if (!previewDiv) return;

    previewDiv.innerHTML = "";

    files.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function (e) {
            const photoItem = document.createElement("div");
            photoItem.className = "photo-preview-item";
            photoItem.innerHTML = `
                <img src="${e.target.result}" alt="Preview ${index + 1}">
                <button type="button" class="photo-preview-remove" onclick="removePhoto(${index})">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
                    </svg>
                </button>
            `;
            previewDiv.appendChild(photoItem);
        };
        reader.readAsDataURL(file);
    });
}

function removePhoto(index) {
    selectedPhotos.splice(index, 1);

    // Update file input
    const photoInput = document.getElementById("photoInput");
    if (photoInput) {
        const dt = new DataTransfer();
        selectedPhotos.forEach((file) => dt.items.add(file));
        photoInput.files = dt.files;

        // Trigger preview update
        previewPhotos({ target: photoInput });
    }
}

function clearPhotoPreview() {
    const previewDiv = document.getElementById("photoPreview");
    if (previewDiv) {
        previewDiv.innerHTML = "";
    }
    selectedPhotos = [];
}

// ============================================
// FORM SUBMISSION
// ============================================

async function submitReport() {
    if (!validateCurrentStep()) {
        return;
    }

    const submitBtn = document.getElementById("btnSubmit");
    if (!submitBtn) return;

    // Disable button and show loading
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
        <svg class="animate-spin" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
        </svg>
        <span>Mengirim...</span>
    `;

    try {
        const form = document.getElementById("reportForm");
        const formData = new FormData(form);

        const response = await fetch("/reports/submit", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": document.querySelector('input[name="_token"]')
                    .value,
            },
        });

        const result = await response.json();

        if (result.success) {
            // Show success modal
            closeReportModal();
            showSuccessModal(result.report_number);
        } else {
            if (result.errors) {
                // Show validation errors
                const firstError = Object.values(result.errors)[0][0];
                showError(firstError);
            } else {
                showError(result.message || "Gagal mengirim laporan");
            }
        }
    } catch (error) {
        console.error("Submit error:", error);
        showError(
            "Terjadi kesalahan saat mengirim laporan. Silakan coba lagi.",
        );
    } finally {
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

function showSuccessModal(reportNumber) {
    const modal = document.getElementById("successModal");
    const reportNumberEl = document.getElementById("reportNumber");

    if (modal && reportNumberEl) {
        reportNumberEl.textContent = reportNumber;
        modal.style.display = "flex";
        document.body.style.overflow = "hidden";
    }
}

// ============================================
// UTILITY FUNCTIONS
// ============================================

function showError(message) {
    // You can use SweetAlert2, Alertify, or custom notification
    if (typeof Swal !== "undefined") {
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: message,
            confirmButtonColor: "#009966",
        });
    } else if (typeof alertify !== "undefined") {
        alertify.error(message);
    } else {
        alert(message);
    }
}

function showSuccess(message) {
    if (typeof Swal !== "undefined") {
        Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: message,
            confirmButtonColor: "#009966",
        });
    } else if (typeof alertify !== "undefined") {
        alertify.success(message);
    } else {
        alert(message);
    }
}

function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}

// Make functions globally available
window.openReportModal = openReportModal;
window.closeReportModal = closeReportModal;
window.closeSuccessModal = closeSuccessModal;
window.nextStep = nextStep;
window.previousStep = previousStep;
window.selectLocation = selectLocation;
window.setLocation = setLocation;
window.clearLocationSelection = clearLocationSelection;
window.previewPhotos = previewPhotos;
window.removePhoto = removePhoto;

console.log("Report modal script loaded successfully");

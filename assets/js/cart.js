// Global variables to store selections
let selectedFrameId = null;
let selectedLensId = null;
let selectedCategoryId = null;

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add event listener to "Add to Cart" button
    const addToCartBtn = document.getElementById('addToCartBtn');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            // Get frame_id from URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            selectedFrameId = urlParams.get('frame_id');

            // Load and show lens categories
            loadLensCategories();
        });
    } else {
        console.error('Add to Cart button not found');
    }
});

// Load lens categories from API
function loadLensCategories() {
    fetch('api/get_lens_categories.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const categoryList = document.getElementById('lensCategoryList');
                categoryList.innerHTML = data.categories.map(category => `
                    <button type="button" class="border-0 bg-white text-start p-3 rounded-4 shadow-sm d-flex align-items-center justify-content-between"
                            onclick="selectLensCategory(${category.id}, '${category.name}')">
                      <div class="d-flex align-items-center gap-3">
                        <div class="icon-placeholder bg-light rounded-circle" style="width: 40px; height: 40px;"></div>
                        <div>
                          <div class="fw-semibold fs-6 text-dark">${category.name}</div>
                          <div class="text-muted small">${category.description}</div>
                        </div>
                      </div>
                      <i class="bi bi-chevron-right fs-5 text-muted"></i>
                    </button>
                  `).join('');
                const lensCategoryModal = new bootstrap.Modal(document.getElementById('lensCategoryModal'));
                lensCategoryModal.show();
            }
        })
        .catch(error => {
            console.error('Error loading lens categories:', error);
            alert('Error loading lens categories. Please try again.');
        });
}

// Handle lens category selection
function selectLensCategory(categoryId, categoryName) {
    selectedCategoryId = categoryId;

    // Load lens options for selected category
    loadLensOptions(categoryId);
}

// Load lens options from API
function loadLensOptions(categoryId) {
    try {
        fetch(`api/get_lens_options.php?category_id=${categoryId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message || 'Failed to load lens options');
                }

                const lensOptionsContainer = document.querySelector('#lensCompanyModal .modal-body');
                if (!lensOptionsContainer) {
                    throw new Error('Lens options container not found');
                }

                if (!data.lenses || !Array.isArray(data.lenses)) {
                    throw new Error('Invalid lens data received from server');
                }

                if (data.lenses.length === 0) {
                    lensOptionsContainer.innerHTML = `
                        <div class="text-center py-4">
                            <div class="text-muted">No lens options available for this category</div>
                        </div>
                    `;
                } else {
                    lensOptionsContainer.innerHTML = `
                        <div class="list-group list-group-flush">
                            ${data.lenses.map(lens => `
                                <button type="button" class="list-group-item list-group-item-action border-0 p-0 mb-3 w-100"
                                        onclick="selectLensOption(${lens.id}, '${lens.name}')">
                                    <div class="card border-0 shadow-sm hover-shadow-md transition-all w-100">
                                        <div class="card-body p-4">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center flex-grow-1">
                                                    <div class="lens-icon rounded-circle bg-primary bg-opacity-10 p-3 me-4">
                                                        <i class="bi bi-bullseye text-primary fs-4"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <h6 class="fw-bold mb-0 fs-5">${lens.name}</h6>
                                                            <span class="badge bg-primary rounded-pill px-4 py-2 ms-3">$${lens.price}</span>
                                                        </div>
                                                        <p class="text-muted mb-0">${lens.description || ''}</p>
                                                    </div>
                                                </div>
                                                <i class="bi bi-chevron-right text-primary fs-4 ms-4"></i>
                                            </div>
                                        </div>
                                    </div>
                                </button>
                            `).join('')}
                        </div>
                    `;
                }

                // Close lens category modal
                const categoryModal = bootstrap.Modal.getInstance(document.getElementById('lensCategoryModal'));
                if (categoryModal) {
                    categoryModal.hide();
                }

                // Show lens options modal
                const lensOptionsModal = document.getElementById('lensCompanyModal');
                if (!lensOptionsModal) {
                    throw new Error('Lens options modal not found');
                }
                const bsLensOptionsModal = new bootstrap.Modal(lensOptionsModal);
                bsLensOptionsModal.show();
            })
            .catch(error => {
                throw error;
            });
    } catch (error) {
        console.error('Error loading lens options:', error);
        alert('Error loading lens options. Please try again.');
    }
}

// Handle lens option selection
function selectLensOption(lensId, lensPrice) {
    selectedLensId = lensId;

    // Close lens options modal
    const currentModal = bootstrap.Modal.getInstance(document.getElementById('lensOptionsModal'));
    if (currentModal) {
        currentModal.hide();
    }

    // Show prescription modal
    const prescriptionModal = new bootstrap.Modal(document.getElementById('prescriptionModal'));
    prescriptionModal.show();
}

// Handle prescription submission
function submitPrescription(event) {
    if (event) {
        event.preventDefault();
    }

    // Validate required fields
    const requiredFields = ['left_eye_sph', 'right_eye_sph', 'left_eye_cyl', 'right_eye_cyl', 'axis'];
    let isValid = true;
    let missingFields = [];

    requiredFields.forEach(field => {
        const element = document.getElementById(field);
        if (!element || !element.value.trim()) {
            isValid = false;
            missingFields.push(field.replace(/_/g, ' '));
        }
    });

    if (!isValid) {
        alert('Please fill in all required fields: ' + missingFields.join(', '));
        return;
    }

    const formData = {
        frame_id: selectedFrameId,
        lens_id: selectedLensId,
        prescription: {
            left_eye_sph: document.getElementById('left_eye_sph').value,
            right_eye_sph: document.getElementById('right_eye_sph').value,
            left_eye_cyl: document.getElementById('left_eye_cyl').value,
            right_eye_cyl: document.getElementById('right_eye_cyl').value,
            axis: document.getElementById('axis').value,
            addition: document.getElementById('addition')?.value || ''
        }
    };

    addToCart(formData);
}

// Handle skipping prescription
function skipPrescription() {
    const formData = {
        frame_id: selectedFrameId,
        lens_id: selectedLensId
    };

    addToCart(formData);
}

// Add item to cart
function addToCart(formData) {
    // Validate frame_id and lens_id
    if (!formData.frame_id || !formData.lens_id) {
        alert('Missing frame or lens selection');
        return;
    }

    fetch('api/save_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Close prescription modal if it's open
            const prescriptionModal = document.getElementById('prescriptionModal');
            if (prescriptionModal) {
                const bsModal = bootstrap.Modal.getInstance(prescriptionModal);
                if (bsModal) {
                    bsModal.hide();
                }
            }

            // Show success modal
            const successModal = document.getElementById('successModal');
            if (successModal) {
                const bsSuccessModal = new bootstrap.Modal(successModal);
                bsSuccessModal.show();
            }

            // Update cart count if needed
            updateCartCount();
        } else {
            throw new Error(data.error || 'Error adding item to cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'Error adding item to cart. Please try again.');
    });
}

// Helper function to go back to previous modal
function goBackToPreviousModal(modalId) {
    // Close current modal
    const currentModal = bootstrap.Modal.getInstance(document.querySelector('.modal.show'));
    if (currentModal) {
        currentModal.hide();
    }

    // Show previous modal
    const previousModal = new bootstrap.Modal(document.querySelector(modalId));
    previousModal.show();
}

// Optional: Update cart count in navbar
function updateCartCount() {
    fetch('/api/get_cart_count.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cartCountElement = document.getElementById('cartCount');
                if (cartCountElement) {
                    cartCountElement.textContent = data.count;
                }
            }
        })
        .catch(error => console.error('Error updating cart count:', error));
}

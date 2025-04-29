<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List Modal</title>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal.show {
            display: flex;
        }

        .modal-dialog {
            background-color: rgb(250, 249, 247);
            border-radius: 8px;
            overflow: hidden;
            max-width: 55vw;
            width: 100%;
            height: 100vh;
            position: fixed;
            right: 0;
            top: 0;
            transform: translateX(100%);
            transition: transform 0.4s ease-in-out;
        }

        .modal.show .modal-dialog {
            transform: translateX(0);
        }

        .modal-header {
            /* display: flex; */
            /* justify-content: space-between; */
            /* padding: 25px; */
            background-color: #faf9f7;
            color: black;
            display: flex;
            -webkit-box-align: center;
            align-items: center;
            -webkit-box-pack: justify;
            justify-content: space-between;
            padding: 20px 46px;
        }

        .modal-body {
            padding: 20px;
            overflow-y: auto;
            max-height: calc(100vh - 60px);
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: white;
        }

        svg {
            pointer-events: auto !important;
            cursor: pointer;
        }

        .list-group {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .list-group-item {
            display: flex;
            flex-direction: row;
            -webkit-box-align: center;
            align-items: center;
            -webkit-box-pack: justify;
            /* justify-content: space-between; */
            height: 10vh;
            align-items: center;
            padding: 24px;
            gap: 24px;
            margin: 10px 0;
            border-radius: 5px;
            border-bottom: 1px solid #ddd;
            opacity: 0;
            cursor: pointer;
            transition: box-shadow 0.2s ease-in-out;
            box-shadow: rgba(0, 0, 66, 0.06) 0px 0px 12px;
            background-color: white;
            transform: translateY(20px);
            animation: fadeInUp 0.5s ease-in-out forwards;
        }

        .list-group-item:nth-child(1) {
            animation-delay: 0.2s;
        }

        .list-group-item:nth-child(2) {
            animation-delay: 0.4s;
        }

        .list-group-item:nth-child(3) {
            animation-delay: 0.6s;
        }

        .list-group-item img {
            width: 50px;
            height: 50px;
            margin-right: 15px;
            border-radius: 5px;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 992px) {
            .modal-dialog {
                position: fixed;
                bottom: 0;
                top: auto;
                width: 100%;
                height: 80vh;
                max-width: none;
                border-radius: 15px 15px 0 0;
                transform: translateY(100%);
            }

            .modal.show .modal-dialog {
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
<!-- Lens Category Modal -->
<div class="modal fade" id="lenscat">
    <div class="modal-dialog">
        <div class="modal-header">
            <h1>Select Lens Category</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <!-- Dynamic content inserted here -->
        </div>
    </div>
</div>

<!-- Lens Company Modal -->
<div class="modal fade" id="lenscompany">
    <div class="modal-dialog">
        <div class="modal-header">
            <h1>Select Lens Company</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <!-- Dynamic content inserted here -->
        </div>
    </div>
</div>

<!-- Lens Type (Prescription Details) Modal -->
<div class="modal fade" id="lenstype">
    <div class="modal-dialog">
        <div class="modal-header">
            <h1>Enter Prescription</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <!-- Input fields for prescription details (left, right eye, etc.) -->
            <input type="text" id="power_left" placeholder="Power Left" class="form-control mt-2">
            <input type="text" id="power_right" placeholder="Power Right" class="form-control mt-2">
            <input type="text" id="cyc_left" placeholder="CYC Left" class="form-control mt-2">
            <input type="text" id="cyc_right" placeholder="CYC Right" class="form-control mt-2">
        </div>
    </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
let selectedFrameId = null;
let selectedCategoryId = null;
let selectedCompanyId = null;
let selectedLensId = null;
let prescriptionDetails = {};

// When clicking "Add to Cart"
document.getElementById("addToCartBtn").addEventListener("click", function() {
    const frameId = getFrameIdFromUrl();

    if (frameId) {
        openLensCategoryModal(frameId);  // Open the lens category modal if a frame is selected
    } else {
        alert("Frame ID not found!");
    }
});

// Function to get the frame ID from the URL
function getFrameIdFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('frame_id'); // Frame ID passed in the URL
}

// Open Lens Category Modal
function openLensCategoryModal(frameId) {
    selectedFrameId = frameId;
    $.ajax({
        url: 'get_lens_categories.php',
        method: 'GET',
        success: function(data) {
            const categories = JSON.parse(data);
            let html = '';
            categories.forEach(cat => {
                html += `<div onclick="selectCategory(${cat.id})" class="list-group-item">${cat.category_name}</div>`;
            });
            $('#lenscat .modal-body').html(html);
            $('#lenscat').modal('show');
        },
        error: function() {
            console.log("Error fetching lens categories");
        }
    });
}

// Select Lens Category
function selectCategory(categoryId) {
    selectedCategoryId = categoryId;
    $.ajax({
        url: 'get_lens_companies.php?category_id=' + categoryId,
        method: 'GET',
        success: function(data) {
            const companies = JSON.parse(data);
            let html = '';
            companies.forEach(company => {
                html += `<div onclick="selectCompany(${company.id})" class="list-group-item">${company.company_name}</div>`;
            });
            $('#lenscompany .modal-body').html(html);
            $('#lenscompany').modal('show');
        },
        error: function() {
            console.log("Error fetching lens companies");
        }
    });
}

// Select Lens Company
function selectCompany(companyId) {
    selectedCompanyId = companyId;
    $.ajax({
        url: 'get_lenses.php?company_id=' + companyId,
        method: 'GET',
        success: function(data) {
            const lenses = JSON.parse(data);
            let html = '<select id="lens_select" class="form-select">';
            lenses.forEach(lens => {
                html += `<option value="${lens.id}">${lens.lens_name}</option>`;
            });
            html += '</select>';
            $('#lenstype .modal-body').html(html);
            $('#lenstype').modal('show');
        },
        error: function() {
            console.log("Error fetching lenses");
        }
    });
}

// Handle Prescription Input and Add to Cart
function handlePrescriptionAndAddToCart() {
    selectedLensId = $('#lens_select').val();  // Get selected lens id
    prescriptionDetails = {
        powerLeft: $('#power_left').val(),
        powerRight: $('#power_right').val(),
        cycLeft: $('#cyc_left').val(),
        cycRight: $('#cyc_right').val()
    };

    // Save data to Cart (in a session or database)
    $.ajax({
        url: 'add_to_cart.php',
        method: 'POST',
        data: {
            frame_id: selectedFrameId,
            lens_id: selectedLensId,
            prescription: prescriptionDetails,
            quantity: 1
        },
        success: function(response) {
            console.log(response);
            alert("Added to cart successfully");
        },
        error: function() {
            console.log("Error adding to cart");
        }
    });
}

// If user chooses to just add the frame
function addFrameToCart() {
    $.ajax({
        url: 'add_to_cart.php',
        method: 'POST',
        data: {
            frame_id: selectedFrameId,
            lens_id: null,  // No lens selected
            prescription: null,  // No prescription
            quantity: 1
        },
        success: function(response) {
            console.log(response);
            alert("Frame added to cart");
        },
        error: function() {
            console.log("Error adding frame to cart");
        }
    });
}

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
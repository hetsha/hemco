<?php
include 'config/db.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

function getCategories($conn)
{
  $stmt = $conn->prepare("SELECT name FROM frame_category");
  $stmt->execute();
  $result = $stmt->get_result();
  $categories = [];
  while ($row = $result->fetch_assoc()) {
    $categories[] = $row['name'];
  }
  return $categories;
}

function getBrands($conn) {
  $brands = [];
  $result = $conn->query("SELECT brand_id, name FROM brand ORDER BY name ASC");
  while ($row = $result->fetch_assoc()) {
    $brands[] = $row;
  }
  return $brands;
}

$brands = getBrands($conn);

$action = $_POST['action'] ?? '';

// Common input fields
$id       = $_POST['id'] ?? null;
$name     = $_POST['name'] ?? '';
$category = $_POST['category'] ?? '';
$image    = '';
$description = $_POST['description'] ?? '';
$price    = $_POST['price'][0] ?? 0;
$material = $_POST['material'] ?? '';
$shape    = $_POST['shape'] ?? '';
$gender   = $_POST['gender'] ?? '';
$tag      = $_POST['tag'] ?? '';
$brand_id = $_POST['brand_id'] ?? null;

// Get category ID (allow any category from DB)
$category_id = null;
if ($category) {
  $stmt = $conn->prepare("SELECT category_id FROM frame_category WHERE name = ?");
  $stmt->bind_param("s", $category);
  $stmt->execute();
  $stmt->store_result();
  if ($stmt->num_rows > 0) {
    $stmt->bind_result($category_id);
    $stmt->fetch();
  } else {
    $insert = $conn->prepare("INSERT INTO frame_category (name) VALUES (?)");
    $insert->bind_param("s", $category);
    $insert->execute();
    $category_id = $insert->insert_id;
  }
}

if ($action == 'add') {
  // Handle image upload
  $image = '';
  if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filename = '../uploads/' . time() . '_' . uniqid() . '.' . $ext;
    move_uploaded_file($_FILES['image']['tmp_name'], $filename);
    $image = $filename;
  }
  $stmt = $conn->prepare("INSERT INTO frames (name, description, price, material, shape, gender, tag, brand_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssdssssi", $name, $description, $price, $material, $shape, $gender, $tag, $brand_id);
  $stmt->execute();
  $frame_id = $stmt->insert_id;
  // Category map
  if ($category_id) {
    // Remove any previous mapping for this frame (shouldn't exist for add, but safe)
    $stmt2 = $conn->prepare("DELETE FROM frame_category_map WHERE frame_id = ?");
    $stmt2->bind_param("i", $frame_id);
    $stmt2->execute();
    $stmt2 = $conn->prepare("INSERT INTO frame_category_map (frame_id, category_id) VALUES (?, ?)");
    $stmt2->bind_param("ii", $frame_id, $category_id);
    $stmt2->execute();
  }
  // Image
  if ($image) {
    $stmt3 = $conn->prepare("INSERT INTO frame_images (frame_id, image_url) VALUES (?, ?)");
    $stmt3->bind_param("is", $frame_id, $image);
    $stmt3->execute();
  }
  echo "Frame added";
  exit;
}

if ($action == 'edit' && $id) {
  // Check if a new image is uploaded
  if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filename = '../uploads/' . time() . '_' . uniqid() . '.' . $ext;
    move_uploaded_file($_FILES['image']['tmp_name'], $filename);
    $image = $filename;
    // Delete old image if exists
    $stmtOld = $conn->prepare("SELECT image_url FROM frame_images WHERE frame_id = ?");
    $stmtOld->bind_param("i", $id);
    $stmtOld->execute();
    $stmtOld->bind_result($old_image);
    $stmtOld->fetch();
    $stmtOld->close();
    if ($old_image && file_exists($old_image)) {
      unlink($old_image);
    }
    // Update with new image
    $stmt = $conn->prepare("UPDATE frame_images SET image_url = ? WHERE frame_id = ?");
    $stmt->bind_param("si", $image, $id);
    $stmt->execute();
  }
  // Update frame
  $stmt = $conn->prepare("UPDATE frames SET name = ?, description = ?, price = ?, material = ?, shape = ?, gender = ?, tag = ?, brand_id = ? WHERE frame_id = ?");
  if (!$stmt) {
    echo "Error preparing UPDATE frames: " . $conn->error;
    exit;
  }
  if (!$stmt->bind_param("ssdssssii", $name, $description, $price, $material, $shape, $gender, $tag, $brand_id, $id)) {
    echo "Error binding UPDATE frames: " . $stmt->error;
    exit;
  }
  if (!$stmt->execute()) {
    echo "Error executing UPDATE frames: " . $stmt->error;
    exit;
  }
  // Update category map
  if ($category_id) {
    $del = $conn->prepare("DELETE FROM frame_category_map WHERE frame_id = ?");
    if (!$del) {
      echo "Error preparing DELETE frame_category_map: " . $conn->error;
      exit;
    }
    if (!$del->bind_param("i", $id)) {
      echo "Error binding DELETE frame_category_map: " . $del->error;
      exit;
    }
    if (!$del->execute()) {
      echo "Error executing DELETE frame_category_map: " . $del->error;
      exit;
    }
    $stmt2 = $conn->prepare("INSERT INTO frame_category_map (frame_id, category_id) VALUES (?, ?)");
    if (!$stmt2) {
      echo "Error preparing INSERT frame_category_map: " . $conn->error;
      exit;
    }
    if (!$stmt2->bind_param("ii", $id, $category_id)) {
      echo "Error binding INSERT frame_category_map: " . $stmt2->error;
      exit;
    }
    if (!$stmt2->execute()) {
      echo "Error executing INSERT frame_category_map: " . $stmt2->error;
      exit;
    }
  }
  echo "Frame updated";
  exit;
}

if ($action == 'delete' && $id) {
  // Get image path
  $stmt = $conn->prepare("SELECT image_url FROM frame_images WHERE frame_id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->bind_result($image_path);
  $stmt->fetch();
  $stmt->close();
  if ($image_path && file_exists($image_path)) {
    unlink($image_path);
  }
  $delStmt = $conn->prepare("DELETE FROM frame_images WHERE frame_id = ?");
  $delStmt->bind_param("i", $id);
  $delStmt->execute();
  $delStmt = $conn->prepare("DELETE FROM frame_category_map WHERE frame_id = ?");
  $delStmt->bind_param("i", $id);
  $delStmt->execute();
  $delStmt = $conn->prepare("DELETE FROM frames WHERE frame_id = ?");
  $delStmt->bind_param("i", $id);
  $delStmt->execute();
  echo "Frame deleted";
  exit;
}

if ($action == 'get_product' && $id) {
  // Get main frame
  $stmt = $conn->prepare("SELECT f.*, fc.name AS category FROM frames f LEFT JOIN frame_category_map fcm ON f.frame_id = fcm.frame_id LEFT JOIN frame_category fc ON fcm.category_id = fc.category_id WHERE f.frame_id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $frame = $stmt->get_result()->fetch_assoc();
  // Get images
  $images = [];
  $stmt2 = $conn->prepare("SELECT image_url FROM frame_images WHERE frame_id = ?");
  $stmt2->bind_param("i", $id);
  $stmt2->execute();
  $res2 = $stmt2->get_result();
  while ($w = $res2->fetch_assoc()) $images[] = $w['image_url'];
  $frame['images'] = $images;
  // Get details
  $details = [];
  $stmt3 = $conn->prepare("SELECT * FROM frame_details WHERE frame_id = ?");
  $stmt3->bind_param("i", $id);
  $stmt3->execute();
  $res3 = $stmt3->get_result();
  while ($d = $res3->fetch_assoc()) $details[] = $d;
  $frame['details'] = $details;
  header('Content-Type: application/json');
  echo json_encode($frame);
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin | Products</title>
  <?php include 'include/header.php'; ?>
</head>

<body class="bg-blue-50 text-gray-800 dark:bg-gray-900 dark:text-gray-100">
  <div class="flex h-screen overflow-hidden">
    <?php include 'include/navbar.php'; ?>
    <div class="flex-1 flex flex-col">
      <header class="flex justify-between items-center bg-white dark:bg-gray-800 px-4 py-3 shadow-md">
        <button id="menu-btn" class="md:hidden text-blue-500 dark:text-blue-300">
          <i data-lucide="menu"></i>
        </button>
        <input type="text" id="searchInput" placeholder="Search Products..." class="px-3 py-1 rounded bg-blue-100 dark:bg-gray-700 w-1/2">
        <div class="flex items-center space-x-4">
          <button class="text-blue-500 dark:text-blue-300"><i data-lucide="bell"></i></button>
          <div class="w-8 h-8 bg-blue-300 dark:bg-gray-600 rounded-full flex items-center justify-center font-bold">S</div>
          <button id="theme-toggle" class="text-blue-500 dark:text-blue-300" title="Toggle Theme">
            <i data-lucide="sun"></i>
          </button>
        </div>
      </header>

      <main class="p-4 overflow-auto grid grid-cols-1 gap-4">

        <div class="flex justify-between items-center mb-4 ">
          <h2 class="text-2xl font-semibold">Products</h2>
          <button id="addProductBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Add Product</button>
        </div>
        <div class="mb-4 flex items-center gap-2">
          <label for="filterCategory" class="font-semibold">Filter by Category:</label>
          <select id="filterCategory" class="p-2 rounded bg-blue-100 dark:bg-gray-700">
            <option value="">All</option>
            <?php
              $categories = getCategories($conn);
              foreach ($categories as $cat) {
                echo "<option value=\"$cat\">$cat</option>";
              }
            ?>
          </select>
        </div>
        <div id="productTable" class="bg-white dark:bg-gray-800 p-4 rounded shadow overflow-x-auto">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead>
                <tr class="text-gray-500 dark:text-gray-300">
                  <th class="p-2">Image</th>
                  <th class="p-2">Name</th>
                  <th class="p-2">Category</th>
                  <th class="p-2">Price</th>
                  <th class="p-2">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $sql = "SELECT f.frame_id, f.name, f.description, f.price, f.material, f.shape, f.gender, f.tag, f.brand_id, GROUP_CONCAT(fc.name SEPARATOR ', ') AS category, fi.image_url
                        FROM frames f
                        LEFT JOIN frame_category_map fcm ON f.frame_id = fcm.frame_id
                        LEFT JOIN frame_category fc ON fcm.category_id = fc.category_id
                        LEFT JOIN frame_images fi ON f.frame_id = fi.frame_id
                        GROUP BY f.frame_id";
                $result = $conn->query($sql);
                while ($product = $result->fetch_assoc()) {
                  echo '<tr class="border-t border-blue-100 dark:border-gray-700 product-row" data-id="' . $product['frame_id'] . '">';
                  echo '<td class="p-2"><img src="' . ($product['image_url'] ? $product['image_url'] : '../img/sample-snack.jpg') . '" class="w-24 h-auto rounded" alt="Product"></td>';
                  echo '<td class="p-2 product-name">' . htmlspecialchars($product['name']) . '</td>';
                  echo '<td class="p-2 product-category">' . htmlspecialchars($product['category']) . '</td>';
                  echo '<td class="p-2 product-price">' . $product['price'] . '</td>';
                  echo '<td class="p-2 space-x-2">';
                  echo '<button class="text-blue-600 hover:underline">Edit</button>';
                  echo '<button class="text-red-600 hover:underline">Delete</button>';
                  echo '</td>';
                  echo '</tr>';
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>
  <!-- Product Modal -->
  <div id="productModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-lg relative max-h-[90vh] overflow-y-auto">
      <h2 class="text-xl font-semibold mb-4" id="modalTitle">Add Product</h2>
      <form id="productForm" enctype="multipart/form-data">
        <input type="hidden" name="id" id="productId">
        <input type="hidden" name="action" id="formAction" value="add">

        <div class="mb-3">
          <label>Name</label>
          <input type="text" name="name" id="productName" required class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700">
        </div>

        <div class="mb-3">
          <label>Category</label>
          <select name="category" id="productCategory" required class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700">
            <option value="">Select Category</option>
            <?php
              $categories = getCategories($conn);
              foreach ($categories as $cat) {
                echo "<option value=\"$cat\">$cat</option>";
              }
            ?>
          </select>
        </div>

        <div class="mb-3">
          <label>Product Image</label>
          <input type="file" name="image" id="productImage" accept="image/*" class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700">
        </div>

        <div class="mb-3">
          <label>Price</label>
          <input type="number" name="price[]" placeholder="Price" required class="p-2 rounded bg-blue-100 dark:bg-gray-700 w-full">
        </div>

        <div class="mb-3">
          <label>Material</label>
          <input type="text" name="material" id="productMaterial" required class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700">
        </div>

        <div class="mb-3">
          <label>Shape</label>
          <input type="text" name="shape" id="productShape" required class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700">
        </div>

        <div class="mb-3">
          <label>Gender</label>
          <select name="gender" id="productGender" required class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700">
            <option value="">Select Gender</option>
            <option value="men">Men</option>
            <option value="women">Women</option>
            <option value="child">Child</option>
          </select>
        </div>

        <div class="mb-3">
          <label>Tag</label>
          <input type="text" name="tag" id="productTag" required class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700">
        </div>

        <div class="mb-3">
          <label>Brand</label>
          <select name="brand_id" id="productBrandId" required class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700">
            <option value="">Select Brand</option>
            <?php foreach($brands as $brand): ?>
              <option value="<?php echo $brand['brand_id']; ?>"><?php echo htmlspecialchars($brand['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="mb-3">
          <label>Description</label>
          <textarea name="description" id="productDescription" rows="3" class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700"></textarea>
        </div>

        <div class="flex justify-end gap-2">
          <button type="button" id="closeModal" class="bg-gray-300 dark:bg-gray-600 px-4 py-2 rounded">Cancel</button>
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
        </div>
      </form>
    </div>
  </div>
  <!-- Responsive Edit Product Modal -->
  <div id="editProductModal" enctype="multipart/form-data" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center px-2">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-lg relative sm:max-w-md sm:p-4 max-h-[90vh] overflow-y-auto">
      <h2 class="text-xl font-semibold mb-4">Edit Product</h2>
      <form id="editProductForm" enctype="multipart/form-data">
        <input type="hidden" name="id" id="editProductId">
        <input type="hidden" name="action" value="edit">

        <div class="mb-3">
          <label>Name</label>
          <input type="text" name="name" id="editProductName" required class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700">
        </div>

        <div class="mb-3">
          <label>Category</label>
          <select name="category" id="editProductCategory" required class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700">
            <option value="">Select Category</option>
            <?php
              $categories = getCategories($conn);
              foreach ($categories as $cat) {
                echo "<option value=\"$cat\" >$cat</option>";
              }
            ?>
          </select>
        </div>

        <div class="mb-3">
          <label>Description</label>
          <textarea name="description" id="editProductDescription" rows="3" class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700"></textarea>
        </div>

        <div class="mb-3">
          <label>Price</label>
          <input type="number" name="price[]" placeholder="Price" required class="p-2 rounded bg-blue-100 dark:bg-gray-700 w-full">
        </div>

        <div class="mb-3">
          <label>Material</label>
          <input type="text" name="material" id="editProductMaterial" required class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700">
        </div>

        <div class="mb-3">
          <label>Shape</label>
          <input type="text" name="shape" id="editProductShape" required class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700">
        </div>

        <div class="mb-3">
          <label>Gender</label>
          <select name="gender" id="editProductGender" required class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700">
            <option value="">Select Gender</option>
            <option value="men">Men</option>
            <option value="women">Women</option>
            <option value="child">Child</option>
          </select>
        </div>

        <div class="mb-3">
          <label>Tag</label>
          <input type="text" name="tag" id="editProductTag" required class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700">
        </div>

        <div class="mb-3">
          <label>Brand</label>
          <select name="brand_id" id="editProductBrandId" required class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700">
            <option value="">Select Brand</option>
            <?php foreach($brands as $brand): ?>
              <option value="<?php echo $brand['brand_id']; ?>"><?php echo htmlspecialchars($brand['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="mb-3">
          <label>Product Image</label>
          <input type="file" name="image" id="editProductImage" accept="image/*" class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700">
          <div id="editProductImagePreview" class="mt-2"></div>
        </div>

        <div class="flex flex-wrap justify-end gap-2">
          <button type="button" id="closeEditModal" class="bg-gray-300 dark:bg-gray-600 px-4 py-2 rounded w-28 sm:w-full">Cancel</button>
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-28 sm:w-full">Update</button>
        </div>
      </form>
    </div>
  </div>

  <?php include 'include/footer.php'; ?>
  <script>
    const modal = document.getElementById('productModal');
    const form = document.getElementById('productForm');

    document.getElementById('addProductBtn').addEventListener('click', () => {
      form.reset();
      document.getElementById('productModal').classList.remove('hidden');
    });

    document.getElementById('closeModal').addEventListener('click', () => {
      document.getElementById('productModal').classList.add('hidden');
      form.reset();
    });

    // Open modal in Add or Edit mode
    function openModal(mode, product = {}) {
      document.getElementById('modalTitle').textContent = mode === 'edit' ? 'Edit Product' : 'Add Product';
      document.getElementById('formAction').value = mode;
      if (mode === 'edit') {
        document.getElementById('productId').value = product.frame_id;
        document.getElementById('productName').value = product.name;
        document.getElementById('productCategory').value = product.category;
        document.getElementById('productMaterial').value = product.material;
        document.getElementById('productShape').value = product.shape;
        document.getElementById('productGender').value = product.gender;
        document.getElementById('productTag').value = product.tag;
        document.getElementById('productBrandId').value = product.brand_id;
        document.getElementById('productDescription').value = product.description;
        let priceInput = document.querySelector('#productForm input[name="price[]"]');
        if (priceInput) priceInput.value = product.price;
      }
      document.getElementById('productModal').classList.remove('hidden');
    }

    // Handle Edit & Delete buttons
    document.addEventListener('click', async e => {
      if (e.target.textContent === 'Edit') {
        const row = e.target.closest('.product-row');
        const productId = row.dataset.id;

        // Fetch full product data including weights
        const response = await fetch('', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({ action: 'get_product', id: productId })
        });
        const product = await response.json();

        // product should have: id, name, category, description, is_in_stock, image, weights (array)
        openModal('edit', product);
      }

      if (e.target.textContent === 'Delete') {
        const row = e.target.closest('.product-row');
        const id = row.dataset.id;
        if (confirm('Are you sure you want to delete this product?')) {
          fetch('', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
              action: 'delete',
              id
            })
          }).then(() => location.reload());
        }
      }
    });
    // Submit form
    form.addEventListener('submit', e => {
      e.preventDefault();
      const formData = new FormData(form);
      fetch('', {
          method: 'POST',
          body: formData // <-- send FormData directly!
        })
        .then(() => {
          modal.classList.add('hidden');
          form.reset();
          location.reload();
        });
    });
  </script>
  <script>
    // Edit modal logic
    function openEditModal(product) {
      document.getElementById('editProductId').value = product.frame_id;
      document.getElementById('editProductName').value = product.name;
      // Set the selected category
      const catSelect = document.getElementById('editProductCategory');
      for (let i = 0; i < catSelect.options.length; i++) {
        if (catSelect.options[i].value === product.category) {
          catSelect.selectedIndex = i;
          break;
        }
      }
      document.getElementById('editProductDescription').value = product.description || '';
      document.getElementById('editProductPrice').value = product.price;
      document.getElementById('editProductMaterial').value = product.material;
      document.getElementById('editProductShape').value = product.shape;
      document.getElementById('editProductGender').value = product.gender;
      document.getElementById('editProductTag').value = product.tag;
      document.getElementById('editProductBrandId').value = product.brand_id;
      document.getElementById('editProductImagePreview').innerHTML = product.images && product.images.length ? `<img src="${product.images[0]}" class="w-24 rounded shadow">` : '';
      document.getElementById('editProductModal').classList.remove('hidden');
    }

    document.getElementById('closeEditModal').onclick = () => document.getElementById('editProductModal').classList.add('hidden');

    // Submit handler (AJAX or form submit as needed)
    document.getElementById('editProductForm').onsubmit = function(e) {
      e.preventDefault();
      const form = document.getElementById('editProductForm');
      const formData = new FormData(form);
      fetch('', {
        method: 'POST',
        body: formData
      })
      .then(() => {
        document.getElementById('editProductModal').classList.add('hidden');
        location.reload();
      });
    };
  </script>
  <script>
    // Utility to fill category selects in both modals
    function fillCategorySelect(selectId, selectedValue = '') {
      const addCatSelect = document.getElementById('productCategory');
      const editCatSelect = document.getElementById(selectId);
      editCatSelect.innerHTML = addCatSelect.innerHTML;
      if (selectedValue) {
        editCatSelect.value = selectedValue;
      }
    }
  </script>
</body>

</html>
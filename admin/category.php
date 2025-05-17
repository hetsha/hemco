<?php
include 'config/db.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// --- CATEGORY HELPERS ---
function loadFrameCategories($conn) {
    $query = "SELECT * FROM frame_category";
    $result = mysqli_query($conn, $query);
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    return $categories;
}
function loadLensCategories($conn) {
    $query = "SELECT * FROM lens_category";
    $result = mysqli_query($conn, $query);
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    return $categories;
}
function loadLenses($conn) {
    $query = "SELECT l.lens_id, l.type, l.price, l.description, lc.type AS category FROM lens l LEFT JOIN lens_category lc ON l.category_id = lc.category_id";
    $result = mysqli_query($conn, $query);
    $lenses = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $lenses[] = $row;
    }
    return $lenses;
}
function loadBrands($conn) {
    $query = "SELECT * FROM brand";
    $result = mysqli_query($conn, $query);
    $brands = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $brands[] = $row;
    }
    return $brands;
}

// --- ADD CATEGORY ---
if (isset($_POST['action']) && $_POST['action'] == 'add_frame_category' && !empty($_POST['name'])) {
    $category_name = mysqli_real_escape_string($conn, $_POST['name']);
    $query = "INSERT INTO frame_category (name) VALUES ('$category_name')";
    mysqli_query($conn, $query);
    header('Location: category.php'); exit();
}
if (isset($_POST['action']) && $_POST['action'] == 'add_lens_category' && !empty($_POST['type'])) {
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $desc = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
    $query = "INSERT INTO lens_category (type, description) VALUES ('$type', '$desc')";
    mysqli_query($conn, $query);
    header('Location: category.php'); exit();
}
if (isset($_POST['action']) && $_POST['action'] == 'add_lens' && !empty($_POST['type']) && !empty($_POST['category_id']) && isset($_POST['price'])) {
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $category_id = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    $desc = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
    $query = "INSERT INTO lens (type, category_id, price, description) VALUES ('$type', $category_id, $price, '$desc')";
    mysqli_query($conn, $query);
    header('Location: category.php'); exit();
}
if (isset($_POST['action']) && $_POST['action'] == 'add_brand' && !empty($_POST['name'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $query = "INSERT INTO brand (name) VALUES ('$name')";
    mysqli_query($conn, $query);
    header('Location: category.php'); exit();
}

$frame_categories = loadFrameCategories($conn);
$lens_categories = loadLensCategories($conn);
$lenses = loadLenses($conn);
$brands = loadBrands($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin | Categories</title>
  <?php include 'include/header.php'; ?>
</head>

<body class="bg-blue-50 text-gray-800 dark:bg-gray-900 dark:text-gray-100">
  <div class="flex h-screen overflow-hidden">
    <?php include 'include/navbar.php'; ?>

    <div class="flex-1 flex flex-col">
      <!-- Top Bar -->
      <header class="flex justify-between items-center bg-white dark:bg-gray-800 px-4 py-3 shadow-md">
        <button id="menu-btn" class="md:hidden text-blue-500 dark:text-blue-300"><i data-lucide="menu"></i></button>
        <h2 class="text-xl font-semibold">Categories</h2>
        <div class="flex items-center space-x-4">
          <button class="text-blue-500 dark:text-blue-300"><i data-lucide="bell"></i></button>
          <div class="w-8 h-8 bg-blue-300 dark:bg-gray-600 rounded-full flex items-center justify-center font-bold">S</div>
          <button id="theme-toggle" class="text-blue-500 dark:text-blue-300" title="Toggle Theme"><i data-lucide="sun"></i></button>
        </div>
      </header>

      <!-- Main Content -->
      <main class="p-4 overflow-auto">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-2xl font-semibold">Category List</h2>
          <button id="openAddModal" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Add Category</button>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead>
                <tr class="text-gray-500 dark:text-gray-300">
                  <th class="p-2">#</th>
                  <th class="p-2">Category Name</th>
                  <th class="p-2">Action</th>
                </tr>
              </thead>
              <tbody id="categoryTable">
                <?php foreach ($frame_categories as $category): ?>
                  <tr class="border-t border-blue-100 dark:border-gray-700">
                    <td class="p-2"><?php echo $category['category_id']; ?></td>
                    <td class="p-2"><?php echo $category['name']; ?></td>
                    <td class="p-2 space-x-2">
                      <button onclick="openEditModal(<?php echo $category['category_id']; ?>, '<?php echo htmlspecialchars($category['name'], ENT_QUOTES); ?>')" class="text-blue-600 hover:underline">Edit</button>
                      <a href="delete_category.php?id=<?php echo $category['category_id']; ?>" class="text-red-600 hover:underline">Delete</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </main>

      <main class="p-4 overflow-auto">
        <div class="flex flex-wrap gap-8">
          <!-- Frame Categories Table -->
          <div class="flex-1 min-w-[350px]">
            <h2 class="text-xl font-semibold mb-2">Frame Categories</h2>
            <form method="POST" class="mb-2 flex gap-2">
              <input type="text" name="name" placeholder="Add Frame Category" class="p-2 rounded bg-blue-100 dark:bg-gray-700" required>
              <button type="submit" name="action" value="add_frame_category" class="bg-blue-600 text-white px-3 py-2 rounded">Add</button>
            </form>
            <div class="overflow-x-auto">
              <table class="w-full text-left text-sm">
                <thead><tr><th class="p-2">#</th><th class="p-2">Name</th></tr></thead>
                <tbody>
                  <?php foreach ($frame_categories as $cat): ?>
                    <tr><td class="p-2"><?php echo $cat['category_id']; ?></td><td class="p-2"><?php echo htmlspecialchars($cat['name']); ?></td></tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
          <!-- Lens Categories Table -->
          <div class="flex-1 min-w-[350px]">
            <h2 class="text-xl font-semibold mb-2">Lens Categories</h2>
            <form method="POST" class="mb-2 flex gap-2 flex-wrap">
              <input type="text" name="type" placeholder="Add Lens Category Type" class="p-2 rounded bg-blue-100 dark:bg-gray-700" required>
              <input type="text" name="description" placeholder="Description (optional)" class="p-2 rounded bg-blue-100 dark:bg-gray-700">
              <button type="submit" name="action" value="add_lens_category" class="bg-blue-600 text-white px-3 py-2 rounded">Add</button>
            </form>
            <div class="overflow-x-auto">
              <table class="w-full text-left text-sm">
                <thead><tr><th class="p-2">#</th><th class="p-2">Type</th><th class="p-2">Description</th></tr></thead>
                <tbody>
                  <?php foreach ($lens_categories as $cat): ?>
                    <tr><td class="p-2"><?php echo $cat['category_id']; ?></td><td class="p-2"><?php echo htmlspecialchars($cat['type']); ?></td><td class="p-2"><?php echo htmlspecialchars($cat['description']); ?></td></tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
          <!-- Lenses Table -->
          <div class="flex-1 min-w-[350px]">
            <h2 class="text-xl font-semibold mb-2">Lenses</h2>
            <form method="POST" class="mb-2 flex gap-2 flex-wrap">
              <input type="text" name="type" placeholder="Lens Type" class="p-2 rounded bg-blue-100 dark:bg-gray-700" required>
              <select name="category_id" class="p-2 rounded bg-blue-100 dark:bg-gray-700" required>
                <option value="">Select Lens Category</option>
                <?php foreach ($lens_categories as $cat): ?>
                  <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['type']); ?></option>
                <?php endforeach; ?>
              </select>
              <input type="number" name="price" step="0.01" placeholder="Price" class="p-2 rounded bg-blue-100 dark:bg-gray-700" required>
              <input type="text" name="description" placeholder="Description (optional)" class="p-2 rounded bg-blue-100 dark:bg-gray-700">
              <button type="submit" name="action" value="add_lens" class="bg-blue-600 text-white px-3 py-2 rounded">Add</button>
            </form>
            <div class="overflow-x-auto">
              <table class="w-full text-left text-sm">
                <thead><tr><th class="p-2">#</th><th class="p-2">Type</th><th class="p-2">Category</th><th class="p-2">Price</th><th class="p-2">Description</th></tr></thead>
                <tbody>
                  <?php foreach ($lenses as $lens): ?>
                    <tr><td class="p-2"><?php echo $lens['lens_id']; ?></td><td class="p-2"><?php echo htmlspecialchars($lens['type']); ?></td><td class="p-2"><?php echo htmlspecialchars($lens['category']); ?></td><td class="p-2">₹<?php echo $lens['price']; ?></td><td class="p-2"><?php echo htmlspecialchars($lens['description']); ?></td></tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
          <!-- Brands Table -->
          <div class="flex-1 min-w-[350px]">
            <h2 class="text-xl font-semibold mb-2">Brands</h2>
            <form method="POST" class="mb-2 flex gap-2">
              <input type="text" name="name" placeholder="Add Brand" class="p-2 rounded bg-blue-100 dark:bg-gray-700" required>
              <button type="submit" name="action" value="add_brand" class="bg-blue-600 text-white px-3 py-2 rounded">Add</button>
            </form>
            <div class="overflow-x-auto">
              <table class="w-full text-left text-sm">
                <thead><tr><th class="p-2">#</th><th class="p-2">Name</th></tr></thead>
                <tbody>
                  <?php foreach ($brands as $brand): ?>
                    <tr><td class="p-2"><?php echo $brand['brand_id']; ?></td><td class="p-2"><?php echo htmlspecialchars($brand['name']); ?></td></tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Add Category Modal -->
  <div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-gray-800 p-6 rounded shadow-lg w-full max-w-md">
      <h3 class="text-xl font-semibold mb-4">Add Category</h3>
      <form id="addCategoryForm" class="space-y-4" method="POST" action="category.php">
        <div>
          <label class="block text-sm">Category Name</label>
          <input type="text" id="addCategoryName" name="name" class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700" />
        </div>
        <div class="flex justify-end space-x-2 mt-4">
          <button type="button" id="cancelAdd" class="px-4 py-2 rounded bg-gray-300 dark:bg-gray-600">Cancel</button>
          <button type="submit" name="action" value="add_category" class="px-4 py-2 rounded bg-blue-600 text-white">Add</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Category Modal -->
  <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-gray-800 p-6 rounded shadow-lg w-full max-w-md">
      <h3 class="text-xl font-semibold mb-4">Edit Category</h3>
      <form id="editCategoryForm" class="space-y-4" method="POST" action="category.php">
        <div>
          <label class="block text-sm">Category Name</label>
          <input type="text" id="editCategoryName" name="name" class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700" />
        </div>
        <input type="hidden" id="editCategoryId" name="id" />
        <div class="flex justify-end space-x-2 mt-4">
          <button type="button" id="cancelEdit" class="px-4 py-2 rounded bg-gray-300 dark:bg-gray-600">Cancel</button>
          <button type="submit" name="action" value="edit_category" class="px-4 py-2 rounded bg-blue-600 text-white">Save</button>
        </div>
      </form>
    </div>
  </div>

  <?php include 'include/footer.php'; ?>
  <script>
    // Open Add Modal
    document.getElementById('openAddModal').addEventListener('click', () => {
      document.getElementById('addModal').classList.remove('hidden');
    });

    // Close Add Modal
    document.getElementById('cancelAdd').addEventListener('click', () => {
      document.getElementById('addModal').classList.add('hidden');
    });

    // Open Edit Modal
    function openEditModal(id, name) {
      document.getElementById('editCategoryId').value = id;
      document.getElementById('editCategoryName').value = name;
      document.getElementById('editModal').classList.remove('hidden');
    }

    // Close Edit Modal
    document.getElementById('cancelEdit').addEventListener('click', () => {
      document.getElementById('editModal').classList.add('hidden');
    });
  </script>
</body>
</html>

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

$frame_categories = loadFrameCategories($conn);

// Handle Add Category
if (isset($_POST['action']) && $_POST['action'] === 'add_category') {
    $name = trim($_POST['name'] ?? '');
    if ($name !== '') {
        $stmt = $conn->prepare("INSERT INTO frame_category (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
    }
    header('Location: category.php');
    exit;
}

// Handle Edit Category
if (isset($_POST['action']) && $_POST['action'] === 'edit_category') {
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    if ($id > 0 && $name !== '') {
        $stmt = $conn->prepare("UPDATE frame_category SET name = ? WHERE category_id = ?");
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
    }
    header('Location: category.php');
    exit;
}

// Handle Delete Category
if (isset($_POST['action']) && $_POST['action'] === 'delete_category') {
    $id = intval($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM frame_category WHERE category_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    header('Location: category.php');
    exit;
}
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
                      <form method="POST" action="category.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this category?');">
                        <input type="hidden" name="id" value="<?php echo $category['category_id']; ?>">
                        <button type="submit" name="action" value="delete_category" class="text-red-600 hover:underline bg-transparent border-0 p-0 m-0 cursor-pointer">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
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

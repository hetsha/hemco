<?php
// Admin page for managing lens categories
include 'config/db.php';
// session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

// Handle add/edit/delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_lenscat'])) {
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        if ($name) {
            $stmt = $conn->prepare('INSERT INTO lens_category (type, description) VALUES (?, ?)');
            $stmt->bind_param('ss', $name, $desc);
            $stmt->execute();
            $stmt->close();
        }
        header('Location: lenscat.php'); exit;
    }
    if (isset($_POST['edit_lenscat'])) {
        $id = intval($_POST['category_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        if ($id && $name) {
            $stmt = $conn->prepare('UPDATE lens_category SET type=?, description=? WHERE category_id=?');
            $stmt->bind_param('ssi', $name, $desc, $id);
            $stmt->execute();
            $stmt->close();
        }
        header('Location: lenscat.php'); exit;
    }
    if (isset($_POST['delete_lenscat'])) {
        $id = intval($_POST['category_id'] ?? 0);
        if ($id) {
            $stmt = $conn->prepare('DELETE FROM lens_category WHERE category_id=?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        }
        header('Location: lenscat.php'); exit;
    }
}

// Fetch lens categories for initial table load
$lensCats = [];
$sql = "SELECT * FROM lens_category ORDER BY category_id DESC";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    $lensCats[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin | Lens Categories</title>
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
        <h2 class="text-2xl font-semibold">Lens Categories</h2>
        <div class="flex items-center space-x-4">
          <button class="text-blue-500 dark:text-blue-300"><i data-lucide="bell"></i></button>
          <div class="w-8 h-8 bg-blue-300 dark:bg-gray-600 rounded-full flex items-center justify-center font-bold">S</div>
          <button id="theme-toggle" class="text-blue-500 dark:text-blue-300" title="Toggle Theme">
            <i data-lucide="sun"></i>
          </button>
        </div>
      </header>
      <main class="p-4 overflow-auto grid grid-cols-1 gap-4">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-semibold">Lens Category List</h2>
          <button id="openAddLensCatModal" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Add Lens Category</button>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead>
                <tr class="text-gray-500 dark:text-gray-300">
                  <th class="p-2">#</th>
                  <th class="p-2">Type</th>
                  <th class="p-2">Description</th>
                  <th class="p-2">Action</th>
                </tr>
              </thead>
              <tbody id="lensCatTableBody">
                <?php if (!empty($lensCats)): ?>
                  <?php foreach ($lensCats as $i => $cat): ?>
                    <tr class="border-t border-blue-100 dark:border-gray-700">
                      <td class="p-2"><?php echo $cat['category_id']; ?></td>
                      <td class="p-2"><?php echo htmlspecialchars($cat['type']); ?></td>
                      <td class="p-2"><?php echo htmlspecialchars($cat['description']); ?></td>
                      <td class="p-2 space-x-2">
                        <button onclick="openEditLensCatModal('<?php echo $cat['category_id']; ?>','<?php echo htmlspecialchars(addslashes($cat['type'])); ?>','<?php echo htmlspecialchars(addslashes($cat['description'])); ?>')" class="text-blue-600 hover:underline">Edit</button>
                        <form method="post" action="" style="display:inline;" onsubmit="return confirm('Delete this lens category?');">
                          <input type="hidden" name="category_id" value="<?php echo $cat['category_id']; ?>">
                          <button type="submit" name="delete_lenscat" class="text-red-600 hover:underline bg-transparent border-0 p-0 m-0 cursor-pointer">Delete</button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="4" class="p-2 text-center text-gray-400">No lens categories found.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Add Lens Category Modal -->
  <div id="addLensCatModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-gray-800 p-6 rounded shadow-lg w-full max-w-md">
      <h3 class="text-xl font-semibold mb-4">Add Lens Category</h3>
      <form id="addLensCatForm" class="space-y-4" method="post" action="">
        <div>
          <label class="block text-sm">Type</label>
          <input type="text" id="addLensCatName" name="name" class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700" required />
        </div>
        <div>
          <label class="block text-sm">Description</label>
          <textarea id="addLensCatDesc" name="description" class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700"></textarea>
        </div>
        <div class="flex justify-end space-x-2 mt-4">
          <button type="button" id="cancelAddLensCat" class="px-4 py-2 rounded bg-gray-300 dark:bg-gray-600">Cancel</button>
          <button type="submit" name="add_lenscat" class="px-4 py-2 rounded bg-blue-600 text-white">Add</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Lens Category Modal -->
  <div id="editLensCatModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-gray-800 p-6 rounded shadow-lg w-full max-w-md">
      <h3 class="text-xl font-semibold mb-4">Edit Lens Category</h3>
      <form id="editLensCatForm" class="space-y-4" method="post" action="">
        <div>
          <label class="block text-sm">Type</label>
          <input type="text" id="editLensCatName" name="name" class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700" required />
        </div>
        <div>
          <label class="block text-sm">Description</label>
          <textarea id="editLensCatDesc" name="description" class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700"></textarea>
        </div>
        <input type="hidden" id="editLensCatId" name="category_id" />
        <div class="flex justify-end space-x-2 mt-4">
          <button type="button" id="cancelEditLensCat" class="px-4 py-2 rounded bg-gray-300 dark:bg-gray-600">Cancel</button>
          <button type="submit" name="edit_lenscat" class="px-4 py-2 rounded bg-blue-600 text-white">Save</button>
        </div>
      </form>
    </div>
  </div>

  <?php include 'include/footer.php'; ?>
  <script>
    // Modal logic
    document.getElementById('openAddLensCatModal').onclick = () => {
      document.getElementById('addLensCatModal').classList.remove('hidden');
    };
    document.getElementById('cancelAddLensCat').onclick = () => {
      document.getElementById('addLensCatModal').classList.add('hidden');
    };
    document.getElementById('cancelEditLensCat').onclick = () => {
      document.getElementById('editLensCatModal').classList.add('hidden');
    };
    window.openEditLensCatModal = function(id, name, desc) {
      document.getElementById('editLensCatId').value = id;
      document.getElementById('editLensCatName').value = name;
      document.getElementById('editLensCatDesc').value = desc;
      document.getElementById('editLensCatModal').classList.remove('hidden');
    };
  </script>
</body>
</html>

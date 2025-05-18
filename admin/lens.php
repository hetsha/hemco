<?php
// Admin page for managing lenses
include 'config/db.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

// Fetch categories for dropdown
$categories = [];
$catResult = mysqli_query($conn, "SELECT * FROM lens_category ORDER BY type ASC");
if ($catResult && mysqli_num_rows($catResult) > 0) {
  while ($row = mysqli_fetch_assoc($catResult)) {
    $categories[] = $row;
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_lens'])) {
        $category_id = intval($_POST['category_id'] ?? 0);
        $type = trim($_POST['type'] ?? '');
        $price = trim($_POST['price'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        if ($type && $price !== '') {
            $stmt = $conn->prepare('INSERT INTO lens (category_id, type, price, description) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('isds', $category_id, $type, $price, $desc);
            $stmt->execute();
            $stmt->close();
        }
        header('Location: lens.php'); exit;
    }
    if (isset($_POST['edit_lens'])) {
        $id = intval($_POST['lens_id'] ?? 0);
        $category_id = intval($_POST['category_id'] ?? 0);
        $type = trim($_POST['type'] ?? '');
        $price = trim($_POST['price'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        if ($id && $type && $price !== '') {
            $stmt = $conn->prepare('UPDATE lens SET category_id=?, type=?, price=?, description=? WHERE lens_id=?');
            $stmt->bind_param('isdsi', $category_id, $type, $price, $desc, $id);
            $stmt->execute();
            $stmt->close();
        }
        header('Location: lens.php'); exit;
    }
    if (isset($_POST['delete_lens'])) {
        $id = intval($_POST['lens_id'] ?? 0);
        if ($id) {
            $stmt = $conn->prepare('DELETE FROM lens WHERE lens_id=?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        }
        header('Location: lens.php'); exit;
    }
}

// Fetch lenses for initial table load
$lenses = [];
$sql = "SELECT l.*, c.type AS category_type FROM lens l LEFT JOIN lens_category c ON l.category_id = c.category_id ORDER BY l.lens_id DESC";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    $lenses[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin | Lenses</title>
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
        <h2 class="text-2xl font-semibold">Lenses</h2>
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
          <h2 class="text-xl font-semibold">Lens List</h2>
          <button id="openAddLensModal" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Add Lens</button>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead>
                <tr class="text-gray-500 dark:text-gray-300">
                  <th class="p-2">#</th>
                  <th class="p-2">Category</th>
                  <th class="p-2">Type</th>
                  <th class="p-2">Price</th>
                  <th class="p-2">Description</th>
                  <th class="p-2">Action</th>
                </tr>
              </thead>
              <tbody id="lensTableBody">
                <?php if (!empty($lenses)): ?>
                  <?php foreach ($lenses as $lens): ?>
                    <tr class="border-t border-blue-100 dark:border-gray-700">
                      <td class="p-2"><?php echo $lens['lens_id']; ?></td>
                      <td class="p-2"><?php echo htmlspecialchars($lens['category_type']); ?></td>
                      <td class="p-2"><?php echo htmlspecialchars($lens['type']); ?></td>
                      <td class="p-2">₹<?php echo htmlspecialchars($lens['price']); ?></td>
                      <td class="p-2"><?php echo htmlspecialchars($lens['description']); ?></td>
                      <td class="p-2 space-x-2">
                        <button onclick="openEditLensModal('<?php echo $lens['lens_id']; ?>','<?php echo $lens['category_id']; ?>','<?php echo htmlspecialchars(addslashes($lens['type'])); ?>','<?php echo htmlspecialchars(addslashes($lens['price'])); ?>','<?php echo htmlspecialchars(addslashes($lens['description'])); ?>')" class="text-blue-600 hover:underline">Edit</button>
                        <form method="post" action="" style="display:inline;" onsubmit="return confirm('Delete this lens?');">
                          <input type="hidden" name="lens_id" value="<?php echo $lens['lens_id']; ?>">
                          <button type="submit" name="delete_lens" class="text-red-600 hover:underline bg-transparent border-0 p-0 m-0 cursor-pointer">Delete</button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="6" class="p-2 text-center text-gray-400">No lenses found.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Add Lens Modal -->
  <div id="addLensModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-gray-800 p-6 rounded shadow-lg w-full max-w-md">
      <h3 class="text-xl font-semibold mb-4">Add Lens</h3>
      <form id="addLensForm" class="space-y-4" method="post" action="">
        <div>
          <label class="block text-sm">Category</label>
          <select id="addLensCategory" name="category_id" class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700" required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['type']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm">Type</label>
          <input type="text" id="addLensType" name="type" class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700" required />
        </div>
        <div>
          <label class="block text-sm">Price</label>
          <input type="number" step="0.01" id="addLensPrice" name="price" class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700" required />
        </div>
        <div>
          <label class="block text-sm">Description</label>
          <textarea id="addLensDesc" name="description" class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700"></textarea>
        </div>
        <div class="flex justify-end space-x-2 mt-4">
          <button type="button" id="cancelAddLens" class="px-4 py-2 rounded bg-gray-300 dark:bg-gray-600">Cancel</button>
          <button type="submit" name="add_lens" class="px-4 py-2 rounded bg-blue-600 text-white">Add</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Lens Modal -->
  <div id="editLensModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-gray-800 p-6 rounded shadow-lg w-full max-w-md">
      <h3 class="text-xl font-semibold mb-4">Edit Lens</h3>
      <form id="editLensForm" class="space-y-4" method="post" action="">
        <div>
          <label class="block text-sm">Category</label>
          <select id="editLensCategory" name="category_id" class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700" required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['type']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm">Type</label>
          <input type="text" id="editLensType" name="type" class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700" required />
        </div>
        <div>
          <label class="block text-sm">Price</label>
          <input type="number" step="0.01" id="editLensPrice" name="price" class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700" required />
        </div>
        <div>
          <label class="block text-sm">Description</label>
          <textarea id="editLensDesc" name="description" class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700"></textarea>
        </div>
        <input type="hidden" id="editLensId" name="lens_id" />
        <div class="flex justify-end space-x-2 mt-4">
          <button type="button" id="cancelEditLens" class="px-4 py-2 rounded bg-gray-300 dark:bg-gray-600">Cancel</button>
          <button type="submit" name="edit_lens" class="px-4 py-2 rounded bg-blue-600 text-white">Save</button>
        </div>
      </form>
    </div>
  </div>

  <?php include 'include/footer.php'; ?>
  <script>
    document.getElementById('openAddLensModal').onclick = () => {
      document.getElementById('addLensModal').classList.remove('hidden');
    };
    document.getElementById('cancelAddLens').onclick = () => {
      document.getElementById('addLensModal').classList.add('hidden');
    };
    document.getElementById('cancelEditLens').onclick = () => {
      document.getElementById('editLensModal').classList.add('hidden');
    };
    window.openEditLensModal = function(id, category_id, type, price, desc) {
      document.getElementById('editLensId').value = id;
      document.getElementById('editLensCategory').value = category_id;
      document.getElementById('editLensType').value = type;
      document.getElementById('editLensPrice').value = price;
      document.getElementById('editLensDesc').value = desc;
      document.getElementById('editLensModal').classList.remove('hidden');
    };
  </script>
</body>
</html>

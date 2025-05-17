<?php
// Admin page for managing lenses
include 'config/db.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
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
                  <th class="p-2">Lens Name</th>
                  <th class="p-2">Action</th>
                </tr>
              </thead>
              <tbody id="lensTableBody">
                <!-- Lens rows will be loaded by JS -->
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
      <form id="addLensForm" class="space-y-4">
        <div>
          <label class="block text-sm">Lens Name</label>
          <input type="text" id="addLensName" name="name" class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700" required />
        </div>
        <div class="flex justify-end space-x-2 mt-4">
          <button type="button" id="cancelAddLens" class="px-4 py-2 rounded bg-gray-300 dark:bg-gray-600">Cancel</button>
          <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white">Add</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Lens Modal -->
  <div id="editLensModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-gray-800 p-6 rounded shadow-lg w-full max-w-md">
      <h3 class="text-xl font-semibold mb-4">Edit Lens</h3>
      <form id="editLensForm" class="space-y-4">
        <div>
          <label class="block text-sm">Lens Name</label>
          <input type="text" id="editLensName" name="name" class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700" required />
        </div>
        <input type="hidden" id="editLensId" name="id" />
        <div class="flex justify-end space-x-2 mt-4">
          <button type="button" id="cancelEditLens" class="px-4 py-2 rounded bg-gray-300 dark:bg-gray-600">Cancel</button>
          <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white">Save</button>
        </div>
      </form>
    </div>
  </div>

  <?php include 'include/footer.php'; ?>
  <script src="lens.js"></script>
  <script>
    // Modal logic
    document.getElementById('openAddLensModal').onclick = () => {
      document.getElementById('addLensModal').classList.remove('hidden');
    };
    document.getElementById('cancelAddLens').onclick = () => {
      document.getElementById('addLensModal').classList.add('hidden');
    };
    document.getElementById('cancelEditLens').onclick = () => {
      document.getElementById('editLensModal').classList.add('hidden');
    };
    // Add Lens
    document.getElementById('addLensForm').onsubmit = function(e) {
      e.preventDefault();
      const name = document.getElementById('addLensName').value.trim();
      if (!name) return;
      fetch('lens_actions.php', { method: 'POST', body: new URLSearchParams({action:'add', name}) })
        .then(() => {
          document.getElementById('addLensModal').classList.add('hidden');
          loadLensesTable();
        });
    };
    // Edit Lens
    document.getElementById('editLensForm').onsubmit = function(e) {
      e.preventDefault();
      const id = document.getElementById('editLensId').value;
      const name = document.getElementById('editLensName').value.trim();
      if (!id || !name) return;
      fetch('lens_actions.php', { method: 'POST', body: new URLSearchParams({action:'edit', id, name}) })
        .then(() => {
          document.getElementById('editLensModal').classList.add('hidden');
          loadLensesTable();
        });
    };
    // Load Lenses Table
    function loadLensesTable() {
      fetch('lens_actions.php', { method: 'POST', body: new URLSearchParams({action:'get'}) })
        .then(r => r.json())
        .then(data => {
          const tbody = document.getElementById('lensTableBody');
          tbody.innerHTML = data.map(lens =>
            `<tr class="border-t border-blue-100 dark:border-gray-700">
              <td class="p-2">${lens.id || lens.lens_id}</td>
              <td class="p-2">${lens.name || lens.type}</td>
              <td class="p-2 space-x-2">
                <button onclick="openEditLensModal('${lens.id || lens.lens_id}','${(lens.name || lens.type).replace(/'/g, "&#39;")}")" class="text-blue-600 hover:underline">Edit</button>
                <button onclick="deleteLens('${lens.id || lens.lens_id}')" class="text-red-600 hover:underline">Delete</button>
              </td>
            </tr>`
          ).join('');
        });
    }
    // Open Edit Modal
    window.openEditLensModal = function(id, name) {
      document.getElementById('editLensId').value = id;
      document.getElementById('editLensName').value = name;
      document.getElementById('editLensModal').classList.remove('hidden');
    };
    // Delete Lens
    window.deleteLens = function(id) {
      if (confirm('Delete this lens?')) {
        fetch('lens_actions.php', { method: 'POST', body: new URLSearchParams({action:'delete', id}) })
          .then(() => loadLensesTable());
      }
    };
    // Initial load
    loadLensesTable();
  </script>
</body>
</html>

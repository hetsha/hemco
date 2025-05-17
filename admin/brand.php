<?php
// Admin page for managing brands
include 'config/db.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin | Brands</title>
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
        <h2 class="text-2xl font-semibold">Brands</h2>
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
          <h2 class="text-xl font-semibold">Brand List</h2>
          <button id="openAddBrandModal" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Add Brand</button>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead>
                <tr class="text-gray-500 dark:text-gray-300">
                  <th class="p-2">#</th>
                  <th class="p-2">Brand Name</th>
                  <th class="p-2">Action</th>
                </tr>
              </thead>
              <tbody id="brandTableBody">
                <!-- Brand rows will be loaded by JS -->
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Add Brand Modal -->
  <div id="addBrandModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-gray-800 p-6 rounded shadow-lg w-full max-w-md">
      <h3 class="text-xl font-semibold mb-4">Add Brand</h3>
      <form id="addBrandForm" class="space-y-4">
        <div>
          <label class="block text-sm">Brand Name</label>
          <input type="text" id="addBrandName" name="name" class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700" required />
        </div>
        <div class="flex justify-end space-x-2 mt-4">
          <button type="button" id="cancelAddBrand" class="px-4 py-2 rounded bg-gray-300 dark:bg-gray-600">Cancel</button>
          <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white">Add</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Brand Modal -->
  <div id="editBrandModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-gray-800 p-6 rounded shadow-lg w-full max-w-md">
      <h3 class="text-xl font-semibold mb-4">Edit Brand</h3>
      <form id="editBrandForm" class="space-y-4">
        <div>
          <label class="block text-sm">Brand Name</label>
          <input type="text" id="editBrandName" name="name" class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700" required />
        </div>
        <input type="hidden" id="editBrandId" name="id" />
        <div class="flex justify-end space-x-2 mt-4">
          <button type="button" id="cancelEditBrand" class="px-4 py-2 rounded bg-gray-300 dark:bg-gray-600">Cancel</button>
          <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white">Save</button>
        </div>
      </form>
    </div>
  </div>

  <?php include 'include/footer.php'; ?>
  <script>
    // Modal logic
    document.getElementById('openAddBrandModal').onclick = () => {
      document.getElementById('addBrandModal').classList.remove('hidden');
    };
    document.getElementById('cancelAddBrand').onclick = () => {
      document.getElementById('addBrandModal').classList.add('hidden');
    };
    document.getElementById('cancelEditBrand').onclick = () => {
      document.getElementById('editBrandModal').classList.add('hidden');
    };
    // Add Brand
    document.getElementById('addBrandForm').onsubmit = function(e) {
      e.preventDefault();
      const name = document.getElementById('addBrandName').value.trim();
      if (!name) return;
      fetch('brand_actions.php', { method: 'POST', body: new URLSearchParams({action:'add', name}) })
        .then(() => {
          document.getElementById('addBrandModal').classList.add('hidden');
          loadBrandsTable();
        });
    };
    // Edit Brand
    document.getElementById('editBrandForm').onsubmit = function(e) {
      e.preventDefault();
      const id = document.getElementById('editBrandId').value;
      const name = document.getElementById('editBrandName').value.trim();
      if (!id || !name) return;
      fetch('brand_actions.php', { method: 'POST', body: new URLSearchParams({action:'edit', id, name}) })
        .then(() => {
          document.getElementById('editBrandModal').classList.add('hidden');
          loadBrandsTable();
        });
    };
    // Load Brands Table
    function loadBrandsTable() {
      fetch('brand_actions.php', { method: 'POST', body: new URLSearchParams({action:'get'}) })
        .then(r => r.json())
        .then(data => {
          const tbody = document.getElementById('brandTableBody');
          tbody.innerHTML = data.map(brand =>
            `<tr class="border-t border-blue-100 dark:border-gray-700">
              <td class="p-2">${brand.id || brand.brand_id}</td>
              <td class="p-2">${brand.name}</td>
              <td class="p-2 space-x-2">
                <button onclick="openEditBrandModal('${brand.id || brand.brand_id}','${brand.name.replace(/'/g, "&#39;")}")" class="text-blue-600 hover:underline">Edit</button>
                <button onclick="deleteBrand('${brand.id || brand.brand_id}')" class="text-red-600 hover:underline">Delete</button>
              </td>
            </tr>`
          ).join('');
        });
    }
    // Open Edit Modal
    window.openEditBrandModal = function(id, name) {
      document.getElementById('editBrandId').value = id;
      document.getElementById('editBrandName').value = name;
      document.getElementById('editBrandModal').classList.remove('hidden');
    };
    // Delete Brand
    window.deleteBrand = function(id) {
      if (confirm('Delete this brand?')) {
        fetch('brand_actions.php', { method: 'POST', body: new URLSearchParams({action:'delete', id}) })
          .then(() => loadBrandsTable());
      }
    };
    // Initial load
    loadBrandsTable();
  </script>
</body>
</html>

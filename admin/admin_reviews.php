<?php
include 'config/db.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Handle Add Review BEFORE output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
    // Sanitize and validate inputs
    $product_id = intval($_POST['product_id']);
    $rating = intval($_POST['rating']);
    $review = trim($_POST['review']);

    // Basic validation
    if ($product_id > 0 && $rating >= 1 && $rating <= 5 && !empty($review)) {
        $stmt = $conn->prepare("INSERT INTO product_reviews (product_id, user_id, rating, review, created_at) VALUES (?, NULL, ?, ?, NOW())");
        // Assuming admin adds the review, so user_id is NULL (or you can set a specific admin user_id)
        $stmt->bind_param("iis", $product_id, $rating, $review);
        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $error = "Failed to add review.";
        }
        $stmt->close();
    } else {
        $error = "Please fill all fields correctly.";
    }
}

// Handle deletion BEFORE output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  $id = intval($_POST['delete_id']);
  $conn->query("DELETE FROM product_reviews WHERE id = $id");
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin | Reviews</title>
  <?php include 'include/header.php'; ?>
</head>

<body class="bg-blue-50 text-gray-800 dark:bg-gray-900 dark:text-gray-100">
  <div class="flex h-screen overflow-hidden">
    <?php include 'include/navbar.php'; ?>
    <div class="flex-1 flex flex-col">
      <!-- Header -->
      <header class="flex justify-between items-center bg-white dark:bg-gray-800 px-4 py-3 shadow-md">
        <button id="menu-btn" class="md:hidden text-blue-500 dark:text-blue-300">
          <i data-lucide="menu"></i>
        </button>
        <input type="text" placeholder="Search Reviews..." class="px-3 py-1 rounded bg-blue-100 dark:bg-gray-700 w-1/2">
        <div class="flex items-center space-x-4">
          <button class="text-blue-500 dark:text-blue-300"><i data-lucide="bell"></i></button>
          <div class="w-8 h-8 bg-blue-300 dark:bg-gray-600 rounded-full flex items-center justify-center font-bold">S</div>
          <button id="theme-toggle" class="text-blue-500 dark:text-blue-300"><i data-lucide="sun"></i></button>
        </div>
      </header>

      <!-- Main -->
      <main class="p-4 overflow-auto grid grid-cols-1 gap-4">

<div class="flex justify-between items-center mb-4">
  <h2 class="text-2xl font-semibold">Product Reviews</h2>
  <button onclick="document.getElementById('addReviewModal').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
    + Add Review
  </button>
</div>

        <div id="reviewsTable" class="bg-white dark:bg-gray-800 p-4 rounded shadow overflow-x-auto">
          <?php
          // Connect and fetch reviews
          $result = $conn->query("SELECT r.*, p.name as product_name, u.username as user_name
                        FROM product_reviews r
                        JOIN products p ON r.product_id = p.id
                        LEFT JOIN users u ON r.user_id = u.id
                        ORDER BY r.created_at DESC");

            if ($result->num_rows > 0) {
            echo '<table class="w-full text-left text-sm">
                    <thead>
                      <tr class="text-gray-500 dark:text-gray-300">
                        <th class="p-2">Product</th>
                        <th class="p-2">User</th>
                        <th class="p-2">Rating</th>
                        <th class="p-2">Review</th>
                        <th class="p-2">Date</th>
                        <th class="p-2">Action</th>
                      </tr>
                    </thead>
                    <tbody>';
            while ($row = $result->fetch_assoc()) {
              echo "<tr class='border-t border-blue-100 dark:border-gray-700'>
                      <td class='p-2'>{$row['product_name']}</td>
                      <td class='p-2'>" . (!empty($row['user_name']) ? $row['user_name'] : 'Admin') . "</td>
                      <td class='p-2'>⭐ {$row['rating']}</td>
                      <td class='p-2'>{$row['review']}</td>
                      <td class='p-2'>" . date('d M Y', strtotime($row['created_at'])) . "</td>
                      <td class='p-2'>
                        <form method='POST' onsubmit='return confirm(\"Delete this review?\")'>
                          <input type='hidden' name='delete_id' value='{$row['id']}'>
                          <button type='submit' name='delete' class='text-red-600 hover:underline'>Delete</button>
                        </form>
                      </td>
                    </tr>";
            }
            echo '</tbody></table>';
          } else {
            echo '<p class="text-gray-500">No reviews found.</p>';
          }
          ?>
        </div>
      </main>
    </div>
  </div>
  <div id="addReviewModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden">
  <div class="bg-white dark:bg-gray-800 p-6 rounded shadow-lg w-full max-w-md">
    <h3 class="text-xl font-semibold mb-4">Add Review (Admin)</h3>
    <form method="POST">
      <div class="mb-4">
        <label class="block mb-1">Product</label>
        <select name="product_id" required class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700">
          <option value="">-- Select Product --</option>
          <?php
          $products = $conn->query("SELECT id, name FROM products ORDER BY name ASC");
          while ($p = $products->fetch_assoc()) {
            echo "<option value='{$p['id']}'>{$p['name']}</option>";
          }
          ?>
        </select>
      </div>
      <div class="mb-4">
        <label class="block mb-1">Rating (1-5)</label>
        <input type="number" name="rating" min="1" max="5" required class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700">
      </div>
      <div class="mb-4">
        <label class="block mb-1">Review</label>
        <textarea name="review" rows="3" required class="w-full p-2 rounded bg-blue-100 dark:bg-gray-700"></textarea>
      </div>
      <div class="flex justify-end gap-2">
        <button type="button" onclick="document.getElementById('addReviewModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded">Cancel</button>
        <button type="submit" name="add_review" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Submit</button>
      </div>
    </form>
  </div>
</div>

  <?php include 'include/footer.php'; ?>
</body>
</html>

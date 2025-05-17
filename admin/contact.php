<?php
include 'config/db.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch all contact inquiries
$result = $conn->query("SELECT * FROM contact ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Contact Inquiries</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'include/header.php'; ?>
</head>

<body class="bg-blue-50 text-gray-800 dark:bg-gray-900 dark:text-gray-100">
    <div class="flex h-screen overflow-hidden">
        <?php include 'include/navbar.php'; ?>

        <div class="flex-1 flex flex-col">
            <!-- Top Bar -->
            <header class="flex justify-between items-center bg-white dark:bg-gray-800 px-4 py-3 shadow-md">
        <div class="md:hidden">
          <button id="menu-btn" class="text-blue-500 dark:text-blue-300"><i data-lucide="menu"></i></button>
        </div>
        <input type="text" placeholder="Search Customers..." class="px-3 py-1 rounded bg-blue-100 dark:bg-gray-700 w-1/2">
        <div class="flex items-center space-x-4">
          <button class="text-blue-500 dark:text-blue-300"><i data-lucide="bell"></i></button>
          <div class="w-8 h-8 bg-blue-300 dark:bg-gray-600 rounded-full flex items-center justify-center font-bold">S</div>
          <button id="theme-toggle" class="text-blue-500 dark:text-blue-300" title="Toggle Theme">
            <i data-lucide="sun"></i>
          </button>
        </div>
      </header>
            <!-- Main Content -->
            <main class="p-4 overflow-auto grid grid-cols-1 gap-4">

                    <h2 class="text-2xl font-semibold mb-4">Contact Inquiries</h2>
                     <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
          <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm block md:table">
                        <thead>
                            <tr class="text-gray-500 dark:text-gray-300 border-b">
                                <th class="p-2">ID</th>
                                <th class="p-2">Name</th>
                                <th class="p-2">Email</th>
                                <th class="p-2">Phone</th>
                                <th class="p-2">Subject</th>
                                <th class="p-2">Message</th>
                                <th class="p-2">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="border-b border-blue-100 dark:border-gray-700">
                                    <td class="p-2"><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td class="p-2"><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td class="p-2"><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td class="p-2"><?php echo htmlspecialchars($row['phone']); ?></td>
                                    <td class="p-2"><?php echo htmlspecialchars($row['subject']); ?></td>
                                    <td class="p-2"><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
                                    <td class="p-2"><?php echo htmlspecialchars($row['created_at']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                            <?php if ($result->num_rows === 0): ?>
                                <tr>
                                    <td colspan="7" class="p-2 text-center text-gray-500">No inquiries found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
</div>
                </div>
            </main>
        </div>
    </div>
    <?php include 'include/footer.php'; ?>
</body>

</html>
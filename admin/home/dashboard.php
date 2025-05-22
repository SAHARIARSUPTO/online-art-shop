<?php
session_start();
include '../auth.php';  // session and auth check
include '../../db.php';  // PDO connection (adjust path if needed)

// Check for new orders
$stmt = $pdo->query("SELECT COUNT(*) as new_order_count FROM orders WHERE is_new = 1");
$newOrdersCount = $stmt->fetch()['new_order_count'] ?? 0;

$showOrderNotification = $newOrdersCount > 0;

// Mark new orders as read after fetching count so notification works
if ($showOrderNotification) {
    $pdo->query("UPDATE orders SET is_new = 0 WHERE is_new = 1");
}

$stmt = $pdo->query("SELECT 
    COUNT(*) as total_orders,
    SUM(CASE WHEN LOWER(status) = 'approved' THEN 1 ELSE 0 END) as approved_orders,
    SUM(CASE WHEN LOWER(status) = 'pending' THEN 1 ELSE 0 END) as pending_orders
FROM orders");
$orderStats = $stmt->fetch();


$stmt = $pdo->query("SELECT COUNT(*) as pending_testimonials FROM testimonials WHERE approved = 0");
$pendingTestimonials = $stmt->fetch()['pending_testimonials'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as news_count FROM news");
$newsCount = $stmt->fetch()['news_count'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as artworks_count FROM artworks");
$artworksCount = $stmt->fetch()['artworks_count'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as product_count FROM products");
$productCount = $stmt->fetch()['product_count'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

  <div class="p-4 sm:p-6 md:p-8 max-w-7xl mx-auto">

    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6">🎯 Admin Dashboard</h1>

    <?php if ($showOrderNotification): ?>
      <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md max-w-3xl mx-auto shadow-sm">
        <div class="flex items-center gap-2">
          <span class="text-2xl">🔔</span>
          <div>
            <p class="font-bold">New order(s) placed!</p>
            <p class="text-sm text-gray-700">Check the orders page for details.</p>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

      <!-- Orders -->
      <a href="orders.php" class="bg-white rounded-xl shadow-md p-5 hover:shadow-xl transition block">
        <div class="text-xl font-semibold flex items-center gap-2 mb-1">🛒 Total Orders</div>
        <div class="text-4xl font-bold text-green-600"><?= $orderStats['total_orders'] ?? 0 ?></div>
       
      </a>

      <!-- Testimonials -->
      <div class="bg-white rounded-xl shadow-md p-5 hover:shadow-xl transition">
        <div class="text-xl font-semibold flex items-center gap-2 mb-1">💬 Pending Testimonials</div>
        <div class="text-4xl font-bold text-yellow-500"><?= $pendingTestimonials ?></div>
      </div>

      <!-- News -->
      <div class="bg-white rounded-xl shadow-md p-5 hover:shadow-xl transition">
        <div class="text-xl font-semibold flex items-center gap-2 mb-1">📰 News Posts</div>
        <div class="text-4xl font-bold text-blue-600"><?= $newsCount ?></div>
      </div>

      <!-- Artworks -->
      <div class="bg-white rounded-xl shadow-md p-5 hover:shadow-xl transition">
        <div class="text-xl font-semibold flex items-center gap-2 mb-1">🎨 Artworks</div>
        <div class="text-4xl font-bold text-purple-600"><?= $artworksCount ?></div>
      </div>

      <!-- Products Management -->
      <a href="admin_products.php" class="bg-white rounded-xl shadow-md p-5 hover:shadow-xl transition block">
        <div class="text-xl font-semibold flex items-center gap-2 mb-1">🖼️ Products</div>
        <div class="text-4xl font-bold text-pink-600"><?= $productCount ?></div>
        <div class="mt-2 text-sm text-gray-600">Manage &amp; edit products</div>
      </a>

    </div>

    <div class="mt-10 text-gray-500 text-center sm:text-left">
      👋 Welcome back, Admin! Use the menu to manage your content.
    </div>
  </div>

</body>
</html>

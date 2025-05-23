<?php
session_start();
require 'db.php';

$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo "<p>Product not found.</p>";
    exit;
}

$review_stmt = $pdo->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY created_at DESC");
$review_stmt->execute([$product_id]);
$reviews = $review_stmt->fetchAll();

$avg_stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE product_id = ?");
$avg_stmt->execute([$product_id]);
$avg_rating = round($avg_stmt->fetchColumn(), 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($product['name']) ?> - Online Art Store</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#22c55e',
          },
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          }
        }
      }
    };
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans text-gray-800">

<header class="bg-white shadow sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <a href="index.php" class="text-2xl font-bold text-primary">🎨 Online Art Store</a>
    <nav class="space-x-6 text-sm font-medium">
      <a href="index.php" class="text-gray-700 hover:text-primary transition">Home</a>
      <a href="shop.php" class="text-gray-700 hover:text-primary transition">Shop</a>
      <a href="cart.php" class="inline-block bg-primary text-white px-4 py-2 rounded hover:bg-green-600">🛒 Cart</a>
    </nav>
  </div>
</header>

<main class="max-w-6xl mx-auto px-4 md:px-6 py-10 grid grid-cols-1 lg:grid-cols-2 gap-10">
  <div>
    <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-[400px] object-cover rounded-lg shadow">
  </div>
  <div class="space-y-6">
    <h1 class="text-3xl font-bold text-gray-900"><?= htmlspecialchars($product['name']) ?></h1>
    <p class="text-gray-600 text-lg leading-relaxed"><?= htmlspecialchars($product['description']) ?></p>
    <p class="text-2xl font-bold text-green-600">$<?= number_format($product['price'], 2) ?></p>
    <p class="text-sm text-gray-500">Average Rating: <span class="text-yellow-500 font-medium">⭐ <?= $avg_rating ?>/5</span></p>

    <form action="add_to_cart.php" method="GET" class="mt-4">
      <input type="hidden" name="id" value="<?= $product['id'] ?>">
      <button type="submit" class="bg-primary text-white px-5 py-3 rounded-md font-semibold hover:bg-green-700 transition">➕ Add to Cart</button>
    </form>

    <section class="mt-10">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Leave a Review</h2>
      <form action="submit_review.php" method="POST" class="space-y-4">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
        <div>
          <label for="user_name" class="block text-sm font-medium text-gray-700">Your Name</label>
          <input type="text" name="user_name" id="user_name" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2">
        </div>
        <div>
          <label for="rating" class="block text-sm font-medium text-gray-700">Rating</label>
          <select name="rating" id="rating" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2">
            <option value="">Select Rating</option>
            <option value="5">5 - Excellent</option>
            <option value="4">4 - Very Good</option>
            <option value="3">3 - Good</option>
            <option value="2">2 - Fair</option>
            <option value="1">1 - Poor</option>
          </select>
        </div>
        <div>
          <label for="review" class="block text-sm font-medium text-gray-700">Review</label>
          <textarea name="review" id="review" rows="4" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2"></textarea>
        </div>
        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-green-700">Submit Review</button>
      </form>
    </section>

    <section class="mt-10">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Customer Reviews</h2>
      <div class="space-y-6">
        <?php if ($reviews): ?>
          <?php foreach ($reviews as $rev): ?>
            <div class="bg-gray-100 rounded-md p-4 shadow-sm">
              <p class="font-semibold text-gray-800"><?= htmlspecialchars($rev['user_name']) ?> <span class="text-yellow-500">- <?= $rev['rating'] ?>/5</span></p>
              <p class="text-gray-700"><?= nl2br(htmlspecialchars($rev['review'])) ?></p>
              <p class="text-xs text-gray-500 mt-1"><?= date('F j, Y, g:i a', strtotime($rev['created_at'])) ?></p>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-gray-500">No reviews yet. Be the first to review this product!</p>
        <?php endif; ?>
      </div>
    </section>
  </div>
</main>

<footer class="bg-white border-t mt-12 py-6 text-center text-sm text-gray-500">
  &copy; <?= date('Y') ?> Online Art Store. All rights reserved.
</footer>

</body>
</html>

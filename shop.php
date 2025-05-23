<?php
session_start();
require 'db.php';

// Sorting Logic
$sort = $_GET['sort'] ?? null;
$filter = $_GET['filter'] ?? null;

$sql = "SELECT * FROM products";

// Filtering Logic
if ($filter === 'cheap') {
    $sql .= " WHERE price < 50";
} elseif ($filter === 'mid') {
    $sql .= " WHERE price BETWEEN 50 AND 150";
} elseif ($filter === 'expensive') {
    $sql .= " WHERE price > 150";
}

// Sorting Logic
if ($sort === 'asc') {
    $sql .= " ORDER BY price ASC";
} elseif ($sort === 'desc') {
    $sql .= " ORDER BY price DESC";
}

$stmt = $pdo->query($sql);
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Online Art Store</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#22c55e',
          }
        }
      }
    };
  </script>
  <style>
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 15px -3px rgba(34,197,94,0.4), 0 4px 6px -2px rgba(34,197,94,0.1);
    }
    .transition-smooth {
      transition: all 0.3s ease-in-out;
    }
    .active-filter {
      @apply bg-primary text-white;
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans leading-relaxed">

<!-- Header -->
<header class="bg-primary shadow-md">
  <div class="max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
    <a href="index.php">
      <h1 class="text-2xl font-bold text-white">Online Art Store</h1>
    </a>
    <nav class="space-x-4">
      <a href="index.php" class="text-white hover:underline">Home</a>
      <a href="shop.php" class="text-white hover:underline">Shop</a>
      <a href="cart.php" class="bg-white text-primary px-4 py-2 rounded hover:bg-gray-100 font-medium">🛒 Cart</a>
    </nav>
  </div>
</header>

<!-- Filter & Sort Bar -->
<section class="max-w-7xl mx-auto px-6 pt-10">
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <!-- Sort -->
    <div>
      <h3 class="text-lg font-medium mb-2">Sort by:</h3>
      <div class="flex gap-3">
        <a href="?sort=asc<?= $filter ? "&filter=$filter" : '' ?>" class="px-4 py-2 border rounded-md <?= $sort === 'asc' ? 'bg-primary text-white' : 'bg-white' ?>">Price: Low to High</a>
        <a href="?sort=desc<?= $filter ? "&filter=$filter" : '' ?>" class="px-4 py-2 border rounded-md <?= $sort === 'desc' ? 'bg-primary text-white' : 'bg-white' ?>">Price: High to Low</a>
      </div>
    </div>

    <!-- Filter -->
    <div>
      <h3 class="text-lg font-medium mb-2">Filter by Price:</h3>
      <div class="flex gap-3">
        <a href="?filter=cheap<?= $sort ? "&sort=$sort" : '' ?>" class="px-4 py-2 border rounded-md <?= $filter === 'cheap' ? 'bg-primary text-white' : 'bg-white' ?>">Under $50</a>
        <a href="?filter=mid<?= $sort ? "&sort=$sort" : '' ?>" class="px-4 py-2 border rounded-md <?= $filter === 'mid' ? 'bg-primary text-white' : 'bg-white' ?>">$50 - $150</a>
        <a href="?filter=expensive<?= $sort ? "&sort=$sort" : '' ?>" class="px-4 py-2 border rounded-md <?= $filter === 'expensive' ? 'bg-primary text-white' : 'bg-white' ?>">Over $150</a>
        <a href="index.php" class="px-4 py-2 border rounded-md text-red-600 hover:bg-red-100">Reset</a>
      </div>
    </div>
  </div>
</section>

<!-- Product Listings -->
<main class="max-w-7xl mx-auto px-6 pb-20">
  <?php if (count($products) > 0): ?>
    <section class="grid gap-10 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($products as $product): ?>
        <article class="card bg-white rounded-xl shadow-md overflow-hidden transition-smooth">
  <a href="product.php?id=<?= $product['id'] ?>">
    <img 
      src="images/<?= htmlspecialchars($product['image']) ?>" 
      alt="<?= htmlspecialchars($product['name']) ?>" 
      class="w-full h-64 object-cover object-center"
      loading="lazy"
    />
  </a>
  <div class="p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-2"><?= htmlspecialchars($product['name']) ?></h2>
    <p class="text-gray-600 text-sm mb-4"><?= htmlspecialchars($product['description']) ?></p>
    <p class="text-green-700 font-bold text-lg mb-4">$<?= number_format($product['price'], 2) ?></p>
    <form action="add_to_cart.php" method="GET" class="text-center">
      <input type="hidden" name="id" value="<?= $product['id'] ?>" />
      <button 
        type="submit" 
        class="bg-primary text-white px-6 py-2 rounded-md font-semibold hover:bg-green-700 transition"
      >
        ➕ Add to Cart
      </button>
    </form>
  </div>
</article>

      <?php endforeach; ?>
    </section>
  <?php else: ?>
    <p class="text-center text-red-600 mt-10 text-lg">😕 No products match your criteria.</p>
  <?php endif; ?>
</main>

<!-- Footer -->
<footer class="bg-gray-100 text-center text-gray-500 text-sm py-6 border-t border-gray-200">
  &copy; <?= date('Y') ?> Online Art Store. All rights reserved.
</footer>

</body>
</html>

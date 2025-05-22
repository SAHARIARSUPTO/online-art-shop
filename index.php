<?php
session_start();
require 'db.php';

// Fetch products
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Online Art Store</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* subtle card hover effect */
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 15px -3px rgba(34,197,94,0.4), 0 4px 6px -2px rgba(34,197,94,0.1);
    }
    .transition-smooth {
      transition: all 0.3s ease-in-out;
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans leading-relaxed">

  <header class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
      <h1 class="text-3xl font-extrabold text-green-600 flex items-center gap-2" aria-label="Online Art Store Logo">
        <span aria-hidden="true"></span> Online Art Store
      </h1>
      <a 
        href="cart.php" 
        class="inline-block bg-green-600 text-white px-5 py-2 rounded-md text-sm font-semibold hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400"
        aria-label="View shopping cart"
      >
        🛒 View Cart
      </a>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-6 py-12">
    <?php if (count($products) > 0): ?>
      <section aria-label="Product listings" class="grid gap-10 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($products as $product): ?>
          <article 
            class="card bg-white rounded-xl shadow-md overflow-hidden transition-smooth cursor-pointer"
            tabindex="0"
            aria-labelledby="product-title-<?= $product['id'] ?>"
            aria-describedby="product-desc-<?= $product['id'] ?>"
          >
            <figure class="w-full h-64 overflow-hidden">
              <img 
                src="images/<?= htmlspecialchars($product['image']) ?>" 
                alt="<?= htmlspecialchars($product['name']) ?>" 
                class="w-full h-full object-cover object-center"
                loading="lazy"
              />
            </figure>
            <div class="p-6">
              <h2 
                id="product-title-<?= $product['id'] ?>" 
                class="text-xl font-semibold text-gray-900 mb-2"
              >
                <?= htmlspecialchars($product['name']) ?>
              </h2>
              <p 
                id="product-desc-<?= $product['id'] ?>" 
                class="text-gray-600 text-sm mb-4 min-h-[3rem]"
              >
                <?= htmlspecialchars($product['description']) ?>
              </p>
              <p class="text-green-700 font-bold text-lg mb-6">
                $<?= number_format($product['price'], 2) ?>
              </p>
              <form action="add_to_cart.php" method="GET" class="text-center">
                <input type="hidden" name="id" value="<?= $product['id'] ?>" />
                <button 
                  type="submit" 
                  class="inline-block bg-green-600 text-white px-6 py-2 rounded-md font-semibold hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400 transition"
                  aria-label="Add <?= htmlspecialchars($product['name']) ?> to cart"
                >
                  ➕ Add to Cart
                </button>
              </form>
            </div>
          </article>
        <?php endforeach; ?>
      </section>
    <?php else: ?>
      <section class="text-center py-20">
        <p class="text-red-600 text-xl font-medium">😕 Sorry, no products found in the store right now.</p>
      </section>
    <?php endif; ?>
  </main>

  <footer class="bg-white text-center text-gray-500 text-sm py-6 border-t border-gray-200">
    &copy; <?= date('Y') ?> Online Art Store. All rights reserved.
  </footer>

</body>
</html>

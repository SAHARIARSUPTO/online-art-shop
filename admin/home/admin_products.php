<?php
session_start();
require '../../db.php'; // or correct relative path

// Fetch all products
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Products</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 15px -3px rgba(34,197,94,0.4), 0 4px 6px -2px rgba(34,197,94,0.1);
    }
    .transition-smooth {
      transition: all 0.3s ease-in-out;
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">

  <div class="max-w-7xl mx-auto px-6 py-10">
    <h1 class="text-3xl font-bold mb-8 text-green-700">🎨 Manage Products</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
      <?php foreach ($products as $product): ?>
        <div class="card bg-white rounded-xl shadow p-5 transition-smooth">
          <img 
            src="../../images/<?= htmlspecialchars($product['image']) ?>" 
            alt="<?= htmlspecialchars($product['name']) ?>" 
            class="w-full h-48 object-cover rounded-md mb-4"
          />

          <h2 class="text-xl font-semibold text-gray-900 mb-1"><?= htmlspecialchars($product['name']) ?></h2>
          <p class="text-sm text-gray-600 mb-2"><?= htmlspecialchars($product['description']) ?></p>
          <p class="text-green-600 font-bold text-lg mb-4">$<?= number_format($product['price'], 2) ?></p>

          <div class="flex justify-between gap-2">
            <a 
              href="edit_product.php?id=<?= $product['id'] ?>" 
              class="flex-1 text-center bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition"
            >
              ✏️ Edit
            </a>
            <a 
              href="delete_product.php?id=<?= $product['id'] ?>" 
              onclick="return confirm('Are you sure you want to delete this product?')"
              class="flex-1 text-center bg-red-500 text-white py-2 rounded hover:bg-red-600 transition"
            >
              🗑️ Delete
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if (count($products) === 0): ?>
      <p class="text-center text-gray-500 mt-10 text-lg">😢 No products available to manage.</p>
    <?php endif; ?>
  </div>

</body>
</html>

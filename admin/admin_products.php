<?php
session_start();
require '../db.php';

// Handle Add New Product submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
  $name = trim($_POST['name'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $price = floatval($_POST['price'] ?? 0);

  if ($name === '' || $description === '' || $price <= 0) {
    $error = "Please fill out all fields with valid info.";
  } elseif (empty($_FILES['image']['name'])) {
    $error = "Please upload a product image.";
  } else {
    $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
    $target = '../../images/' . $imageName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
      $insertStmt = $pdo->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
      $insertStmt->execute([$name, $description, $price, $imageName]);
      header("Location: " . $_SERVER['PHP_SELF']);
      exit;
    } else {
      $error = "Failed to upload image.";
    }
  }
}

// Fetch products
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth" >
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Product Management Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Hover card effect */
    .card:hover {
      transform: translateY(-6px);
      box-shadow: 0 12px 20px -4px rgba(34,197,94,0.6), 0 6px 10px -3px rgba(34,197,94,0.15);
    }
    .transition-smooth {
      transition: all 0.3s ease-in-out;
    }
    /* Custom scrollbar for product list */
    .product-list::-webkit-scrollbar {
      width: 8px;
    }
    .product-list::-webkit-scrollbar-thumb {
      background-color: #22c55e;
      border-radius: 4px;
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-900 font-sans min-h-screen flex flex-col">

  <header class="bg-green-600 text-white p-6 shadow-md sticky top-0 z-10">
    <h1 class="text-3xl font-extrabold tracking-tight">🎨 Product Management Dashboard</h1>
  </header>



  <main class="flex flex-1 max-w-7xl mx-auto p-8 gap-10 flex-col md:flex-row">

    <!-- Add New Product Form (Left) -->
    <section class="md:w-1/3 bg-white rounded-xl shadow-md p-8 sticky top-24 self-start h-fit">
      <h2 class="text-2xl font-bold mb-6 text-green-700">➕ Add New Product</h2>

      <?php if (!empty($error)): ?>
        <div class="mb-4 px-4 py-3 bg-red-100 text-red-700 rounded font-semibold">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="space-y-5" novalidate>
        <input type="hidden" name="add_product" value="1" />

        <div>
          <label for="name" class="block mb-1 font-medium">Product Name</label>
          <input id="name" type="text" name="name" required
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" />
        </div>

        <div>
          <label for="description" class="block mb-1 font-medium">Description</label>
          <textarea id="description" name="description" rows="4" required
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
        </div>

        <div>
          <label for="price" class="block mb-1 font-medium">Price ($)</label>
          <input id="price" type="number" step="0.01" min="0" name="price" required
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" />
        </div>

        <div>
          <label for="image" class="block mb-1 font-medium">Product Image</label>
          <input id="image" type="file" name="image" accept="image/*" required
            class="w-full" />
        </div>

        <button type="submit"
          class="w-full bg-green-600 hover:bg-green-700 transition text-white py-3 rounded-md font-semibold text-lg">
          ➕ Add Product
        </button>
      </form>
    </section>

    <!-- Product List (Right) -->
    <section class="md:w-2/3 overflow-auto product-list max-h-[80vh]">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        <?php if (count($products) === 0): ?>
          <p class="col-span-full text-center text-gray-500 mt-10 text-lg">😢 No products available.</p>
        <?php endif; ?>

        <?php foreach ($products as $product): ?>
          <div class="card bg-white rounded-xl shadow-md p-5 transition-smooth flex flex-col">
            <img
              src="../../images/<?= htmlspecialchars($product['image']) ?>"
              alt="<?= htmlspecialchars($product['name']) ?>"
              class="w-full h-44 object-cover rounded-md mb-4"
              loading="lazy"
            />

            <h3 class="text-xl font-semibold text-gray-900 mb-1 truncate"><?= htmlspecialchars($product['name']) ?></h3>
            <p class="text-gray-600 text-sm flex-1 mb-3"><?= htmlspecialchars($product['description']) ?></p>
            <p class="text-green-600 font-bold text-lg mb-4">$<?= number_format($product['price'], 2) ?></p>

            <div class="flex gap-3">
              <a href="edit_product.php?id=<?= $product['id'] ?>"
                class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white py-2 rounded transition font-semibold">
                ✏️ Edit
              </a>
              <a href="delete_product.php?id=<?= $product['id'] ?>"
                onclick="return confirm('Are you sure you want to delete this product?')"
                class="flex-1 text-center bg-red-600 hover:bg-red-700 text-white py-2 rounded transition font-semibold">
                🗑️ Delete
              </a>
            </div>
          </div>
        <?php endforeach; ?>

      </div>
    </section>

  </main>
<div class="m-10 flex justify-center">
  <a href="dashboard.php" class="inline-block bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition font-semibold">
    ← Back to Dashboard
  </a>
</div>
  <footer class="bg-gray-100 text-center p-4 text-sm text-gray-600">
    &copy; <?= date('Y') ?> Your Company. All rights reserved.
  </footer>

</body>
</html>

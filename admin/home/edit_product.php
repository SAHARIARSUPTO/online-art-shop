<?php
session_start();
require '../../db.php'; // update path if needed

// Check if ID exists
if (!isset($_GET['id'])) {
  die('Product ID is missing.');
}

$id = (int) $_GET['id'];

// Fetch existing product data
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
  die('Product not found.');
}

// Update product if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'] ?? '';
  $description = $_POST['description'] ?? '';
  $price = $_POST['price'] ?? 0;

  // Handle image upload
  if ($_FILES['image']['name']) {
    $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
    $target = '../../images/' . $imageName;
    move_uploaded_file($_FILES['image']['tmp_name'], $target);
  } else {
    $imageName = $product['image']; // Keep old image
  }

  // Update DB
  $updateStmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, image = ? WHERE id = ?");
  $updateStmt->execute([$name, $description, $price, $imageName, $id]);

  header("Location: admin_products.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Product</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-10 px-6 text-gray-800 font-sans">

  <div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-8">
    <h1 class="text-2xl font-bold text-green-600 mb-6">📝 Edit Product</h1>

    <form method="POST" enctype="multipart/form-data" class="space-y-6">
      <div>
        <label class="block mb-2 font-medium">Product Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-green-500" />
      </div>

      <div>
        <label class="block mb-2 font-medium">Description</label>
        <textarea name="description" rows="4" required class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-green-500"><?= htmlspecialchars($product['description']) ?></textarea>
      </div>

      <div>
        <label class="block mb-2 font-medium">Price ($)</label>
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']) ?>" required class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-green-500" />
      </div>

      <div>
        <label class="block mb-2 font-medium">Product Image</label>
        <input type="file" name="image" class="w-full" />
        <p class="text-sm mt-2 text-gray-500">Leave blank to keep current image.</p>
        <img src="../../images/<?= htmlspecialchars($product['image']) ?>" alt="Current image" class="mt-4 w-32 rounded shadow border" />
      </div>

      <div class="flex gap-4">
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition">✅ Update</button>
        <a href="admin_products.php" class="text-gray-500 underline">Cancel</a>
      </div>
    </form>
  </div>

</body>
</html>

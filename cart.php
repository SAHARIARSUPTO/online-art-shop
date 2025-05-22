<?php
session_start();
$cart = $_SESSION['cart'] ?? [];

function formatPrice($price) {
  return '$' . number_format($price, 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Cart - Online Art Store</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; }
  </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">

  <!-- Header -->
  <header class="bg-white shadow">
    <div class="max-w-6xl mx-auto px-6 py-6 flex justify-between items-center">
      <h1 class="text-2xl font-bold text-green-600">🛒 Your Cart</h1>
      <a href="index.php" class="text-sm text-green-600 hover:underline">← Continue Shopping</a>
    </div>
  </header>

  <!-- Main Content -->
  <main class="flex-grow max-w-6xl mx-auto px-6 py-10 w-full">
    <?php if (count($cart) > 0): ?>
      <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="w-full text-left text-sm">
          <thead class="bg-green-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-4 font-semibold text-gray-600">Product</th>
              <th class="px-6 py-4 font-semibold text-gray-600">Price</th>
              <th class="px-6 py-4 font-semibold text-gray-600">Quantity</th>
              <th class="px-6 py-4 font-semibold text-gray-600">Subtotal</th>
              <th class="px-6 py-4 font-semibold text-gray-600">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <?php 
              $total = 0;
              foreach ($cart as $item):
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
              <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 flex items-center gap-4">
                  <img src="images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-16 h-16 object-cover rounded shadow">
                  <span class="font-medium"><?= htmlspecialchars($item['name']) ?></span>
                </td>
                <td class="px-6 py-4"><?= formatPrice($item['price']) ?></td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2">
                    <a href="update_quantity.php?id=<?= $item['id'] ?>&action=decrease" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 rounded transition">−</a>
                    <span class="min-w-[24px] text-center"><?= $item['quantity'] ?></span>
                    <a href="update_quantity.php?id=<?= $item['id'] ?>&action=increase" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 rounded transition">+</a>
                  </div>
                </td>
                <td class="px-6 py-4 font-semibold text-gray-700"><?= formatPrice($subtotal) ?></td>
                <td class="px-6 py-4">
                  <a href="remove_from_cart.php?id=<?= $item['id'] ?>" class="text-red-500 hover:text-red-600 hover:underline">Remove</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot class="bg-gray-50">
            <tr>
              <td colspan="3" class="px-6 py-4 text-right text-lg font-bold">Total:</td>
              <td class="px-6 py-4 text-lg font-bold text-green-600"><?= formatPrice($total) ?></td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- Checkout Button -->
      <div class="mt-8 text-right">
        <a href="order_form.php" class="bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700 transition">Proceed to Checkout</a>
      </div>

    <?php else: ?>
      <div class="text-center py-20">
        <p class="text-gray-500 text-lg mb-6">😕 Your cart is currently empty.</p>
        <a href="index.php" class="bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700 transition">Back to Shop</a>
      </div>
    <?php endif; ?>
  </main>

  <!-- Footer -->
  <footer class="bg-white text-center text-sm text-gray-400 py-6">
    &copy; <?= date('Y') ?> Online Art Store. All rights reserved.
  </footer>

</body>
</html>

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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart - Online Art Store</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; }
  </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">

  <header class="bg-white shadow-sm">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex flex-col sm:flex-row justify-between items-center text-center sm:text-left">
      <h1 class="text-2xl font-bold text-green-700 mb-2 sm:mb-0">🛒 Your Cart</h1>
      <a href="index.php" class="text-sm text-green-600 hover:text-green-800 transition duration-300">← Continue Shopping</a>
    </div>
  </header>

  <main class="flex-grow max-w-6xl mx-auto px-4 sm:px-6 py-10 w-full flex flex-col items-center">
    <?php if (count($cart) > 0): ?>
      <div class="w-full bg-white shadow-lg rounded-xl overflow-hidden">
        <div class="hidden md:block">
          <table class="min-w-full w-full text-left text-sm">
            <thead class="bg-green-100 border-b border-gray-200">
              <tr>
                <th class="px-6 py-4 font-semibold text-gray-700">Product</th>
                <th class="px-6 py-4 font-semibold text-gray-700">Price</th>
                <th class="px-6 py-4 font-semibold text-gray-700">Quantity</th>
                <th class="px-6 py-4 font-semibold text-gray-700">Subtotal</th>
                <th class="px-6 py-4 font-semibold text-gray-700">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <?php
                $total = 0;
                foreach ($cart as $item):
                  $subtotal = $item['price'] * $item['quantity'];
                  $total += $subtotal;
              ?>
                <tr class="hover:bg-gray-50 transition duration-150">
                  <td class="px-6 py-4 flex items-center gap-4">
                    <img src="images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-16 h-16 object-cover rounded-md shadow-sm">
                    <span class="font-medium text-gray-800"><?= htmlspecialchars($item['name']) ?></span>
                  </td>
                  <td class="px-6 py-4 text-gray-700"><?= formatPrice($item['price']) ?></td>
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                      <a href="update_quantity.php?id=<?= $item['id'] ?>&action=decrease" class="bg-gray-200 hover:bg-gray-300 text-gray-700 w-7 h-7 flex items-center justify-center rounded-full transition duration-150 text-lg font-semibold">−</a>
                      <span class="min-w-[24px] text-center font-medium text-gray-800"><?= $item['quantity'] ?></span>
                      <a href="update_quantity.php?id=<?= $item['id'] ?>&action=increase" class="bg-gray-200 hover:bg-gray-300 text-gray-700 w-7 h-7 flex items-center justify-center rounded-full transition duration-150 text-lg font-semibold">+</a>
                    </div>
                  </td>
                  <td class="px-6 py-4 font-semibold text-green-700"><?= formatPrice($subtotal) ?></td>
                  <td class="px-6 py-4">
                    <a href="remove_from_cart.php?id=<?= $item['id'] ?>" class="text-red-500 hover:text-red-700 hover:underline transition duration-150">Remove</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot class="bg-gray-50 border-t border-gray-200">
              <tr>
                <td colspan="3" class="px-6 py-4 text-right text-lg font-bold text-gray-800">Total:</td>
                <td class="px-6 py-4 text-xl font-bold text-green-700"><?= formatPrice($total) ?></td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>

        <div class="md:hidden divide-y divide-gray-100">
          <?php
            $total = 0; // Recalculate total for this view
            foreach ($cart as $item):
              $subtotal = $item['price'] * $item['quantity'];
              $total += $subtotal;
          ?>
            <div class="p-4 flex items-center gap-4">
              <img src="images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-20 h-20 object-cover rounded-md shadow-sm flex-shrink-0">
              <div class="flex-grow">
                <p class="font-semibold text-gray-800 text-base mb-1"><?= htmlspecialchars($item['name']) ?></p>
                <p class="text-gray-600 text-sm">Price: <span class="font-medium"><?= formatPrice($item['price']) ?></span></p>
                <div class="flex items-center gap-2 mt-2">
                  <span class="text-gray-600 text-sm">Qty:</span>
                  <a href="update_quantity.php?id=<?= $item['id'] ?>&action=decrease" class="bg-gray-200 hover:bg-gray-300 text-gray-700 w-6 h-6 flex items-center justify-center rounded-full transition duration-150 text-md font-semibold">−</a>
                  <span class="min-w-[20px] text-center font-medium text-gray-800 text-sm"><?= $item['quantity'] ?></span>
                  <a href="update_quantity.php?id=<?= $item['id'] ?>&action=increase" class="bg-gray-200 hover:bg-gray-300 text-gray-700 w-6 h-6 flex items-center justify-center rounded-full transition duration-150 text-md font-semibold">+</a>
                </div>
                <p class="text-gray-700 mt-2 text-sm">Subtotal: <span class="font-bold text-green-700"><?= formatPrice($subtotal) ?></span></p>
                <div class="mt-2">
                  <a href="remove_from_cart.php?id=<?= $item['id'] ?>" class="text-red-500 hover:text-red-700 hover:underline text-xs transition duration-150">Remove</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
          <div class="p-4 bg-gray-50 flex justify-between items-center">
            <span class="text-lg font-bold text-gray-800">Total:</span>
            <span class="text-xl font-bold text-green-700"><?= formatPrice($total) ?></span>
          </div>
        </div>
      </div>

      <div class="mt-8 text-center">
        <a href="order_form.php" class="inline-block bg-green-600 text-white px-8 py-4 rounded-lg hover:bg-green-700 transition duration-300 text-lg font-semibold shadow-md">Proceed to Checkout</a>
      </div>

    <?php else: ?>
      <div class="text-center py-20 bg-white shadow-lg rounded-xl p-8 max-w-md w-full">
        <p class="text-gray-600 text-xl mb-6">😕 Your cart is currently empty.</p>
        <a href="index.php" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-300 text-base font-medium">Back to Shop</a>
      </div>
    <?php endif; ?>
  </main>

  <footer class="bg-white text-center text-sm text-gray-500 py-6 border-t border-gray-100">
    &copy; <?= date('Y') ?> Online Art Store. All rights reserved.
  </footer>

</body>
</html>
<?php
session_start();
$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
  header("Location: cart.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout - Art Store</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @media (max-width: 640px) {
      .checkout-container {
        margin-top: 1rem;
        margin-bottom: 1rem;
        padding: 1.5rem;
      }
      .input-field {
        padding: 0.75rem;
      }
    }
  </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-start sm:items-center justify-center p-4 sm:p-6">

  <div class="checkout-container w-full max-w-md bg-white rounded-lg shadow-md p-6 sm:p-8">
    <h2 class="text-xl sm:text-2xl font-bold text-center text-gray-800 mb-4 sm:mb-6">Complete Your Order</h2>

    <!-- Cart Summary (Mobile Friendly) -->
    <div class="bg-gray-50 p-4 rounded-lg mb-4 sm:mb-6">
      <h3 class="font-medium text-gray-700 mb-2">Order Summary</h3>
      <div class="space-y-2 max-h-40 overflow-y-auto">
        <?php foreach($cart as $item): ?>
        <div class="flex justify-between text-sm">
          <span><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></span>
          <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="border-t border-gray-200 mt-3 pt-3 font-medium">
        <div class="flex justify-between">
          <span>Total:</span>
          <span>$<?= number_format(array_sum(array_map(function($item) { 
            return $item['price'] * $item['quantity']; 
          }, $cart)), 2) ?></span>
        </div>
      </div>
    </div>

    <form action="order.php" method="POST" class="space-y-4 sm:space-y-5">
      <div>
        <label class="block text-sm sm:text-base font-medium text-gray-700 mb-1">Full Name</label>
        <input type="text" name="name" required placeholder="Your Name"
          class="input-field w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent">
      </div>

      <div>
        <label class="block text-sm sm:text-base font-medium text-gray-700 mb-1">Email Address</label>
        <input type="email" name="email" required placeholder="your@email.com"
          class="input-field w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent">
      </div>

      <div>
        <label class="block text-sm sm:text-base font-medium text-gray-700 mb-1">Phone Number</label>
        <input type="tel" name="phone" required placeholder="e.g. 04XX XXX XXX"
          class="input-field w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent">
      </div>

      <div>
        <label class="block text-sm sm:text-base font-medium text-gray-700 mb-1">Delivery Address</label>
        <textarea name="address" required rows="3" placeholder="Street, City, Postcode"
          class="input-field w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
      </div>

      <div class="pt-2">
        <button type="submit"
          class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-md transition duration-200 ease-in-out transform hover:scale-[1.01]">
          Place Order ($<?= number_format(array_sum(array_map(function($item) { 
            return $item['price'] * $item['quantity']; 
          }, $cart)), 2) ?>)
        </button>
      </div>
    </form>

    <p class="text-xs sm:text-sm text-center text-gray-500 mt-4 sm:mt-6">
      Need help? <a href="contact.php" class="text-green-600 hover:text-green-700 underline">Contact us</a>
    </p>
  </div>

</body>
</html>
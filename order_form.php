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
  <title>Checkout - Art Store</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">

  <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Complete Your Order</h2>

    <form action="order.php" method="POST" class="space-y-5">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
        <input type="text" name="name" required placeholder="Your Name"
          class="w-full px-4 py-2 border border-gray-300 rounded-md">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
        <input type="email" name="email" required placeholder="Your Email"
          class="w-full px-4 py-2 border border-gray-300 rounded-md">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
        <input type="text" name="phone" required placeholder="e.g. 04XX XXX XXX"
          class="w-full px-4 py-2 border border-gray-300 rounded-md">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Address</label>
        <input type="text" name="address" required placeholder="Street, City, Postcode"
          class="w-full px-4 py-2 border border-gray-300 rounded-md">
      </div>

      <button type="submit"
        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-md">
         Place Order
      </button>
    </form>

    <p class="text-sm text-center text-gray-500 mt-4">Need help? <a href="contact.php" class="text-green-600 underline">Contact us</a></p>
  </div>

</body>
</html>

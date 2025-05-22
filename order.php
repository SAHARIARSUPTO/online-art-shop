<?php
require_once 'db.php'; // Your PDO connection
session_start();

$cart = $_SESSION['cart'] ?? [];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: order_form.php");
  exit;
}

// Validate form inputs
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');

if (!$name || !$email || !$phone || !$address || empty($cart)) {
  echo "Please fill in all required fields and make sure your cart is not empty.";
  exit;
}

// Calculate total
$total = 0;
foreach ($cart as $item) {
  $total += $item['price'] * $item['quantity'];
}

try {
  $pdo->beginTransaction();

  // Insert order (assuming orders table has columns: customer_name, email, phone, address, total, status, is_approved)
  $stmt = $pdo->prepare("INSERT INTO orders (customer_name, email, phone, address, total, status, is_approved) VALUES (?, ?, ?, ?, ?, 'pending', 0)");
  $stmt->execute([$name, $email, $phone, $address, $total]);
  $orderId = $pdo->lastInsertId();

  // Insert order items with product name
  $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)");

  foreach ($cart as $item) {
    $itemStmt->execute([
      $orderId,
      $item['id'],
      $item['name'],
      $item['quantity'],
      $item['price']
    ]);
  }

  $pdo->commit();
} catch (Exception $e) {
  $pdo->rollBack();
  die("Order failed: " . $e->getMessage());
}


function formatPrice($price) {
  return '$' . number_format($price, 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Order Confirmation - Art Store</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-10 px-4">
  <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-lg">
    <h2 class="text-3xl font-bold text-green-600 mb-6">🎉 Order Confirmed!</h2>

    <p class="text-gray-700 mb-4">
      Thank you <strong><?= htmlspecialchars($name) ?></strong> for your order.<br />
      A confirmation email has been sent to <strong><?= htmlspecialchars($email) ?></strong>.
    </p>

    <div class="mb-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-2">Delivery Info:</h3>
      <p><strong>Phone:</strong> <?= htmlspecialchars($phone) ?></p>
      <p><strong>Address:</strong> <?= htmlspecialchars($address) ?></p>
    </div>

    <div class="mb-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-2">Order Summary:</h3>
      <table class="w-full text-sm border">
        <thead class="bg-gray-100 text-left">
          <tr>
            <th class="py-2 px-4">Product</th>
            <th class="py-2 px-4">Qty</th>
            <th class="py-2 px-4">Price</th>
            <th class="py-2 px-4">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cart as $item): ?>
            <tr class="border-t">
              <td class="py-2 px-4"><?= htmlspecialchars($item['name']) ?></td>
              <td class="py-2 px-4"><?= $item['quantity'] ?></td>
              <td class="py-2 px-4"><?= formatPrice($item['price']) ?></td>
              <td class="py-2 px-4"><?= formatPrice($item['price'] * $item['quantity']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot class="border-t bg-gray-50">
          <tr>
            <td colspan="3" class="py-2 px-4 font-semibold text-right">Total:</td>
            <td class="py-2 px-4 font-semibold text-green-600"><?= formatPrice($total) ?></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <a href="index.php" class="inline-block mt-4 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-md">
      Back to Home
    </a>
  </div>
</body>
</html>

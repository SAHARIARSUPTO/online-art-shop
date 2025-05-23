<?php
require_once 'db.php'; // Your PDO connection
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/PHPMailer/src/Exception.php';


$cart = $_SESSION['cart'] ?? [];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: order_form.php");
  exit;
}

// Validate form inputs
$name = trim($_POST['name'] ?? '');
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
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

// PHP MAILER PART
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->SMTPDebug = 0; // Set to 2 for debugging
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'your_email@gmail.com'; // Replace with your email
    $mail->Password   = 'your_app_password';    // Use app password if 2FA is on
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('your_email@gmail.com', 'Online Art Store');
    $mail->isHTML(true);

    // To customer
    $mail->addAddress($email, $name);
    $mail->Subject = 'Order Confirmation from Online Art Store';
    $mail->Body = "
  <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
    <h2 style='color: #F55E61;'> Online Art Store</h2>
    <p>Hi <strong>$name</strong>,</p>
    <p>Thank you for your order! We're excited to get your items ready for delivery. Below are your order details:</p>

    <h3 style='margin-top: 20px; color: #555;'>🧾 Order Summary</h3>
    <ul style='padding-left: 20px;'>
";

foreach ($cart as $item) {
  $mail->Body .= "<li>{$item['name']} ({$item['quantity']} × $" . number_format($item['price'], 2) . ")</li>";
}

$mail->Body .= "
    </ul>
    <p><strong>Total:</strong> $" . number_format($total, 2) . "</p>

    <h3 style='margin-top: 20px; color: #555;'>📦 Delivery Info</h3>
    <p><strong>Phone:</strong> $phone<br><strong>Address:</strong> $address</p>

    <p style='margin-top: 30px;'>We'll reach out shortly to arrange delivery. If you have any questions, feel free to reply to this email.</p>
    <p style='margin-top: 20px;'>Thanks again for shopping with us! 💖</p>
    <p>— <em>The Online Art Store Team</em></p>
  </div>
";

    $mail->send();

    // To business
    $mail->clearAddresses();
    $mail->addAddress('business_email@example.com', 'Store Owner'); // Replace with business email
    $mail->Subject = '📥 New Order Received';
    $mail->Body = "
  <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
    <h2 style='color: #F55E61;'>📥 New Order Received</h2>
    <p><strong>From:</strong> $name ($email)</p>
    <p><strong>Phone:</strong> $phone</p>
    <p><strong>Address:</strong> $address</p>

    <h3 style='margin-top: 20px; color: #555;'>🛒 Order Items</h3>
    <ul style='padding-left: 20px;'>
";

foreach ($cart as $item) {
  $mail->Body .= "<li>{$item['name']} ({$item['quantity']} × $" . number_format($item['price'], 2) . ")</li>";
}

$mail->Body .= "
    </ul>
    <p><strong>Total Order Value:</strong> $" . number_format($total, 2) . "</p>

    <p style='margin-top: 30px;'>Login to the admin dashboard to approve and process this order.</p>
    <p>🖼️ <strong>Online Art Store Dashboard</strong></p>
  </div>
";

    $mail->send();

} catch (Exception $e) {
    error_log("Email Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Order Confirmation - Art Store</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-10 px-4">
  <div class="max-w-3xl mx-auto bg-white p-6 sm:p-8 rounded-lg shadow-lg">
    <h2 class="text-2xl sm:text-3xl font-bold text-green-600 mb-4 sm:mb-6">🎉 Order Confirmed!</h2>

    <div class="space-y-4">
      <p class="text-gray-700">
        Thank you <strong><?= htmlspecialchars($name) ?></strong> for your order.<br />
        A confirmation email has been sent to <strong><?= htmlspecialchars($email) ?></strong>.
      </p>

      <div class="bg-gray-50 p-4 rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Delivery Info:</h3>
        <p><strong>Phone:</strong> <?= htmlspecialchars($phone) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($address) ?></p>
      </div>

      <div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Order Summary:</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-sm border">
            <thead class="bg-gray-100 text-left">
              <tr>
                <th class="py-2 px-3 sm:px-4">Product</th>
                <th class="py-2 px-3 sm:px-4">Qty</th>
                <th class="py-2 px-3 sm:px-4">Price</th>
                <th class="py-2 px-3 sm:px-4">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($cart as $item): ?>
                <tr class="border-t">
                  <td class="py-2 px-3 sm:px-4"><?= htmlspecialchars($item['name']) ?></td>
                  <td class="py-2 px-3 sm:px-4"><?= $item['quantity'] ?></td>
                  <td class="py-2 px-3 sm:px-4"><?= formatPrice($item['price']) ?></td>
                  <td class="py-2 px-3 sm:px-4"><?= formatPrice($item['price'] * $item['quantity']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot class="border-t bg-gray-50">
              <tr>
                <td colspan="3" class="py-2 px-3 sm:px-4 font-semibold text-right">Total:</td>
                <td class="py-2 px-3 sm:px-4 font-semibold text-green-600"><?= formatPrice($total) ?></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <div class="flex justify-between items-center pt-4">
        <a href="shop.php" class="text-green-600 hover:text-green-700 underline">
          Continue Shopping
        </a>
        <a href="index.php" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-md">
          Back to Home
        </a>
      </div>
    </div>
  </div>
</body>
</html>
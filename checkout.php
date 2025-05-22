<?php
session_start();
require 'db.php'; // your PDO connection

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
  header('Location: cart.php');
  exit();
}

try {
  $pdo->beginTransaction();

// When inserting order (checkout.php)
$stmt = $pdo->prepare("INSERT INTO orders (created_at, is_new) VALUES (NOW(), 1)");
$stmt->execute();
  $orderId = $pdo->lastInsertId();

  // Insert order items
  $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");

  foreach ($cart as $item) {
    $itemStmt->execute([
      $orderId,
      $item['id'],
      $item['quantity'],
      $item['price']
    ]);
  }

  $pdo->commit();

  // Save order ID if needed
  $_SESSION['order_id'] = $orderId;

  // Clear the cart
  unset($_SESSION['cart']);



  header("Location: order.php");
  exit();

} catch (Exception $e) {
  $pdo->rollBack();
  echo "Order failed: " . $e->getMessage();
}

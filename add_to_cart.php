<?php
session_start();
require 'db.php';

if (!isset($_GET['id'])) {
  header('Location: index.php');
  exit;
}

$id = (int)$_GET['id'];

// Get product details from DB
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
  header('Location: index.php');
  exit;
}

$cart = $_SESSION['cart'] ?? [];

// If product already in cart, increase quantity
if (isset($cart[$id])) {
  $cart[$id]['quantity'] += 1;
} else {
  $cart[$id] = [
    'id' => $product['id'],
    'name' => $product['name'],
    'price' => $product['price'],
    'image' => $product['image'],
    'quantity' => 1,
  ];
}

$_SESSION['cart'] = $cart;

header('Location: cart.php');
exit;

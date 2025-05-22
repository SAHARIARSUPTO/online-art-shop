<?php
session_start();

if (!isset($_GET['id'])) {
  header('Location: cart.php');
  exit;
}

$id = (int)$_GET['id'];

$cart = $_SESSION['cart'] ?? [];

if (isset($cart[$id])) {
  unset($cart[$id]);
  $_SESSION['cart'] = $cart;
}

header('Location: cart.php');
exit;

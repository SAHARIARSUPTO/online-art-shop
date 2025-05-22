<?php
session_start();

if (!isset($_GET['id']) || !isset($_GET['action'])) {
  header("Location: cart.php");
  exit;
}

$id = $_GET['id'];
$action = $_GET['action'];

if (!isset($_SESSION['cart'][$id])) {
  header("Location: cart.php");
  exit;
}

if ($action === 'increase') {
  $_SESSION['cart'][$id]['quantity'] += 1;
} elseif ($action === 'decrease') {
  if ($_SESSION['cart'][$id]['quantity'] > 1) {
    $_SESSION['cart'][$id]['quantity'] -= 1;
  } else {
    unset($_SESSION['cart'][$id]); // auto remove if qty is 1 and user clicks "-"
  }
}

header("Location: cart.php");
exit;

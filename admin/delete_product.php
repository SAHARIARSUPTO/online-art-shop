<?php
session_start();
require '../db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die('Invalid product ID.');
}

$productId = intval($_GET['id']);

// Fetch the product to delete its image file too
$stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
  die('Product not found.');
}

// Delete the image file
$imagePath = '../images/' . $product['image'];
if (file_exists($imagePath)) {
  unlink($imagePath); // delete the file
}

// Delete the product from the database
$deleteStmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$deleteStmt->execute([$productId]);

// Redirect back to the admin products page
header("Location: admin_products.php");
exit;

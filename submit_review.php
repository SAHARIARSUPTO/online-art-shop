<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    $user_name = $_POST['user_name'] ?? '';
    $rating = $_POST['rating'] ?? '';
    $review = $_POST['review'] ?? '';

    if ($product_id && $user_name && $rating && $review) {
        $stmt = $pdo->prepare("INSERT INTO reviews (product_id, user_name, rating, review, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$product_id, $user_name, $rating, $review]);

        // Redirect back to product page
        header("Location: product.php?id=" . $product_id);
        exit;
    } else {
        echo "Missing data. Please fill in all fields.";
    }
} else {
    echo "Invalid request method.";
}
?>

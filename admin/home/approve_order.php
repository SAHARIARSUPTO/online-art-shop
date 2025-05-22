<?php
include '../auth.php';
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $orderId = intval($_POST['order_id']);

    // Update order status to approved
    $updateSql = "UPDATE orders SET status='approved' WHERE id = $orderId";
    if ($conn->query($updateSql) === TRUE) {
        // Fetch order email to send mail
        $orderSql = "SELECT email FROM orders WHERE id = $orderId";
        $orderResult = $conn->query($orderSql);
        if ($orderResult->num_rows > 0) {
            $order = $orderResult->fetch_assoc();
            $to = $order['email'];
            $subject = "Your Order #$orderId has been Approved!";
            $message = "Hey! Your order #$orderId has been approved and is being processed. Thanks for shopping with us!";
            $headers = "From: no-reply@yourartstore.com";

            mail($to, $subject, $message, $headers);
        }
        header("Location: home/dashboard.php?approved=1");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
} else {
    header("Location: home/dashboard.php");
    exit;
}
?>

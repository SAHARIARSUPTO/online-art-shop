<?php
include '../auth.php';
include '../../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['order_id'])) {
    $orderId = (int)$_POST['order_id'];

    // Delete
    if (isset($_POST['delete_order'])) {
      $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
      $stmt->execute([$orderId]);
    }

    // Update
    if (isset($_POST['update_order'])) {
      $name = $_POST['customer_name'] ?? '';
      $email = $_POST['email'] ?? '';
      $phone = $_POST['phone'] ?? '';
      $address = $_POST['address'] ?? '';
      $total = floatval($_POST['total'] ?? 0);
      $isApproved = ($_POST['is_approved'] === '1') ? 1 : 0;

      $stmt = $pdo->prepare("UPDATE orders SET customer_name=?, email=?, phone=?, address=?, total=?, is_approved=? WHERE id=?");
      $stmt->execute([$name, $email, $phone, $address, $total, $isApproved, $orderId]);
    }

    header("Location: orders.php");
    exit;
  }
}

// Fetch all orders
$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>All Orders - Admin Panel</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6 font-sans">
  <h1 class="text-3xl font-bold mb-6">All Orders</h1>
  <a href="dashboard.php" class="inline-block mb-6 text-green-600 hover:underline">← Back to Dashboard</a>

  <table class="min-w-full bg-white rounded shadow overflow-hidden">
    <thead class="bg-gray-100">
      <tr>
        <th class="text-left py-2 px-4">ID</th>
        <th class="text-left py-2 px-4">Name</th>
        <th class="text-left py-2 px-4">Email</th>
        <th class="text-left py-2 px-4">Phone</th>
        <th class="text-left py-2 px-4">Address</th>
        <th class="text-left py-2 px-4">Total</th>
        <th class="text-left py-2 px-4">Status</th>
        <th class="text-left py-2 px-4">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($orders as $order): ?>
      <tr class="border-t">
        <form method="POST">
          <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
          <td class="py-2 px-4"><?= $order['id'] ?></td>
          <td class="py-2 px-4">
            <input type="text" name="customer_name" value="<?= htmlspecialchars($order['customer_name']) ?>" class="border px-2 py-1 rounded w-full">
          </td>
          <td class="py-2 px-4">
            <input type="email" name="email" value="<?= htmlspecialchars($order['email']) ?>" class="border px-2 py-1 rounded w-full">
          </td>
          <td class="py-2 px-4">
            <input type="text" name="phone" value="<?= htmlspecialchars($order['phone']) ?>" class="border px-2 py-1 rounded w-full">
          </td>
          <td class="py-2 px-4">
            <input type="text" name="address" value="<?= htmlspecialchars($order['address']) ?>" class="border px-2 py-1 rounded w-full">
          </td>
          <td class="py-2 px-4">
            <input type="number" step="0.01" name="total" value="<?= number_format($order['total'], 2) ?>" class="border px-2 py-1 rounded w-24">
          </td>
          <td class="py-2 px-4">
            <select name="is_approved" class="border rounded px-2 py-1">
              <option value="0" <?= $order['is_approved'] == 0 ? 'selected' : '' ?>>Pending</option>
              <option value="1" <?= $order['is_approved'] == 1 ? 'selected' : '' ?>>Approved</option>
            </select>
          </td>
          <td class="py-2 px-4 flex gap-2">
            <button type="submit" name="update_order" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Update</button>
            <button type="submit" name="delete_order" onclick="return confirm('Delete this order?')" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Delete</button>
          </td>
        </form>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($orders)): ?>
      <tr>
        <td colspan="8" class="text-center py-4 text-gray-500">No orders found.</td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>

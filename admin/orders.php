<?php
include 'auth.php';
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['order_id'])) {
    $orderId = (int)$_POST['order_id'];

    if (isset($_POST['delete_order'])) {
      $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
      $stmt->execute([$orderId]);
    }

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

$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll();

$stmtItems = $pdo->query("SELECT * FROM order_items");
$itemsRaw = $stmtItems->fetchAll();

$orderItems = [];
foreach ($itemsRaw as $item) {
  $orderItems[$item['order_id']][] = $item;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>All Orders - Admin Panel</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#10B981',
            secondary: '#F0FDF4',
          },
        },
      },
    };
  </script>
  <style>
    @media (max-width: 768px) {
      .order-row td {
        display: block;
        width: 100%;
        padding: 0.5rem;
      }
      .order-row td:before {
        content: attr(data-label);
        font-weight: bold;
        display: inline-block;
        width: 120px;
      }
      .order-row {
        display: block;
        margin-bottom: 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 1rem;
      }
      .order-actions {
        flex-direction: column;
        gap: 0.5rem;
      }
      .order-actions button {
        width: 100%;
      }
    }
  </style>
</head>

<body class="bg-secondary min-h-screen font-sans text-gray-800">
  <header class="bg-green-600 text-white p-4 md:p-6 shadow-md sticky top-0 z-10">
    <h1 class="text-xl md:text-3xl font-extrabold tracking-tight">Order Product Management Dashboard</h1>
  </header>

  <main class="p-4">
    <h1 class="text-xl md:text-2xl font-semibold mb-4 mt-2 text-primary">🧾 All Orders</h1>

    <div class="overflow-x-auto">
      <table class="w-full bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <thead class="bg-primary/10 text-primary uppercase text-sm tracking-wider hidden md:table-header-group">
          <tr>
            <th class="text-left py-3 px-4">ID</th>
            <th class="text-left py-3 px-4">Customer Name</th>
            <th class="text-left py-3 px-4">Email</th>
            <th class="text-left py-3 px-4">Phone</th>
            <th class="text-left py-3 px-4">Address</th>
            <th class="text-left py-3 px-4">Total</th>
            <th class="text-left py-3 px-4">Status</th>
            <th class="text-left py-3 px-4">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $order): ?>
          <tr class="border-t hover:bg-primary/5 transition-colors duration-200 md:table-row flex flex-col md:table-row">
            <form method="POST" class="contents">
              <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
              <td data-label="ID" class="order-row py-2 px-4 md:table-cell"><?= $order['id'] ?></td>
              <td data-label="Customer Name" class="order-row py-2 px-4 md:table-cell">
                <input type="text" name="customer_name" value="<?= htmlspecialchars($order['customer_name']) ?>" class="border border-gray-300 px-2 py-1 rounded w-full">
              </td>
              <td data-label="Email" class="order-row py-2 px-4 md:table-cell">
                <input type="email" name="email" value="<?= htmlspecialchars($order['email']) ?>" class="border border-gray-300 px-2 py-1 rounded w-full">
              </td>
              <td data-label="Phone" class="order-row py-2 px-4 md:table-cell">
                <input type="text" name="phone" value="<?= htmlspecialchars($order['phone']) ?>" class="border border-gray-300 px-2 py-1 rounded w-full">
              </td>
              <td data-label="Address" class="order-row py-2 px-4 md:table-cell">
                <input type="text" name="address" value="<?= htmlspecialchars($order['address']) ?>" class="border border-gray-300 px-2 py-1 rounded w-full">
              </td>
              <td data-label="Total" class="order-row py-2 px-4 md:table-cell">
                <input type="number" step="0.01" name="total" value="<?= number_format($order['total'], 2) ?>" class="border border-gray-300 px-2 py-1 rounded md:w-24 w-full">
              </td>
              <td data-label="Status" class="order-row py-2 px-4 md:table-cell">
                <select name="is_approved" class="border border-gray-300 rounded px-2 py-1 w-full">
                  <option value="0" <?= $order['is_approved'] == 0 ? 'selected' : '' ?>>Pending</option>
                  <option value="1" <?= $order['is_approved'] == 1 ? 'selected' : '' ?>>Approved</option>
                </select>
              </td>
              <td class="order-row py-2 px-4 flex gap-2 order-actions">
                <button type="submit" name="update_order" class="bg-primary text-white px-4 py-2 rounded-xl hover:bg-green-700 transition-all shadow md:w-auto w-full">
                  Update
                </button>
                <button type="submit" name="delete_order" onclick="return confirm('Delete this order?')" class="bg-red-500 text-white px-4 py-2 rounded-xl hover:bg-red-600 transition-all shadow md:w-auto w-full">
                  Delete
                </button>
              </td>
            </form>
          </tr>

          <?php if (!empty($orderItems[$order['id']])): ?>
          <tr class="bg-primary/5 border-t border-b">
            <td colspan="8" class="px-4 py-3 text-sm text-gray-700">
              <div class="mb-1 font-semibold">📦 Products in Order:</div>
              <ul class="list-disc pl-6 space-y-1 text-gray-600">
                <?php foreach ($orderItems[$order['id']] as $item): ?>
                  <li class="break-words">
                    <?= htmlspecialchars($item['product_name']) ?>
                    — Qty: <?= $item['quantity'] ?>
                    — Price: ৳<?= number_format($item['price'], 2) ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            </td>
          </tr>
          <?php endif; ?>
          <?php endforeach; ?>

          <?php if (empty($orders)): ?>
          <tr>
            <td colspan="8" class="text-center py-4 text-gray-500">No orders found.</td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
  <div class="m-4 md:m-10 flex justify-center">
    <a href="dashboard.php" class="inline-block bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition font-semibold">
      ← Back to Dashboard
    </a>
  </div>
</body>
</html>
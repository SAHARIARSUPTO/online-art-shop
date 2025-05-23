<?php
session_start();
include 'auth.php';  // auth check
include '../db.php';  // PDO connection

// Handle approve/unapprove action
if (isset($_GET['action'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'approve') {
        $stmt = $pdo->prepare("UPDATE testimonials SET approved = 1 WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($_GET['action'] === 'unapprove') {
        $stmt = $pdo->prepare("UPDATE testimonials SET approved = 0 WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($_GET['action'] === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
        $stmt->execute([$id]);
    }
    header('Location: testimonials.php');
    exit;
}

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_testimonial'])) {
    $id = (int)$_POST['testimonial_id'];
    $user_name = trim($_POST['user_name']);
    $testimonial = trim($_POST['testimonial']);
    if ($user_name !== '' && $testimonial !== '') {
        $stmt = $pdo->prepare("UPDATE testimonials SET user_name = ?, testimonial = ? WHERE id = ?");
        $stmt->execute([$user_name, $testimonial, $id]);
        $success_message = "Testimonial updated successfully.";
    } else {
        $error_message = "Both fields are required.";
    }
}

// Fetch all testimonials
$stmt = $pdo->query("SELECT * FROM testimonials ORDER BY created_at DESC");
$testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>Manage Testimonials - Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
  @media (max-width: 768px) {
    .testimonial-card {
      display: block;
      border: 1px solid #e5e7eb;
      border-radius: 0.5rem;
      padding: 1rem;
      margin-bottom: 1rem;
    }
    .testimonial-card td {
      display: block;
      padding: 0.25rem 0;
      width: 100%;
    }
    .testimonial-card td:before {
      content: attr(data-label);
      font-weight: bold;
      display: inline-block;
      width: 100px;
    }
    .testimonial-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      margin-top: 0.5rem;
    }
    .testimonial-actions a, .testimonial-actions button {
      flex: 1 1 120px;
      text-align: center;
      padding: 0.5rem;
      border-radius: 0.25rem;
    }
    .table-header {
      display: none;
    }
    .testimonial-text {
      white-space: normal;
      overflow: visible;
      text-overflow: clip;
    }
  }
</style>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

<header class="bg-green-600 text-white p-4 md:p-6 shadow-md sticky top-0 z-10">
  <h1 class="text-xl md:text-3xl font-extrabold tracking-tight">Manage Testimonials</h1>
</header>

<div class="max-w-7xl mx-auto p-4 md:p-6">

  <?php if (!empty($success_message)): ?>
    <p class="bg-green-200 text-green-800 p-3 rounded mb-4 max-w-3xl mx-auto text-center"><?= htmlspecialchars($success_message) ?></p>
  <?php endif; ?>
  <?php if (!empty($error_message)): ?>
    <p class="bg-red-200 text-red-800 p-3 rounded mb-4 max-w-3xl mx-auto text-center"><?= htmlspecialchars($error_message) ?></p>
  <?php endif; ?>

  <div class="overflow-x-auto">
    <table class="min-w-full bg-white shadow rounded-lg overflow-hidden max-w-5xl mx-auto">
      <thead class="bg-green-600 text-white table-header">
        <tr>
          <th class="px-4 md:px-6 py-3 text-left">ID</th>
          <th class="px-4 md:px-6 py-3 text-left">User Name</th>
          <th class="px-4 md:px-6 py-3 text-left">Testimonial</th>
          <th class="px-4 md:px-6 py-3 text-center">Approved</th>
          <th class="px-4 md:px-6 py-3 text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($testimonials as $t): ?>
          <tr class="border-b hover:bg-green-50 testimonial-card">
            <td data-label="ID" class="px-4 md:px-6 py-4"><?= $t['id'] ?></td>
            <td data-label="User" class="px-4 md:px-6 py-4 max-w-xs testimonial-text"><?= htmlspecialchars($t['user_name']) ?></td>
            <td data-label="Testimonial" class="px-4 md:px-6 py-4 max-w-xl testimonial-text"><?= nl2br(htmlspecialchars($t['testimonial'])) ?></td>
            <td data-label="Approved" class="px-4 md:px-6 py-4 text-center">
              <?= $t['approved'] ? 
                '<span class="text-green-600 font-semibold">Yes</span>' : 
                '<span class="text-red-600 font-semibold">No</span>' ?>
            </td>
            <td class="px-4 md:px-6 py-4 text-center testimonial-actions">
              <?php if (!$t['approved']): ?>
                <a href="?action=approve&id=<?= $t['id'] ?>" class="bg-green-100 text-green-800 hover:bg-green-200 font-semibold">Approve</a>
              <?php else: ?>
                <a href="?action=unapprove&id=<?= $t['id'] ?>" class="bg-yellow-100 text-yellow-800 hover:bg-yellow-200 font-semibold">Unapprove</a>
              <?php endif; ?>
              <button onclick="openEditModal(<?= htmlspecialchars(json_encode($t['id'])) ?>, '<?= htmlspecialchars(addslashes($t['user_name'])) ?>', '<?= htmlspecialchars(addslashes($t['testimonial'])) ?>')" class="bg-blue-100 text-blue-800 hover:bg-blue-200 font-semibold">Edit</button>
              <a href="?action=delete&id=<?= $t['id'] ?>" onclick="return confirm('Delete this testimonial?')" class="bg-red-100 text-red-800 hover:bg-red-200 font-semibold">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (count($testimonials) === 0): ?>
          <tr>
            <td colspan="5" class="text-center p-6 text-gray-500">No testimonials found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="m-10 flex justify-center">
  <a href="dashboard.php" class="inline-block bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition font-semibold">
    ← Back to Dashboard
  </a>
</div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
  <div class="bg-white rounded-lg w-full max-w-md p-4 md:p-6 relative">
    <h2 class="text-xl md:text-2xl font-semibold mb-4">Edit Testimonial</h2>
    <form method="POST" class="space-y-4">
      <input type="hidden" name="testimonial_id" id="testimonial_id" />
      <div>
        <label for="user_name" class="block font-medium mb-1">User Name</label>
        <input type="text" id="user_name" name="user_name" required class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>
      <div>
        <label for="testimonial" class="block font-medium mb-1">Testimonial</label>
        <textarea id="testimonial" name="testimonial" rows="4" required class="w-full border border-gray-300 rounded px-3 py-2 resize-y"></textarea>
      </div>
      <div class="flex justify-end space-x-4 mt-6">
        <button type="button" onclick="closeEditModal()" class="px-4 py-2 border rounded hover:bg-gray-100">Cancel</button>
        <button type="submit" name="update_testimonial" class="bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700">Save Changes</button>
      </div>
    </form>
    <button onclick="closeEditModal()" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-xl font-bold">&times;</button>
  </div>
</div>

<script>
function openEditModal(id, userName, testimonial) {
  document.getElementById('testimonial_id').value = id;
  document.getElementById('user_name').value = userName;
  document.getElementById('testimonial').value = testimonial;
  document.getElementById('editModal').classList.remove('hidden');
  document.body.style.overflow = 'hidden';
}

function closeEditModal() {
  document.getElementById('editModal').classList.add('hidden');
  document.body.style.overflow = 'auto';
}
</script>

</body>
</html>
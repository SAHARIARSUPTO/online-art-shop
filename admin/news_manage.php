<?php
session_start();
include 'auth.php';
include '../db.php';

// Handle create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
  $title = $_POST['title'];
  $content = $_POST['content'];
  $visible = isset($_POST['visible']) ? 1 : 0;

  $stmt = $pdo->prepare("INSERT INTO news (title, content, visible, created_at) VALUES (?, ?, ?, NOW())");
  $stmt->execute([$title, $content, $visible]);
  header("Location: news_manage.php");
  exit;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
  $id = $_POST['id'];
  $title = $_POST['title'];
  $content = $_POST['content'];
  $visible = isset($_POST['visible']) ? 1 : 0;

  $stmt = $pdo->prepare("UPDATE news SET title = ?, content = ?, visible = ? WHERE id = ?");
  $stmt->execute([$title, $content, $visible, $id]);
  header("Location: news_manage.php");
  exit;
}

// Handle delete
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
  $stmt->execute([$id]);
  header("Location: news_manage.php");
  exit;
}

// Handle toggle visibility
if (isset($_GET['toggle'])) {
  $id = $_GET['toggle'];
  $stmt = $pdo->prepare("UPDATE news SET visible = NOT visible WHERE id = ?");
  $stmt->execute([$id]);
  header("Location: news_manage.php");
  exit;
}

// Handle edit fetch
$editMode = false;
$editNews = null;
if (isset($_GET['edit'])) {
  $editMode = true;
  $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
  $stmt->execute([$_GET['edit']]);
  $editNews = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all news
$stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC");
$newsItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage News</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-4 md:p-8 font-sans">
  <div class="max-w-4xl mx-auto">

    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">📰 Manage News</h1>

    <!-- Form -->
    <div class="bg-white shadow-md rounded-lg p-4 md:p-6 mb-8 md:mb-10">
      <h2 class="text-lg md:text-xl font-semibold mb-4"><?= $editMode ? '✏️ Edit News' : '➕ Add News' ?></h2>
      <form method="POST">
        <?php if ($editMode): ?>
          <input type="hidden" name="id" value="<?= $editNews['id'] ?>">
        <?php endif; ?>

        <div class="mb-4">
          <label class="block mb-1 font-medium">Title</label>
          <input type="text" name="title" required class="w-full p-2 border rounded text-sm md:text-base" value="<?= $editMode ? htmlspecialchars($editNews['title']) : '' ?>">
        </div>
        <div class="mb-4">
          <label class="block mb-1 font-medium">Content</label>
          <textarea name="content" rows="4" required class="w-full p-2 border rounded text-sm md:text-base"><?= $editMode ? htmlspecialchars($editNews['content']) : '' ?></textarea>
        </div>
        <div class="mb-4 flex items-center space-x-2">
          <input type="checkbox" name="visible" id="visible" <?= $editMode && $editNews['visible'] ? 'checked' : '' ?>>
          <label for="visible" class="text-sm md:text-base">Visible to users</label>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
          <button type="submit" name="<?= $editMode ? 'update' : 'create' ?>" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm md:text-base">
            <?= $editMode ? 'Update' : 'Post' ?> News
          </button>
          <?php if ($editMode): ?>
            <a href="news_manage.php" class="text-center text-sm text-gray-500 underline py-2">Cancel edit</a>
          <?php endif; ?>
        </div>
      </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-2 md:px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Title</th>
            <th class="px-2 md:px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase hidden sm:table-cell">Date</th>
            <th class="px-2 md:px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase hidden sm:table-cell">Status</th>
            <th class="px-2 md:px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php foreach ($newsItems as $news): ?>
            <tr>
              <td class="px-2 md:px-4 py-4 text-sm md:text-base"><?= htmlspecialchars($news['title']) ?></td>
              <td class="px-2 md:px-4 py-4 text-sm hidden sm:table-cell"><?= date('M d, Y', strtotime($news['created_at'])) ?></td>
              <td class="px-2 md:px-4 py-4 text-sm hidden sm:table-cell">
                <?= $news['visible'] ? '<span class="text-green-600 font-semibold">Visible</span>' : '<span class="text-red-500 font-semibold">Hidden</span>' ?>
              </td>
              <td class="px-2 md:px-4 py-4 text-right space-x-1 md:space-x-2">
                <a href="?edit=<?= $news['id'] ?>" class="text-blue-600 hover:underline text-xs md:text-sm">Edit</a>
                <a href="?delete=<?= $news['id'] ?>" class="text-red-600 hover:underline text-xs md:text-sm" onclick="return confirm('Delete this news post?')">Delete</a>
                <a href="?toggle=<?= $news['id'] ?>" class="text-gray-600 hover:underline text-xs md:text-sm"><?= $news['visible'] ? 'Hide' : 'Show' ?></a>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (count($newsItems) === 0): ?>
            <tr><td colspan="4" class="text-center py-4 text-gray-500 text-sm md:text-base">No news posted yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
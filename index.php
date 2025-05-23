<?php
session_start();
require 'db.php';

// Handle testimonial submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['testimonial_submit'])) {
    $user_name = trim($_POST['user_name']);
    $testimonial = trim($_POST['testimonial']);

    if ($user_name !== '' && $testimonial !== '') {
        $stmt = $pdo->prepare("INSERT INTO testimonials (user_name, testimonial) VALUES (?, ?)");
        if ($stmt->execute([$user_name, $testimonial])) {
            $_SESSION['success_message'] = "Thanks for your feedback! It will show up after approval.";
        } else {
            $_SESSION['error_message'] = "There was an error submitting your feedback.";
        }
    } else {
        $_SESSION['error_message'] = "Please fill out both fields.";
    }

    // Redirect to prevent form resubmission on refresh
    header('Location: index.php#testimonial-form');
    exit();
}
$stmt = $pdo->query("SELECT * FROM news WHERE visible = 1 ORDER BY created_at DESC LIMIT 5");
$news_items = $stmt->fetchAll();
// Fetch products (if you already do this somewhere else, keep your existing one)
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll();

// Fetch approved testimonials
$stmt = $pdo->query("SELECT * FROM testimonials WHERE approved = 1 ORDER BY created_at DESC");
$approved_testimonials = $stmt->fetchAll();

// Retrieve messages from session and clear them
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

$error_message = '';
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Online Art Store</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#22c55e',
          }
        }
      }
    };
  </script>
  <style>
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 15px -3px rgba(34,197,94,0.4), 0 4px 6px -2px rgba(34,197,94,0.1);
    }
    .transition-smooth {
      transition: all 0.3s ease-in-out;
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans leading-relaxed">

  <header class="bg-primary shadow-md">
    <div class="max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
      <a href="index.php">
        <h1 class="text-2xl font-bold text-white">Online Art Store</h1>
      </a>
      <nav class="space-x-4">
        <a href="index.php" class="text-white hover:underline">Home</a>
        <a href="shop.php" class="text-white hover:underline">Shop</a>
        <a href="cart.php" class="bg-white text-primary px-4 py-2 rounded hover:bg-gray-100 font-medium">🛒 Cart</a>
      </nav>
    </div>
  </header>
<?php if (count($news_items) > 0): ?>
  <?php $latest_news = $news_items[0]; ?>
  <section class="bg-yellow-100 text-yellow-900 py-2 shadow-inner border-t border-b border-yellow-300">
    <div class="max-w-6xl mx-auto px-4 flex items-center gap-3 overflow-hidden">
      <h2 class="font-semibold text-yellow-800 whitespace-nowrap">Latest News:</h2>
      <div class="relative overflow-hidden w-full">
        <div class="animate-scroll whitespace-nowrap text-sm">
          <strong><?= htmlspecialchars($latest_news['title']) ?>:</strong>
          <?= htmlspecialchars($latest_news['content']) ?>
          <span class="text-gray-500">(<?= date("M d, Y", strtotime($latest_news['created_at'])) ?>)</span>
        </div>
      </div>
    </div>
    <style>
  @keyframes scroll {
    0% { transform: translateX(100%); }
    100% { transform: translateX(-100%); }
  }

  .animate-scroll {
    animation: scroll 20s linear infinite;
  }
</style>

  </section>
<?php endif; ?>


  <section class="w-full">
    <div class="relative h-[400px] sm:h-[500px] bg-cover bg-center" style="background-image: url('https://st4.depositphotos.com/5586578/27473/i/450/depositphotos_274739766-stock-photo-artist-essential-tools-wooden-palette.jpg');">
      <div class="absolute inset-0 bg-black/40 flex flex-col justify-center items-center text-center text-white">
        <h2 class="text-3xl sm:text-5xl font-bold mb-4">Welcome to the Art World</h2>
        <p class="text-lg sm:text-xl mb-6 max-w-2xl">Discover unique paintings from talented artists across the globe</p>
        <a href="shop.php" class="bg-primary px-6 py-3 rounded font-semibold hover:bg-green-700 transition">🎨 Shop Now</a>
      </div>
    </div>
  </section>

  <section class="max-w-5xl mx-auto px-6 py-16">
    <h2 class="text-3xl font-bold text-center mb-6">About Us</h2>
    <p class="text-gray-700 text-center max-w-3xl mx-auto">
      At Online Art Store, we bring exclusive, handpicked paintings to art lovers. Whether you’re decorating your home, gifting a piece, or just Browse, our platform connects you with high-quality, creative artwork — all in one place.
    </p>
  </section>

  <main class="max-w-7xl mx-auto px-6 py-12">
    <?php if (count($products) > 0): ?>
      <section class="grid gap-10 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($products as $product): ?>
          <article class="card bg-white rounded-xl shadow-md overflow-hidden transition-smooth">
            <a href="product.php?id=<?= htmlspecialchars($product['id']) ?>">
              <img
                src="images/<?= htmlspecialchars($product['image']) ?>"
                alt="<?= htmlspecialchars($product['name']) ?>"
                class="w-full h-64 object-cover object-center"
                loading="lazy"
              />
            </a>

            <div class="p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-2"><?= htmlspecialchars($product['name']) ?></h2>
              <p class="text-gray-600 text-sm mb-4"><?= htmlspecialchars($product['description']) ?></p>
              <p class="text-green-700 font-bold text-lg mb-4">$<?= number_format((float)$product['price'], 2) ?></p>
              <form action="add_to_cart.php" method="GET" class="text-center">
                <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>" />
                <button
                  type="submit"
                  class="bg-primary text-white px-6 py-2 rounded-md font-semibold hover:bg-green-700 transition"
                >
                  ➕ Add to Cart
                </button>
              </form>
            </div>
          </article>
        <?php endforeach; ?>
      </section>
    <?php else: ?>
      <p class="text-center text-red-600 mt-10 text-lg">😕 No products found in the store right now.</p>
    <?php endif; ?>
  </main>

  <section class="bg-white py-16">
    <div class="max-w-6xl mx-auto px-6">
      <h2 class="text-3xl font-bold text-center mb-10">🎨 Explore by Category</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        <div class="bg-green-50 p-6 rounded-lg text-center shadow hover:shadow-md transition">
          <img src="images/abstract.jpg" alt="Abstract Art" class="h-32 mx-auto object-cover rounded mb-4" />
          <h3 class="font-semibold text-lg">Abstract</h3>
        </div>
        <div class="bg-green-50 p-6 rounded-lg text-center shadow hover:shadow-md transition">
          <img src="images/landscape.jpg" alt="Landscape Art" class="h-32 mx-auto object-cover rounded mb-4" />
          <h3 class="font-semibold text-lg">Landscape</h3>
        </div>
        <div class="bg-green-50 p-6 rounded-lg text-center shadow hover:shadow-md transition">
          <img src="images/portrait.jpg" alt="Portrait Art" class="h-32 mx-auto object-cover rounded mb-4" />
          <h3 class="font-semibold text-lg">Portrait</h3>
        </div>
        <div class="bg-green-50 p-6 rounded-lg text-center shadow hover:shadow-md transition">
          <img src="images/minimal.jpg" alt="Minimal Art" class="h-32 mx-auto object-cover rounded mb-4" />
          <h3 class="font-semibold text-lg">Minimalist</h3>
        </div>
      </div>
    </div>
  </section>

  <section class="bg-gray-50 py-16 border-t border-b">
    <div class="max-w-6xl mx-auto px-6 text-center">
      <h2 class="text-3xl font-bold mb-10">💡 Why Choose Us?</h2>
      <div class="grid gap-10 sm:grid-cols-3">
        <div>
          <div class="text-4xl mb-2">🖌️</div>
          <h3 class="font-bold text-lg mb-2">Curated Collection</h3>
          <p class="text-gray-600">Every artwork is handpicked by our curators to ensure quality and uniqueness.</p>
        </div>
        <div>
          <div class="text-4xl mb-2">🚚</div>
          <h3 class="font-bold text-lg mb-2">Fast Worldwide Delivery</h3>
          <p class="text-gray-600">We ship across the globe with secure packaging and real-time tracking.</p>
        </div>
        <div>
          <div class="text-4xl mb-2">🔒</div>
          <h3 class="font-bold text-lg mb-2">Secure Payments</h3>
          <p class="text-gray-600">All transactions are encrypted and protected using SSL certificates.</p>
        </div>
      </div>
    </div>
  </section>


  <section class="bg-primary text-white py-16 text-center">
    <div class="max-w-2xl mx-auto px-6">
      <h2 class="text-3xl font-bold mb-4">🧾 Stay in the loop!</h2>
      <p class="mb-6">Subscribe for new arrivals, art trends, and special discounts delivered to your inbox.</p>
      <form class="flex flex-col sm:flex-row gap-4 justify-center" action="index.php" method="POST">
        <input
          type="email"
          name="email"
          placeholder="Enter your email"
          class="px-4 py-2 rounded text-gray-800 w-full sm:w-auto"
          required
        />
        <button type="submit" class="bg-white text-primary px-6 py-2 rounded font-semibold hover:bg-gray-100">Subscribe</button>
      </form>
    </div>
  </section>

  <section class="max-w-4xl mx-auto px-6 py-16">
    <h2 class="text-2xl font-bold mb-6 text-center">FAQs</h2>
    <div class="space-y-4">
      <details class="bg-white rounded-lg shadow p-4">
        <summary class="font-semibold cursor-pointer">How do I purchase a painting?</summary>
        <p class="mt-2 text-gray-600">Simply click on "Add to Cart", then proceed to checkout from your cart page.</p>
      </details>
      <details class="bg-white rounded-lg shadow p-4">
        <summary class="font-semibold cursor-pointer">Do you ship internationally?</summary>
        <p class="mt-2 text-gray-600">Yes, we ship worldwide. Shipping charges may vary based on your location.</p>
      </details>
      <details class="bg-white rounded-lg shadow p-4">
        <summary class="font-semibold cursor-pointer">Can I return a painting?</summary>
        <p class="mt-2 text-gray-600">Returns are accepted within 7 days of delivery if the item is damaged or not as described.</p>
      </details>
    </div>
  </section>
<section class="bg-white py-16 max-w-4xl mx-auto px-6 rounded-lg shadow-lg mt-20">
    <h2 class="text-3xl font-bold text-center mb-10">💬 What Our Customers Say</h2>

    <div class="space-y-6 mb-16">
      <?php if (count($approved_testimonials) > 0): ?>
        <?php foreach ($approved_testimonials as $t): ?>
          <blockquote class="border-l-4 border-green-500 pl-4 italic text-gray-700 bg-green-50 p-4 rounded">
            <?= nl2br(htmlspecialchars($t['testimonial'])) ?>
            <br><span class="font-semibold block mt-2 text-right">— <?= htmlspecialchars($t['user_name']) ?></span>
          </blockquote>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-center text-gray-500">No testimonials yet. Be the first to leave your feedback!</p>
      <?php endif; ?>
    </div>

    <h3 class="text-2xl font-semibold mb-6 text-center">Leave Us Your Feedback</h3>
    <?php if (!empty($success_message)): ?>
      <p class="text-green-600 text-center mb-6"><?= $success_message ?></p>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
      <p class="text-red-600 text-center mb-6"><?= $error_message ?></p>
    <?php endif; ?>
    <form action="index.php#testimonial-form" method="POST" id="testimonial-form" class="max-w-xl mx-auto space-y-6">
      <div>
        <label for="user_name" class="block mb-2 font-medium text-gray-700">Your Name</label>
        <input type="text" id="user_name" name="user_name" required class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" />
      </div>
      <div>
        <label for="testimonial" class="block mb-2 font-medium text-gray-700">Your Feedback</label>
        <textarea id="testimonial" name="testimonial" rows="4" required class="w-full border border-gray-300 rounded-md px-4 py-2 resize-none focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
      </div>
      <div class="text-center">
        <button type="submit" name="testimonial_submit" class="bg-primary text-white px-8 py-3 rounded-md font-semibold hover:bg-green-700 transition">
          Submit Feedback
        </button>
      </div>
    </form>
  </section>

  <footer class="bg-gray-100 text-center text-gray-500 text-sm py-6 border-t border-gray-200">
    &copy; <?= date('Y') ?> Online Art Store. All rights reserved.
  </footer>

</body>
</html>
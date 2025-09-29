<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must login to view and purchase products!'); window.location.href='login.php';</script>";
    exit();
}

/* ✅ Build dynamic WHERE clause for search or brand filter */
$where = "";
if (!empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where = "WHERE name LIKE '%$search%' OR brand LIKE '%$search%'";
} elseif (!empty($_GET['brand'])) {
    $brand = $conn->real_escape_string($_GET['brand']);
    $where = "WHERE brand = '$brand'";
}

$sql = "SELECT * FROM products $where ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mobile Store</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <!-- HEADER -->
  <header class="header">
    <div class="logo">📱 Mobile Store</div>
    <nav class="navbar">
      <a href="index.php">Home</a>
      <a href="about.php">About</a>
      <a href="contact.php">Contact</a>
      <a href="login.php">Login</a>
      <a href="signup.php">Signup</a>
      <a href="cart.php">🛒 Cart</a>
    </nav>
  </header>

  <!-- HERO SECTION -->
  <section class="hero">
    <h1>Welcome to Mobile Store</h1>
    <p>Discover the latest smartphones at the best prices.</p>
    <form class="search-bar" method="GET" action="index.php">
      <input type="text" name="search" placeholder="Search mobiles..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
      <button type="submit">Search</button>
    </form>
  </section>

  <!-- CATEGORIES -->
  <section class="categories">
    <h2>Shop by Brand</h2>
    <div class="category-grid">
      <a class="category-card" href="index.php?brand=Apple">Apple</a>
      <a class="category-card" href="index.php?brand=Samsung">Samsung</a>
      <a class="category-card" href="index.php?brand=OnePlus">OnePlus</a>
      <a class="category-card" href="index.php?brand=Xiaomi">Xiaomi</a>
      <a class="category-card" href="index.php?brand=Realme">Realme</a>
    </div>
  </section>

  <!-- PRODUCT SECTION (Dynamic from DB) -->
  <section id="products" class="products">
    <h2>
      <?php
        if (!empty($_GET['search'])) {
            echo "Search Results for \"" . htmlspecialchars($_GET['search']) . "\"";
        } elseif (!empty($_GET['brand'])) {
            echo htmlspecialchars($_GET['brand']) . " Mobiles";
        } else {
            echo "Featured Mobiles";
        }
      ?>
    </h2>
    <div class="product-grid">
      <?php
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        echo "<div class='product-card'>";

        // 🔗 Make the image + name clickable
        echo "<a href='product_details.php?id=" . $row['id'] . "' style='text-decoration:none; color:inherit;'>";
        echo "<img src='images/" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['name']) . "'>";
        echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
        echo "</a>";

        echo "<p>₹" . htmlspecialchars($row['price']) . "</p>";

        // 🔑 Show Add to Cart only if logged in
        if (isset($_SESSION['user_id'])) {
            echo "<form method='POST' action='add_to_cart.php'>
                    <input type='hidden' name='product_id' value='" . $row['id'] . "'>
                    <button type='submit' class='btn'>Add to Cart</button>
                  </form>";
        } else {
            echo "<button class='btn' disabled style='background: gray; cursor: not-allowed;'>Login to Purchase</button>";
        }

        echo "</div>";
    }
} else {
    echo "<p>No products available.</p>";
}
?>

    </div>
  </section>

  <!-- NEWSLETTER -->
  <section class="newsletter">
    <h2>Stay Updated</h2>
    <p>Subscribe to get the latest offers and new arrivals directly in your inbox.</p>
    <form>
      <input type="email" placeholder="Enter your email">
      <button type="submit">Subscribe</button>
    </form>
  </section>

  <!-- FOOTER -->
  <footer class="footer">
    <p>&copy; <?php echo date("Y"); ?> Mobile Store. All rights reserved.</p>
  </footer>
  
</body>
</html>

<?php
session_start();
include 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    echo "<p>Product not found.</p>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($product['name']); ?> - Mobile Store</title>
<link rel="stylesheet" href="styles.css">
<style>
.details-container {
    max-width: 800px; margin:40px auto; padding:20px;
    border:1px solid #ddd; text-align:center;
}
.details-container img { max-width:300px; margin-bottom:20px; }
.btn { padding:10px 20px; background:#007BFF; color:#fff; border:none; border-radius:4px; cursor:pointer; margin:10px; }
.btn:hover { background:#0056b3; }
</style>
</head>
<body>
<header class="header">
  <div class="logo">📱 Mobile Store</div>
  <nav class="navbar">
    <a href="index.php">Home</a>
    <a href="cart.php">🛒 Cart</a>
  </nav>
</header>

<div class="details-container">
  <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
  <h1><?php echo htmlspecialchars($product['name']); ?></h1>
  <p><strong>Brand:</strong> <?php echo htmlspecialchars($product['brand']); ?></p>
  <p><strong>Price:</strong> ₹<?php echo htmlspecialchars($product['price']); ?></p>
  <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

  <?php if (isset($_SESSION['user_id'])): ?>
      <!-- Add to Cart -->
      <form method="POST" action="add_to_cart.php" style="display:inline;">
          <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
          <button type="submit" class="btn">Add to Cart</button>
      </form>

      <!-- Buy Now (adds to cart then redirects) -->
      <form method="POST" action="add_to_cart.php" style="display:inline;">
          <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
          <input type="hidden" name="buy_now" value="1">
          <button type="submit" class="btn">Buy Now</button>
      </form>
  <?php else: ?>
      <p><a href="login.php" class="btn">Login to Purchase</a></p>
  <?php endif; ?>
</div>
</body>
</html>

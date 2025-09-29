<?php
session_start();
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Shopping Cart - Mobile Store</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

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

<main>
  <h1>Your Shopping Cart</h1>

  <div class="cart-container">
    <?php
    if (!isset($_SESSION['user_id'])) {
        echo "<p>Your cart is empty. <a href='login.php'>Login</a> to add items.</p>";
    } else {
        $user_id = $_SESSION['user_id'];

        // fetch cart items with product details
        $sql = "SELECT c.id AS id, c.quantity, p.id AS product_id, p.name, p.price, p.image
                FROM cart c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $subtotal = 0;
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // compute PHP-side initial total (display only; JS will recalc)
                $itemTotal = $row['price'] * $row['quantity'];
                $subtotal += $itemTotal;
                ?>
                <?php error_log("id {$row['id']} qty {$row['quantity']}", 3, __DIR__ . "/cart_debug.log"); ?>
                <div class="cart-item" data-id="<?php echo $row['id']; ?>">
                  <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                  <div class="item-details">
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>

                    <!-- unit price MUST be numeric in data-price (no commas, no currency) -->
                    <p class="unit-price" data-price="<?php echo floatval($row['price']); ?>">
                      ₹<?php echo number_format($row['price']); ?>
                    </p>

                    <div class="quantity">
                      <form action="update_cart.php" method="post" class="update-form">
                        <input type="hidden" name="id" value="<?= $row['quantity'] ?>">    
                        <button type="button" class="minus-btn">-</button>
                        <input type="number" name="quantity" class = "quantity" value="<?php echo $row['quantity']; ?>" min="1">
                        <button type="button" class="plus-btn">+</button>
                        <button type="submit" style="display:none;">Save</button>
                      </form>
                    </div>
                  </div>

                  <!-- item total — will be recalculated by JS on page load -->
                  <div class="item-total">₹<?php echo number_format($row['price'] * $row['quantity']); ?></div>

                  <form action="remove_from_cart.php" method="post" class="remove-form">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button type="submit" class="remove-btn">Remove</button>
                  </form>
                </div>
                <?php
            } // end while
            ?>
            <div class="cart-summary">
              <h2>Cart Summary</h2>
              <p>Subtotal: <strong>₹<?php echo number_format($subtotal); ?></strong></p>
              <p>Shipping: <strong>₹500</strong></p>
              <p>Total: <strong>₹<?php echo number_format($subtotal + 500); ?></strong></p>
              <button class="checkout-btn"
        onclick="window.location.href='checkout.php'">
   Proceed to Checkout
</button>

            </div>
            <?php
        } else {
            echo "<p>Your cart is empty.</p>";
        }
    }
    ?>
  </div>
</main>

<footer>
  <p>&copy; <?php echo date("Y"); ?> Mobile Store. All rights reserved.</p>
</footer>

<script src="cart_clean.js"></script>
</body>
</html>

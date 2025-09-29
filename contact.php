<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - Mobile Store</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header class="header">
    <div class="logo">📱 Mobile Store</div>
    <nav class="navbar">
      <a href="index.php">Home</a>
      <a href="about.php">About</a>
      <a href="contact.php" class="active">Contact</a>
      <a href="login.php">Login</a>
      <a href="signup.php">Signup</a>
      <a href="cart.php">🛒 Cart</a>
    </nav>
  </header>

  <section class="page">
    <h1>Contact Us</h1>
    <p>Have any questions? We’d love to hear from you.</p>
    <form class="contact-form">
      <input type="text" placeholder="Your Name" required>
      <input type="email" placeholder="Your Email" required>
      <textarea placeholder="Your Message" required></textarea>
      <button type="submit">Send Message</button>
    </form>
  </section>

  <footer class="footer">
    <p>&copy; <?php echo date("Y"); ?> Mobile Store. All rights reserved.</p>
  </footer>
</body>
</html>

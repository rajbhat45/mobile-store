<?php
session_start();
include 'db.php'; // database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];

  // fetch user
  $stmt = $conn->prepare("SELECT id, password FROM users WHERE email=? LIMIT 1");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // verify password (change to === if you didn’t hash passwords in signup)
    if (password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      header("Location: index.php"); // redirect after login
      exit();
    } else {
      echo "<script>alert('Invalid password!'); window.location.href='login.php';</script>";
    }
  } else {
    echo "<script>alert('No account found with that email!'); window.location.href='login.php';</script>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Login - Mobile Store</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header class="header">
    <div class="logo">📱 Mobile Store</div>
    <nav class="navbar">
      <a href="index.php">Home</a>
      <a href="about.php">About</a>
      <a href="contact.php">Contact</a>
      <a href="login.php" class="active">Login</a>
      <a href="signup.php">Signup</a>
      <a href="cart.php">🛒 Cart</a>
    </nav>
  </header>

  <section class="page form-page">
    <h1>User Login</h1>
    <!-- add method + action so form submits to this PHP -->
    <form class="form" method="POST" action="login.php">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <p>Don’t have an account? <a href="signup.php">Signup here</a></p>
  </section>

  <footer class="footer">
    <p>&copy; <?php echo date("Y"); ?> Mobile Store. All rights reserved.</p>
  </footer>
</body>
</html>

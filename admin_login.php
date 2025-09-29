<?php
session_start();

// CONFIG
$ADMIN_EMAIL     = "admin123@gmail.com";
$ADMIN_HASHED_PW = '$2y$10$MnjMDAxuCKYurtq.y3qeW.ss69qBgOncPrT6FZ138Ov8PvijchMeS';

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === $ADMIN_EMAIL && password_verify($password, $ADMIN_HASHED_PW)) {
        // Successful admin login
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_email'] = $ADMIN_EMAIL;
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $msg = "Invalid email or password";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Login - Mobile Store</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header class="header">
    <div class="logo">📱 Mobile Store</div>
    <nav class="navbar">
      <a href="index.php">Home</a>
      <a href="admin_login.php" class="active">Admin Login</a>
    </nav>
  </header>

  <section class="page form-page">
    <h1>Admin Login</h1>
    <?php if ($msg): ?>
      <p class="error"><?php echo htmlspecialchars($msg); ?></p>
    <?php endif; ?>
    <form class="form" method="post" autocomplete="off">
      <input type="email" name="email" placeholder="Admin Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
  </section>

  <footer class="footer">
    <p>&copy; <?php echo date("Y"); ?> Mobile Store. All rights reserved.</p>
  </footer>
</body>
</html>

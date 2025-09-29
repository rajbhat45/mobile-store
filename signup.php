<?php
include 'db.php';
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // secure password

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = "Email already registered!";
    } else {
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            $message = "Signup successful! You can now login.";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Signup - Mobile Store</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header>
    <div class="header-title">Mobile Store</div>
    <nav>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="login.php">Login</a></li>
        <li><a href="signup.php" class="active">Signup</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <section class="form-container">
      <h2>Create an Account</h2>
      <?php if ($message) echo "<p style='color:red;'>$message</p>"; ?>

      <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Signup</button>
      </form>

      <p>Already have an account? <a href="login.php">Login here</a></p>
    </section>
  </main>

  <footer>
    <p>&copy; <?php echo date("Y"); ?> Mobile Store. All rights reserved.</p>
  </footer>
</body>
</html>

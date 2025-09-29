<?php
session_start();
require 'db.php';

if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* ──────────────────────────────────────────
   1️⃣  Get the payment method from payment.php
   ────────────────────────────────────────── */
$payment_method = $_POST['payment_method'] ?? 'Not Selected';

/* ──────────────────────────────────────────
   2️⃣  Update address if user entered new one
   (still keeps your previous logic)
   ────────────────────────────────────────── */
$newAddress = trim($_POST['address'] ?? '');
if ($newAddress !== '') {
    $stmt = $conn->prepare("UPDATE users SET address=? WHERE id=?");
    $stmt->bind_param("si", $newAddress, $user_id);
    $stmt->execute();
}

/* ──────────────────────────────────────────
   3️⃣  OPTIONAL: Clear cart / insert into orders table
   (kept as comment for now, same as your old code)
   ────────────────────────────────────────── */
// Example: $conn->query("DELETE FROM cart WHERE user_id=$user_id");

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Order Placed</title>
<style>
body {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    background: #f0f4f8;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.thankyou {
    text-align: center;
    background: #fff;
    padding: 2rem 3rem;
    border-radius: 15px;
    box-shadow: 0 0 25px rgba(0,0,0,0.1);
}
.thankyou h1 {
    color: #4CAF50;
    font-size: 2rem;
    margin-bottom: 0.5rem;
}
.thankyou p {
    font-size: 1.1rem;
    color: #555;
    margin-top: 0.5rem;
}
.payment-info {
    margin-top: 1rem;
    font-weight: bold;
    color: #333;
}
</style>
</head>
<body>
<div class="thankyou">
  <h1>✅ Order Placed</h1>
  <p>Thank you for shopping with us!</p>

  <!-- show the payment method the user selected -->
  <!--<p class="payment-info">Payment Method: <?= htmlspecialchars($payment_method) ?></p> -->
</div>
</body>
</html>

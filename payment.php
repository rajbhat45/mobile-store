<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'db.php';

// Fetch user address
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT address FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$hasAddress = !empty($user['address']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Select Payment Method</title>
<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #fdfbfb, #ebedee);
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }
    .payment-container {
        background: #fff;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        text-align: center;
        width: 420px;
    }
    h1 { margin-bottom: 30px; color: #2a4d9b; }
    .btn {
        display: block;
        width: 100%;
        padding: 14px;
        margin: 10px 0;
        border-radius: 8px;
        border: none;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }
    .btn.credit { background: #2a4d9b; color: white; }
    .btn.debit { background: #28a745; color: white; }
    .btn.upi   { background: #ff9800; color: white; }
    .btn.cod   { background: #555; color: white; }
    .btn:hover { opacity: 0.85; }

    /* Sections for forms */
    .section { display: none; margin-top: 20px; }
    .section.active { display: block; }
    .inner-form { margin-top: 20px; }
    .inner-form input[type="text"],
    .inner-form input[type="number"] {
        width: 90%;
        padding: 10px;
        margin: 8px 0;
        border: 1px solid #ccc;
        border-radius: 6px;
    }
    .buy-btn {
        background: #2a4d9b;
        color: #fff;
        padding: 12px 30px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        margin-top: 15px;
        transition: 0.3s;
        display: inline-block;
    }
    .buy-btn:hover { opacity: 0.9; }
    .center-btn { text-align: center; } /* ensures buy button is centered */
</style>
<script>
function showSection(method) {
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    const sec = document.getElementById(method);
    if (sec) sec.classList.add('active');
}
</script>
</head>
<body>
<div class="payment-container">
    <h1>Select Payment Method</h1>

    <!-- Main Buttons -->
    <button class="btn credit" onclick="showSection('credit')">Pay using Credit Card</button>
    <button class="btn debit"  onclick="showSection('debit')">Pay using Debit Card</button>
    <button class="btn upi"    onclick="showSection('upi')">Pay using UPI</button>
    <!-- Cash on Delivery: direct submit -->
    <form action="place_order.php" method="POST" style="margin-top:10px;">
        
        <input type="hidden" name="method" value="Cash on Delivery">
        <button type="submit" class="btn cod">Cash on Delivery</button>
    </form>

    <!-- Credit Card Section -->
    <div id="credit" class="section">
        <form action="place_order.php" method="POST" class="inner-form">
            <?php if (!$hasAddress): ?>
                <input type="text" name="address" placeholder="Enter Delivery Address" required>
            <?php endif; ?>
            <input type="text" name="cc_number" placeholder="Credit Card Number" required>
            <input type="text" name="cc_expiry" placeholder="Expiry (MM/YY)" required>
            <input type="number" name="cc_cvv" placeholder="CVV" required>
            <input type="hidden" name="method" value="Credit Card">
            <div class="center-btn">
                <button type="submit" class="buy-btn">Buy</button>
            </div>
        </form>
    </div>

    <!-- Debit Card Section -->
    <div id="debit" class="section">
        <form action="place_order.php" method="POST" class="inner-form">
            <?php if (!$hasAddress): ?>
                <input type="text" name="address" placeholder="Enter Delivery Address" required>
            <?php endif; ?>
            <input type="text" name="dc_number" placeholder="Debit Card Number" required>
            <input type="text" name="dc_expiry" placeholder="Expiry (MM/YY)" required>
            <input type="number" name="dc_cvv" placeholder="CVV" required>
            <input type="hidden" name="method" value="Debit Card">
            <div class="center-btn">
                <button type="submit" class="buy-btn">Buy</button>
            </div>
        </form>
    </div>

    <!-- UPI Section -->
    <div id="upi" class="section">
        <form action="place_order.php" method="POST" class="inner-form">
            <?php if (!$hasAddress): ?>
                <input type="text" name="address" placeholder="Enter Delivery Address" required>
            <?php endif; ?>
            <img src="images/paymentQR.png" alt="UPI QR Code" style="max-width:200px;margin:15px 0;">
            <input type="hidden" name="method" value="UPI">
            <div class="center-btn">
                <button type="submit" class="buy-btn">Buy</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>

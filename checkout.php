<?php
session_start();
include 'db.php';

// ✅ Fetch current user’s cart items
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

$sql = "SELECT c.id, p.name, p.price, p.image, c.quantity 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = $user_id";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout</title>
<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #f0f4ff, #d9e6ff);
        color: #333;
    }
    .checkout-container {
        max-width: 1000px;
        margin: 40px auto;
        background: #fff;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    h1 {
        text-align: center;
        color: #2a4d9b;
        margin-bottom: 30px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 25px;
    }
    th, td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    th {
        background: #2a4d9b;
        color: white;
        text-transform: uppercase;
        font-size: 14px;
        letter-spacing: 0.5px;
    }
    td img {
        width: 70px;
        height: 70px;
        border-radius: 10px;
        object-fit: cover;
    }
    .total {
        text-align: right;
        font-size: 1.2rem;
        font-weight: bold;
        margin-top: 15px;
    }
    .btn-area {
        text-align: center;
        margin-top: 30px;
    }
    .btn {
        display: inline-block;
        padding: 12px 28px;
        background: #2a4d9b;
        color: #fff;
        text-decoration: none;
        border-radius: 8px;
        transition: 0.3s ease;
        font-size: 16px;
    }
    .btn:hover {
        background: #1b3570;
    }
    @media(max-width: 768px) {
        table, thead, tbody, th, td, tr {
            display: block;
        }
        th { display: none; }
        td {
            padding: 10px;
            border: none;
            position: relative;
        }
        td img {
            width: 50px;
            height: 50px;
        }
        td:before {
            position: absolute;
            left: 0;
            width: 100%;
            padding-left: 15px;
            font-weight: bold;
        }
    }
</style>
</head>
<body>
<div class="checkout-container">
    <h1>Checkout</h1>

    <?php
    $grand_total = 0;
    if ($result && $result->num_rows > 0) {
        echo "<table>";
        echo "<tr>
                <th>Product</th>
                <th>Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
              </tr>";
        while ($row = $result->fetch_assoc()) {
            $subtotal = $row['price'] * $row['quantity'];
            $grand_total += $subtotal;
            echo "<tr>
                    <td><img src='images/{$row['image']}' alt='{$row['name']}'></td>
                    <td>{$row['name']}</td>
                    <td>₹{$row['price']}</td>
                    <td>{$row['quantity']}</td>
                    <td>₹{$subtotal}</td>
                  </tr>";
        }
        echo "</table>";
        echo "<div class='total'>Grand Total: ₹{$grand_total}</div>";
    } else {
        echo "<p style='text-align:center;'>Your cart is empty.</p>";
    }
    ?>

    <div class="btn-area">
    <a href="payment.php" class="btn">Place Order</a>
    <a href="index.php" class="btn" style="background:#888;margin-left:10px;">Continue Shopping</a>
</div>


</div>
</body>
</html>

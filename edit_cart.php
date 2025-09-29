<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) { header("Location: admin_login.php"); exit; }
require 'db.php';

$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity = (int)$_POST['quantity'];
    $stmt = $conn->prepare("UPDATE cart SET quantity=? WHERE id=?");
    $stmt->bind_param("ii", $quantity, $id);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit;
}

$cart = $conn->query("SELECT * FROM cart WHERE id=$id")->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head><title>Edit Cart Item</title></head>
<body>
<h1>Edit Cart Item</h1>
<form method="post">
    Quantity: <input type="number" name="quantity" min="1" value="<?= $cart['quantity'] ?>" required><br>
    <button type="submit">Save Changes</button>
</form>
</body>
</html>

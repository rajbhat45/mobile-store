<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("You must log in first.");
}

$user_id = $_SESSION['user_id'];
$cart_id = intval($_POST['cart_id']);

// ✅ Delete the row completely (so when you add again, it starts fresh at 1)
$sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $cart_id, $user_id);
$stmt->execute();

// Go back to cart
header("Location: cart.php?fresh=1");
exit;

?>

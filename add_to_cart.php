<?php
session_start();
include 'db.php';

$logFile = __DIR__ . '/cart_debug.log';
file_put_contents($logFile, date('Y-m-d H:i:s') . " >>> add_to_cart.php called. POST=" . json_encode($_POST) . " SESSION_user=" . ($_SESSION['user_id'] ?? 'none') . "\n", FILE_APPEND);

if (!isset($_SESSION['user_id'])) {
    die("You must log in first.");
}

$user_id = (int)$_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
if ($product_id <= 0) {
    file_put_contents($logFile, date('Y-m-d H:i:s') . " >>> add_to_cart: invalid product_id\n", FILE_APPEND);
    header("Location: index.php");
    exit;
}

// default quantity to add
$qty = 1;

// check existing row for this user/product
$stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
    // Option A (safe): set to 1 (prevents duplication/increment bugs)
    $newQty = 1;
    $u = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $u->bind_param("ii", $newQty, $row['id']);
    $u->execute();
    $u->close();

    // Option B (increment by 1) - uncomment if you want this:
    // $newQty = $row['quantity'] + $qty;
    // $u = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    // $u->bind_param("ii", $newQty, $row['id']);
    // $u->execute();
    // $u->close();

    file_put_contents($logFile, date('Y-m-d H:i:s') . " >>> add_to_cart: updated id={$row['id']} to qty={$newQty}\n", FILE_APPEND);
} else {
    // insert new
    $i = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $i->bind_param("iii", $user_id, $product_id, $qty);
    $i->execute();
    file_put_contents($logFile, date('Y-m-d H:i:s') . " >>> add_to_cart: inserted user=$user_id product=$product_id qty=$qty id=" . $conn->insert_id . "\n", FILE_APPEND);
    $i->close();
}

$stmt->close();

// redirect back to cart
header("Location: cart.php");
exit;

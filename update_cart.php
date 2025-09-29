<?php
session_start();
include 'db.php';

// debug log helper
$logFile = __DIR__ . '/cart_debug.log';
file_put_contents($logFile, date('Y-m-d H:i:s') . " >>> update_cart.php called. POST=" . json_encode($_POST) . " SESSION_user=" . ($_SESSION['user_id'] ?? 'none') . "\n", FILE_APPEND);

// auth check
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'not_logged_in']);
    exit;
}

// validate
if (!isset($_POST['id']) || !isset($_POST['quantity'])) {
    echo json_encode(['success' => false, 'error' => 'invalid_request']);
    exit;
}

$id = (int)$_POST['id'];
$quantity = (int)$_POST['quantity'];
if ($quantity < 1) $quantity = 1;
$user_id = (int)$_SESSION['user_id'];

// log current DB value for this row (before)
$before = null;
if ($stmt = $conn->prepare("SELECT quantity FROM cart WHERE id = ? AND user_id = ?")) {
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($r = $res->fetch_assoc()) $before = (int)$r['quantity'];
    $stmt->close();
}

file_put_contents($logFile, date('Y-m-d H:i:s') . " >>> update_cart: before id=$id user=$user_id qty=$before\n", FILE_APPEND);

// perform update (secure: restrict to this user)
if ($stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?")) {
    $stmt->bind_param("iii", $quantity, $id, $user_id);
    $ok = $stmt->execute();
    $stmt->close();
} else {
    $ok = false;
}

// log after
$after = null;
if ($stmt = $conn->prepare("SELECT quantity FROM cart WHERE id = ? AND user_id = ?")) {
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($r = $res->fetch_assoc()) $after = (int)$r['quantity'];
    $stmt->close();
}

file_put_contents($logFile, date('Y-m-d H:i:s') . " >>> update_cart: after  id=$id user=$user_id qty=$after ok=" . ($ok ? '1' : '0') . "\n", FILE_APPEND);

echo json_encode(['success' => (bool)$ok, 'before' => $before, 'after' => $after]);
exit;

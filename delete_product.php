<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) { header("Location: admin_login.php"); exit; }
require 'db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}
header("Location: admin_dashboard.php");
exit;

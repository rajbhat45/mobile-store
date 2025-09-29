<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) { header("Location: admin_login.php"); exit; }
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = $_POST['name'];
    $brand = $_POST['brand'];
    $price = $_POST['price'];
    $image = $_POST['image'];
    $desc  = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO products (name, brand, price, image, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $name, $brand, $price, $image, $desc);
    $stmt->execute();

    header("Location: admin_dashboard.php");
    exit;
}
?>

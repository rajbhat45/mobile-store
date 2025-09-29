<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) { header("Location: admin_login.php"); exit; }
require 'db.php';

$id = (int)($_GET['id'] ?? 0);

// Handle POST (update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = $_POST['name'];
    $brand = $_POST['brand'];
    $price = $_POST['price'];
    $image = $_POST['image'];
    $desc  = $_POST['description'];

    $stmt = $conn->prepare("UPDATE products SET name=?, brand=?, price=?, image=?, description=? WHERE id=?");
    $stmt->bind_param("ssdssi", $name, $brand, $price, $image, $desc, $id);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit;
}

// Fetch current product
$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head><title>Edit Product</title></head>
<body>
<h1>Edit Product</h1>
<form method="post">
    Name: <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required><br>
    Brand: <input type="text" name="brand" value="<?= htmlspecialchars($product['brand']) ?>" required><br>
    Price: <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required><br>
    Image URL: <input type="text" name="image" value="<?= htmlspecialchars($product['image']) ?>"><br>
    Description:<br>
    <textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea><br>
    <button type="submit">Save Changes</button>
</form>
</body>
</html>

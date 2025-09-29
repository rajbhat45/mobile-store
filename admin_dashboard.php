<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}
require 'db.php';

// ── Fetch data ──
$users = $conn->query("SELECT id, username, email, address, role FROM users ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);

$products  = $conn->query("
    SELECT id, name, brand, price, image, description
    FROM products
    ORDER BY id DESC
")->fetch_all(MYSQLI_ASSOC);

$cartItems = $conn->query("
    SELECT c.id, u.username AS user_name, p.name AS product_name,
           c.quantity, (c.quantity * p.price) AS total_price
    FROM cart c
    JOIN users u    ON c.user_id   = u.id
    JOIN products p ON c.product_id = p.id
    ORDER BY c.id DESC
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - Mobile Store</title>
<link rel="stylesheet" href="styles.css">
<style>
body {font-family: Arial, sans-serif; margin:0;}
.header {background:#333; color:#fff; padding:1rem;}
.header .logo {font-weight:bold;}
.navbar {background:#444; display:flex;}
.navbar a {color:#fff; padding:1rem; text-decoration:none; flex:1; text-align:center;}
.navbar a.active, .navbar a:hover {background:#666;}
.section {display:none; padding:1rem;}
.section.active {display:block;}
table {width:100%; border-collapse:collapse; margin-top:1rem;}
th,td {border:1px solid #ccc; padding:8px; text-align:left;}
th {background:#f4f4f4;}
.btn {padding:4px 8px; margin:0 2px; text-decoration:none; border-radius:4px;}
.btn-edit {background:#4CAF50; color:#fff;}
.btn-del  {background:#e74c3c; color:#fff;}
</style>
</head>
<body>

<header class="header">
  <div class="logo">📱 Mobile Store Admin</div>
</header>

<nav class="navbar">
  <a href="#products" class="tab-link active">Products</a>
  <a href="#users" class="tab-link">Users</a>
  <a href="#cart" class="tab-link">Cart</a>
  <a href="logout.php">Logout</a>
</nav>

<!-- Products -->
<section id="products" class="section active">
  <h2>Products</h2>

  <!-- ➕ Create Product Form -->
  <form method="post" action="create_product.php" style="margin-bottom:1.5rem; border:1px solid #ccc; padding:1rem;">
    <h3>Add New Product</h3>
    <label>Name:</label>
    <input type="text" name="name" required>
    <label>Brand:</label>
    <input type="text" name="brand" required>
    <label>Price (₹):</label>
    <input type="number" step="0.01" name="price" required>
    <label>Image URL:</label>
    <input type="text" name="image">
    <label>Description:</label>
    <textarea name="description"></textarea>
    <button type="submit">Create Product</button>
  </form>

<!-- Existing Products Table -->
<table>
  <tr>
    <th>ID</th>
    <th>Name</th>
    <th>Brand</th>
    <th>Price (₹)</th>
    <th>Image</th>
    <th>Description</th>
    <th>Actions</th>
  </tr>
  <?php foreach ($products as $p): ?>
    <tr>
      <td><?= $p['id'] ?></td>
      <td><?= htmlspecialchars($p['name']) ?></td>
      <td><?= !empty($p['brand']) ? htmlspecialchars($p['brand']) : '—' ?></td>
      <td><?= number_format($p['price'],2) ?></td>
      <td><?= $p['image'] ? "<img src='".htmlspecialchars($p['image'])."' style='max-width:60px'>" : "—" ?></td>
      <td><?= htmlspecialchars($p['description']) ?></td>
      <td>
        <a class="btn btn-edit" href="edit_product.php?id=<?= $p['id'] ?>">Edit</a>
        <a class="btn btn-del" href="delete_product.php?id=<?= $p['id'] ?>" onclick="return confirm('Delete this product?')">Delete</a>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
</section>

<!-- Users -->
<section id="users" class="section">
  <h2>Users</h2>
  <table>
    <tr><th>ID</th><th>Username</th><th>Email</th><th>Address</th><th>Role</th><th>Actions</th></tr>

    <?php foreach ($users as $u): ?>
      <tr>
        <td><?= $u['id'] ?></td>
        <td><?= htmlspecialchars($u['username']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td><?= htmlspecialchars($u['address'] ?? '—') ?></td>
        <td><?= htmlspecialchars($u['role']) ?></td>
        <td>
          <a class="btn btn-edit" href="edit_user.php?id=<?= $u['id'] ?>">Edit</a>
          <a class="btn btn-del"  href="delete_user.php?id=<?= $u['id'] ?>" onclick="return confirm('Delete this user?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</section>

<!-- Cart -->
<section id="cart" class="section">
  <h2>Cart Items</h2>
  <table>
    <tr><th>ID</th><th>User</th><th>Product</th><th>Quantity</th><th>Total Price (₹)</th><th>Actions</th></tr>
    <?php foreach ($cartItems as $c): ?>
      <tr>
        <td><?= $c['id'] ?></td>
        <td><?= htmlspecialchars($c['user_name']) ?></td>
        <td><?= htmlspecialchars($c['product_name']) ?></td>
        <td><?= $c['quantity'] ?></td>
        <td><?= number_format($c['total_price'],2) ?></td>
        <td>
          <a class="btn btn-edit" href="edit_cart.php?id=<?= $c['id'] ?>">Edit</a>
          <a class="btn btn-del"  href="delete_cart.php?id=<?= $c['id'] ?>" onclick="return confirm('Delete this cart item?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</section>

<script>
// simple tab switcher
document.querySelectorAll('.tab-link').forEach(link=>{
  link.addEventListener('click', e=>{
    e.preventDefault();
    document.querySelectorAll('.tab-link').forEach(a=>a.classList.remove('active'));
    document.querySelectorAll('.section').forEach(s=>s.classList.remove('active'));
    link.classList.add('active');
    document.querySelector(link.getAttribute('href')).classList.add('active');
  });
});
</script>

</body>
</html>

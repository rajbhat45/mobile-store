<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) { header("Location: admin_login.php"); exit; }
require 'db.php';

$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $address = $_POST['address'];
    $role     = $_POST['role'];
    
    $stmt = $conn->prepare("UPDATE users SET username=?, email=?, address=?, role=? WHERE id=?");
    $stmt->bind_param("ssssi", $username, $email, $address, $role, $id);

    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit;
}

$user = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head><title>Edit User</title></head>
<body>
<h1>Edit User</h1>
<form method="post">
    Username: <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required><br>
    Email: <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br>
    Address: <input type="text" name="address"
         value="<?= htmlspecialchars($user['address']) ?>"><br>
    Role:
    <select name="role">
        <option value="user"  <?= $user['role']=='user'  ? 'selected' : '' ?>>User</option>
        <option value="admin" <?= $user['role']=='admin' ? 'selected' : '' ?>>Admin</option>
    </select><br>
    <button type="submit">Save Changes</button>
</form>
</body>
</html>

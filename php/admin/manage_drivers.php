<?php
require "../config/db.php";

if (session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

// ===== Handle Add Driver =====
$addError = "";
if(isset($_POST['add_driver'])){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if($name && $email && $password){
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt->execute([$email]);
        if($stmt->rowCount() > 0){
            $addError = "Email already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name,email,password,role,is_online) VALUES (?,?,?,?,0)");
            $stmt->execute([$name, $email, $hashed, 'driver']);
        }
    } else {
        $addError = "All fields are required.";
    }
}

// ===== Handle Deletion =====
if(isset($_GET['delete'])){
    $stmt = $conn->prepare("DELETE FROM users WHERE id=? AND role='driver'");
    $stmt->execute([$_GET['delete']]);
    header("Location: manage_drivers.php");
    exit;
}

// ===== Fetch drivers =====
$stmt = $conn->prepare("SELECT id, name, email, latitude, longitude FROM users WHERE role='driver'");
$stmt->execute();
$drivers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Drivers</title>
<style>
body { font-family: Arial; }
table { border-collapse: collapse; width: 100%; margin-top: 20px; }
th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
input, button { padding:6px; margin:4px; }
a { text-decoration: none; color: blue; }
.error { color:red; }
</style>
</head>
<body>

<h2>Manage Drivers</h2>

<!-- Add Driver Form -->
<form method="POST">
    <h3>Add Driver</h3>
    Name: <input type="text" name="name" required>
    Email: <input type="email" name="email" required>
    Password: <input type="password" name="password" required>
    <button type="submit" name="add_driver">Add Driver</button>
    <span class="error"><?= $addError ?></span>
</form>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Latitude</th>
        <th>Longitude</th>
        <th>Action</th>
    </tr>
    <?php foreach($drivers as $d): ?>
    <tr>
        <td><?= $d['id'] ?></td>
        <td><?= htmlspecialchars($d['name']) ?></td>
        <td><?= htmlspecialchars($d['email']) ?></td>
        <td><?= $d['latitude'] ?? 'N/A' ?></td>
        <td><?= $d['longitude'] ?? 'N/A' ?></td>
        <td><a href="?delete=<?= $d['id'] ?>" onclick="return confirm('Delete this driver?')">Delete</a></td>
    </tr>
    <?php endforeach; ?>
</table>

<p><a href="index.php">Back to Dashboard</a></p>

</body>
</html>

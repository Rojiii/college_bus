<?php
require "../config/db.php";

if (session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

// ===== Handle Add Student =====
$addError = "";
if(isset($_POST['add_student'])){
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
            $stmt->execute([$name, $email, $hashed, 'student']);
        }
    } else {
        $addError = "All fields are required.";
    }
}

// ===== Handle Deletion =====
if(isset($_GET['delete'])){
    $stmt = $conn->prepare("DELETE FROM users WHERE id=? AND role='student'");
    $stmt->execute([$_GET['delete']]);
    header("Location: manage_students.php");
    exit;
}

// ===== Fetch students =====
$stmt = $conn->prepare("SELECT id, name, email, is_online, latitude, longitude FROM users WHERE role='student'");
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Students</title>
<style>
body { font-family: Arial; }
table { border-collapse: collapse; width: 100%; margin-top: 20px; }
th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
input, button { padding:6px; margin:4px; }
a { text-decoration: none; color: blue; }
.online { color: green; font-weight: bold; }
.offline { color: red; font-weight: bold; }
.error { color:red; }
</style>
</head>
<body>

<h2>Manage Students</h2>

<!-- Add Student Form -->
<form method="POST">
    <h3>Add Student</h3>
    Name: <input type="text" name="name" required>
    Email: <input type="email" name="email" required>
    Password: <input type="password" name="password" required>
    <button type="submit" name="add_student">Add Student</button>
    <span class="error"><?= $addError ?></span>
</form>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Status</th>
        <th>Latitude</th>
        <th>Longitude</th>
        <th>Action</th>
    </tr>
    <?php foreach($students as $s): ?>
    <tr>
        <td><?= $s['id'] ?></td>
        <td><?= htmlspecialchars($s['name']) ?></td>
        <td><?= htmlspecialchars($s['email']) ?></td>
        <td>
            <?php if($s['is_online']==1){ echo "<span class='online'>Online</span>"; } 
                  else { echo "<span class='offline'>Offline</span>"; } ?>
        </td>
        <td><?= $s['latitude'] ?? 'N/A' ?></td>
        <td><?= $s['longitude'] ?? 'N/A' ?></td>
        <td><a href="?delete=<?= $s['id'] ?>" onclick="return confirm('Delete this student?')">Delete</a></td>
    </tr>
    <?php endforeach; ?>
</table>

<p><a href="index.php">Back to Dashboard</a></p>

</body>
</html>

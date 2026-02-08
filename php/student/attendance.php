<?php
require "../config/db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if student is logged in
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student'){
    header("Location: ../auth/login.php");
    exit;
}

$studentId = $_SESSION['user_id'];
$statusMessage = "";

// Handle attendance form
if(isset($_POST['present'])){
    $stmt = $conn->prepare("UPDATE users SET is_online=1 WHERE id=?");
    if($stmt->execute([$studentId])){
        $statusMessage = "You are marked present (online)";
    } else {
        $statusMessage = "Failed to update attendance.";
    }
} elseif(isset($_POST['absent'])){
    $stmt = $conn->prepare("UPDATE users SET is_online=0 WHERE id=?");
    if($stmt->execute([$studentId])){
        $statusMessage = "You are marked absent (offline)";
    } else {
        $statusMessage = "Failed to update attendance.";
    }
}

// Get current status
$stmt = $conn->prepare("SELECT is_online FROM users WHERE id=?");
$stmt->execute([$studentId]);
$currentStatus = $stmt->fetch(PDO::FETCH_ASSOC)['is_online'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance - Student</title>
    <style>
        body { font-family: Arial; margin:0; padding:0; }
        .container { padding: 20px; }
        button { padding: 10px 20px; margin: 5px; }
        .status { margin-top: 10px; font-weight: bold; }
        a { text-decoration:none; color:blue; }
    </style>
</head>
<body>

<div class="container">
    <h2>Attendance</h2>
    <p>Welcome, <?= htmlspecialchars($_SESSION['name'] ?? 'Student') ?></p>

    <form method="POST">
        <button type="submit" name="present">Mark Present (Online)</button>
        <button type="submit" name="absent">Mark Absent (Offline)</button>
    </form>

    <p class="status">
        Current Status: 
        <?php if($currentStatus == 1){ echo "<span style='color:green;'>Online</span>"; } 
              else { echo "<span style='color:red;'>Offline</span>"; } ?>
    </p>

    <p><?= $statusMessage ?></p>

    <p><a href="dashboard.php">Go to Dashboard</a></p>
    <p><a href="../auth/logout.php">Logout</a></p>
</div>

</body>
</html>

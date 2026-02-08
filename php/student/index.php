<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if student is logged in
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student'){
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Portal</title>
    <style>
        body { font-family: Arial; margin:0; padding:0; display:flex; justify-content:center; align-items:center; height:100vh; background:#f2f2f2; }
        .container { text-align:center; background:white; padding:50px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.2); }
        a { display:block; padding:15px 30px; margin:10px; background:#007bff; color:white; text-decoration:none; border-radius:5px; }
        a:hover { background:#0056b3; }
    </style>
</head>
<body>

<div class="container">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['name'] ?? 'Student') ?></h2>
    
    <a href="dashboard.php">Bus Map</a>
    <a href="attendance.php">Attendance</a>
    <a href="../auth/logout.php">Logout</a>
</div>

</body>
</html>

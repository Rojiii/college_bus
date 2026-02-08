<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if driver is logged in
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver'){
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Driver Portal</title>
    <style>
        body { font-family: Arial; margin:0; padding:0; display:flex; justify-content:center; align-items:center; height:100vh; background:#f2f2f2; }
        .container { text-align:center; background:white; padding:50px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.2); }
        a { display:block; padding:15px 30px; margin:10px; background:#28a745; color:white; text-decoration:none; border-radius:5px; }
        a:hover { background:#1e7e34; }
    </style>
</head>
<body>

<div class="container">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['name'] ?? 'Driver') ?></h2>
    
    <a href="dashboard.php">Driver Dashboard (Bus Map)</a>
  
    <a href="../auth/logout.php">Logout</a>
</div>

</body>
</html>

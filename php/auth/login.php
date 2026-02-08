<?php
require "../config/db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$loginError = "";

if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic validation
    if ($email === '' || $password === '') {
        $loginError = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $loginError = "Invalid email format.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            // Role-based redirect
            if ($user['role'] === 'admin') {
                header("Location: ../admin/index.php");
            } elseif ($user['role'] === 'student') {
                header("Location: ../student/index.php");
            } elseif ($user['role'] === 'driver') {
                header("Location: ../driver/index.php");
            }
            exit;

        } else {
            $loginError = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { font-family: Arial; background:#f5f5f5; }
        .box { width:300px; margin:80px auto; padding:20px; background:#fff; border-radius:5px; }
        input, button { width:100%; padding:8px; margin:8px 0; }
        .error { color:red; }
        a { text-decoration:none; color:blue; }
    </style>
</head>
<body>

<div class="box">
    <h3>Login</h3>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>

    <p class="error"><?= htmlspecialchars($loginError) ?></p>

    <p>
        New user?
        <a href="signup.php">Create an account</a>
    </p>
</div>

</body>
</html>

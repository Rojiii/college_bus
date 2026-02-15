<?php
require "../config/db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$signupError = "";
$signupSuccess = "";

if (isset($_POST['signup'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Validation
    if ($name === '' || $email === '' || $password === '' || $role === '') {
        $signupError = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $signupError = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $signupError = "Password must be at least 6 characters.";
    } elseif (!in_array($role, ['student', 'driver', 'admin'])) {
        $signupError = "Invalid role selected.";
    } else {

        // Check duplicate email
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $signupError = "Email already registered.";
        } else {
            // TEMP: Prevent creating second admin
            if ($role === 'admin') {
                $stmt = $conn->prepare("SELECT id FROM users WHERE role='admin' LIMIT 1");
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $signupError = "Admin account already exists.";
                }
            }
        }

        if (!$signupError) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, password, role, is_online) VALUES (?, ?, ?, ?, 0)"
            );
            $stmt->execute([$name, $email, $hashed, $role]);

            $signupSuccess = "Account created successfully. You can now log in.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
    <style>
        body { font-family: Arial; background:#f5f5f5; }
        .box { width:300px; margin:80px auto; padding:20px; background:#fff; border-radius:5px; }
        input, select, button { width:100%; padding:8px; margin:8px 0; }
        .error { color:red; }
        .success { color:green; }
        a { text-decoration:none; color:blue; }
    </style>
</head>
<body>

<div class="box">
    <h3>Signup</h3>

    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password (min 6 chars)" required>

        <select name="role" required>
            <option value="">-- Select Role --</option>
            <option value="student">Student</option>
            <option value="driver">Driver</option>
            <?php 
                // TEMP: Show admin option only if no admin exists
                $stmt = $conn->prepare("SELECT id FROM users WHERE role='admin' LIMIT 1");
                $stmt->execute();
                if($stmt->rowCount() == 0){ 
            ?>
                <option value="admin">Admin</option>
            <?php } ?>
        </select>

        <button type="submit" name="signup">Create Account</button>
    </form>

    <?php if ($signupError): ?>
        <p class="error"><?= htmlspecialchars($signupError) ?></p>
    <?php endif; ?>

    <?php if ($signupSuccess): ?>
        <p class="success"><?= htmlspecialchars($signupSuccess) ?></p>
        <p><a href="login.php">Go to Login</a></p>
    <?php endif; ?>

    <?php if (!$signupSuccess): ?>
        <p>Already have an account? <a href="login.php">Login</a></p>
    <?php endif; ?>
</div>

</body>
</html>

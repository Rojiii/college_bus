<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: notifications.php");
    exit;
}

$id = $_GET['id'];

$stmt = $conn->prepare("UPDATE users SET is_approved = 1 WHERE id = ?");
$stmt->execute([$id]);

header("Location: notifications.php");
exit;

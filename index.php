<?php
session_start();

if(!isset($_SESSION['role'])){
    header("Location: php/auth/login.php");
    exit;
}

if($_SESSION['role'] === 'admin'){
    header("Location: php/admin/dashboard.php");
} elseif($_SESSION['role'] === 'student'){
    header("Location: php/student/dashboard.php");
} elseif($_SESSION['role'] === 'driver'){
    header("Location: php/driver/dashboard.php");
}

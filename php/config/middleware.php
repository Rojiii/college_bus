<?php
function requireLogin($role){
    if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== $role){
        header("Location: /college_bus_routing/php/auth/login.php");
        exit;
    }
}

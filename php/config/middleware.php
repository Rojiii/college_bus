<?php
function requireLogin($role): void{
    if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== $role){
        header(header: "Location: /college_bus_routing/php/auth/login.php");
        exit;
    }
}

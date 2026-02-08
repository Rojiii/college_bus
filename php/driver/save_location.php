<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    http_response_code(403);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if(isset($data['lat'],$data['lng'])){
    $stmt = $conn->prepare("UPDATE users SET latitude=?, longitude=? WHERE id=?");
    $stmt->execute([$data['lat'],$data['lng'],$_SESSION['user_id']]);
}
echo json_encode(['status'=>'ok']);

<?php
require "../config/db.php";
$stmt = $conn->prepare("SELECT latitude, longitude FROM users WHERE role='driver' LIMIT 1");
$stmt->execute();
$driver = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode([
    'lat' => $driver['latitude'],
    'lng' => $driver['longitude']
]);

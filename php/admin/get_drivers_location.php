<?php
require "../config/db.php";

// Fetch all drivers
$stmt = $conn->prepare("SELECT id, name, latitude, longitude FROM users WHERE role='driver'");
$stmt->execute();
$drivers = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($drivers);
?>

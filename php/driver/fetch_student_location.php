<?php
require "../config/db.php";

// Fetch all online students with their coordinates
$stmt = $conn->prepare("SELECT id, name, latitude, longitude FROM users WHERE role='student' AND is_online=1");
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($students);

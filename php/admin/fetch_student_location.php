<?php
require "../config/db.php";

// Get all active students
$stmt = $conn->prepare("
    SELECT id, name, latitude, longitude
    FROM users
    WHERE role='student' AND is_online=1
");
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

$data = [];
foreach($students as $s){
    $data[] = [
        'id' => $s['id'],
        'name' => $s['name'],
        'lat' => $s['latitude'],
        'lng' => $s['longitude']
    ];
}

echo json_encode($data);

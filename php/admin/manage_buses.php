<?php
session_start();
require "../config/db.php";

// Only admin access
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

// Handle Add Bus
if(isset($_POST['add_bus'])){
    $bus_number = $_POST['bus_number'];
    $driver_id = $_POST['driver_id'] ?: NULL;
    $stmt = $conn->prepare("INSERT INTO buses (bus_number, driver_id) VALUES (?, ?)");
    $stmt->execute([$bus_number, $driver_id]);
}

// Handle Delete Bus
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM buses WHERE id=?");
    $stmt->execute([$id]);
}

// Fetch buses and drivers
$buses = $conn->query("SELECT b.id, b.bus_number, u.name AS driver_name FROM buses b LEFT JOIN users u ON b.driver_id=u.id")->fetchAll(PDO::FETCH_ASSOC);
$drivers = $conn->query("SELECT id, name FROM users WHERE role='driver'")->fetchAll(PDO::FETCH_ASSOC);

$totalBuses = count($buses);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Buses</title>
    <style>
        body { font-family: Arial; margin:20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { padding:8px; border:1px solid #ccc; text-align:center; }
        input, select, button { padding:6px; margin:4px; }
        .top-info { margin-bottom:10px; font-weight:bold; }
    </style>
</head>
<body>

<h2>Manage Buses</h2>
<p class="top-info">Total Buses: <?= $totalBuses ?></p>

<form method="POST">
    Bus Number: <input type="text" name="bus_number" required>
    Assign Driver:
    <select name="driver_id">
        <option value="">--None--</option>
        <?php foreach($drivers as $d): ?>
            <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" name="add_bus">Add Bus</button>
</form>

<table>
    <tr>
        <th>ID</th>
        <th>Bus Number</th>
        <th>Driver</th>
        <th>Action</th>
    </tr>
    <?php foreach($buses as $bus): ?>
        <tr>
            <td><?= $bus['id'] ?></td>
            <td><?= htmlspecialchars($bus['bus_number']) ?></td>
            <td><?= htmlspecialchars($bus['driver_name'] ?? '-') ?></td>
            <td><a href="?delete=<?= $bus['id'] ?>" onclick="return confirm('Delete this bus?')">Delete</a></td>
        </tr>
    <?php endforeach; ?>
</table>

<p><a href="index.php">Back to Dashboard</a></p>

</body>
</html>

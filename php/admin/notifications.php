<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Approve user
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    $conn->prepare("UPDATE users SET is_approved=1 WHERE id=?")->execute([$id]);
    header("Location: notifications.php");
    exit;
}

// Reject user
if (isset($_GET['reject'])) {
    $id = $_GET['reject'];
    $conn->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
    header("Location: notifications.php");
    exit;
}

// Fetch pending users
$stmt = $conn->prepare("SELECT * FROM users WHERE is_approved=0");
$stmt->execute();
$pendingUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Notifications</title>
    <style>
        body { font-family: Arial; padding:20px; }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:10px; border:1px solid #ccc; }
        a { padding:6px 10px; text-decoration:none; color:white; }
        .approve { background:green; }
        .reject { background:red; }
    </style>
</head>
<body>

<h2>Pending User Approvals</h2>

<?php if (count($pendingUsers) === 0): ?>
    <p>No pending approvals.</p>
<?php else: ?>
<table>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Action</th>
    </tr>
    <?php foreach ($pendingUsers as $u): ?>
    <tr>
        <td><?= htmlspecialchars($u['name']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td><?= htmlspecialchars($u['role']) ?></td>
        <td>
            <a class="approve" href="?approve=<?= $u['id'] ?>">Approve</a>
            <a class="reject" href="?reject=<?= $u['id'] ?>">Reject</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

</body>
</html>

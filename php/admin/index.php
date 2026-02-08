<?php
session_start();

// Redirect if not logged in as admin
if (!isset($_SESSION['user_id'])) {
    header("Location: /college_bus/php/admin/index.php");
    exit;
}

// Use the correct session variable for admin name
$admin_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : "Admin";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="flex">

    <!-- Sidebar -->
    <div class="w-64 h-screen bg-gray-900 text-white fixed">
        <div class="p-6 text-center border-b border-gray-700">
            <h2 class="text-2xl font-bold">Admin Panel</h2>
            <p class="text-gray-300 text-sm">Welcome, <?php echo $admin_name; ?></p>
        </div>

        <ul class="mt-4">
            <li>
                <a href="manage_places.php" class="block px-6 py-3 hover:bg-gray-700">🏙 Manage Places</a>
            </li>
            <li>
                <a href="manage_buses.php" class="block px-6 py-3 hover:bg-gray-700">🚌 Manage Buses</a>
            </li>
            <li>
                <a href="manage_drivers.php" class="block px-6 py-3 hover:bg-gray-700">👷 Manage Drivers</a>
            </li>
            <li>
                <a href="manage_students.php" class="block px-6 py-3 hover:bg-gray-700">🎓 Manage Students</a>
            </li>
            <li>
                <a href="notifications.php" class="block px-6 py-3 hover:bg-gray-700">🔔 Notifications</a>
            </li>
        </ul>

        <div class="absolute bottom-0 w-full p-4 border-t border-gray-700">
            <a href="../auth/logout.php" class="block text-center bg-red-600 py-2 rounded hover:bg-red-700">
                Logout
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="ml-64 w-full p-10">
        <h1 class="text-3xl font-bold mb-6">Dashboard Overview</h1>

        <div class="grid grid-cols-3 gap-6">

            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-xl font-semibold">Manage Places</h3>
                <p class="text-gray-600 mt-2">See where the drivers and students are.</p>
            </div>

            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-xl font-semibold">Manage Buses</h3>
                <p class="text-gray-600 mt-2">Add, edit, or assign buses.</p>
            </div>

            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-xl font-semibold">Manage Drivers</h3>
                <p class="text-gray-600 mt-2">Add drivers and assign them to buses.</p>
            </div>

            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-xl font-semibold">Manage Students</h3>
                <p class="text-gray-600 mt-2">Update student pickup routes.</p>
            </div>

        </div>

    </div>

</div>

</body>
</html>

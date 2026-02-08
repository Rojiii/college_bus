<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Update Bus Location</title>
</head>
<body>

<h2>Updating Bus Location…</h2>

<script>
function sendLocation(pos){
    fetch('http://localhost:3000/api/update_bus', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({
            lat: pos.coords.latitude,
            lng: pos.coords.longitude
        })
    });
}

navigator.geolocation.watchPosition(
    sendLocation,
    err => console.error(err),
    { enableHighAccuracy:true }
);
</script>

</body>
</html>

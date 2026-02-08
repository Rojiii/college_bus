<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit;
}

// Set student online
$conn->prepare("UPDATE users SET is_online=1 WHERE id=?")->execute([$_SESSION['user_id']]);

$collegeLat = 27.69502;
$collegeLng = 85.32963;
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Dashboard</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<style>
body { margin:0; font-family:Arial; }
#map { height:500px; }
.status { padding:10px; background:#eee; font-weight:bold; }
</style>
</head>
<body>

<div class="status">
    Status: <span style="color:green">ONLINE</span>
</div>

<div id="map"></div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
const collegeLat = <?= $collegeLat ?>;
const collegeLng = <?= $collegeLng ?>;

let studentLat, studentLng;
let busLat, busLng;

// Initialize map
const map = L.map('map').setView([collegeLat, collegeLng], 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(map);

// Icons
const busIcon = L.icon({iconUrl:'https://maps.google.com/mapfiles/ms/icons/blue-dot.png', iconSize:[32,32], iconAnchor:[16,32]});
const studentIcon = L.icon({iconUrl:'https://maps.google.com/mapfiles/ms/icons/green-dot.png', iconSize:[32,32], iconAnchor:[16,32]});
const collegeIcon = L.icon({iconUrl:'https://maps.google.com/mapfiles/ms/icons/red-dot.png', iconSize:[32,32], iconAnchor:[16,32]});

// College marker
L.marker([collegeLat, collegeLng], {icon:collegeIcon}).addTo(map).bindPopup("College");

// Student marker & send location
let studentMarker;
navigator.geolocation.watchPosition(pos => {
    studentLat = pos.coords.latitude;
    studentLng = pos.coords.longitude;

    // Update student marker
    if(!studentMarker){
        studentMarker = L.marker([studentLat, studentLng], {icon:studentIcon})
            .addTo(map)
            .bindPopup("You (Student)").openPopup();
    } else {
        studentMarker.setLatLng([studentLat, studentLng]);
    }

    // Send location to DB
    fetch("save_location.php", {
        method:"POST",
        headers:{"Content-Type":"application/json"},
        body: JSON.stringify({lat: studentLat, lng: studentLng})
    });
}, err => console.error(err), {enableHighAccuracy:true});

// Fetch driver location
let busMarker;
function fetchBus(){
    fetch("get_driver_location.php")
        .then(res=>res.json())
        .then(data=>{
            busLat = data.lat;
            busLng = data.lng;
            if(busLat && busLng){
                if(!busMarker){
                    busMarker = L.marker([busLat, busLng], {icon:busIcon})
                        .addTo(map)
                        .bindPopup("Bus is coming");
                } else {
                    busMarker.setLatLng([busLat, busLng]);
                }
            }
        });
}
setInterval(fetchBus, 2000);
</script>

</body>
</html>

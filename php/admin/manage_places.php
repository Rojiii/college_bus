<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

$collegeLat = 27.69502;
$collegeLng = 85.32963;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Places - Admin</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css"/>
    <style>
        body { margin:0; font-family:Arial; }
        #map { height:600px; }
    </style>
</head>
<body>

<h2>Driver & Student Locations</h2>
<div id="map"></div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

<script>
const collegeLat = <?= $collegeLat ?>;
const collegeLng = <?= $collegeLng ?>;

// Initialize map
const map = L.map('map').setView([collegeLat, collegeLng], 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(map);

// Icons
const driverIcon = L.icon({iconUrl:'https://maps.google.com/mapfiles/ms/icons/blue-dot.png', iconSize:[32,32], iconAnchor:[16,32]});
const studentIcon = L.icon({iconUrl:'https://maps.google.com/mapfiles/ms/icons/green-dot.png', iconSize:[32,32], iconAnchor:[16,32]});
const collegeIcon = L.icon({iconUrl:'https://maps.google.com/mapfiles/ms/icons/red-dot.png', iconSize:[32,32], iconAnchor:[16,32]});

// College marker
L.marker([collegeLat, collegeLng], {icon:collegeIcon}).addTo(map).bindPopup("College");

// Markers
let driverMarkers = {};
let studentMarkers = {}; // optional if you want to show students too

// Fetch drivers
function fetchDrivers(){
    fetch('get_drivers_location.php')
        .then(res=>res.json())
        .then(drivers=>{
            drivers.forEach(driver=>{
                if(driver.latitude && driver.longitude){
                    if(driverMarkers[driver.id]){
                        driverMarkers[driver.id].setLatLng([driver.latitude, driver.longitude]);
                    } else {
                        driverMarkers[driver.id] = L.marker([driver.latitude, driver.longitude], {icon: driverIcon})
                            .addTo(map)
                            .bindPopup(driver.name);
                    }
                }
            });
        });
}

// Refresh every 2 seconds
setInterval(fetchDrivers, 2000);

</script>

</body>
</html>

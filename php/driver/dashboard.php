<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    header("Location: ../auth/login.php");
    exit;
}

$collegeLat = 27.69502;
$collegeLng = 85.32963;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Driver Dashboard</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css"/>
    <style>
        body { margin:0; font-family:Arial; }
        #map { height:500px; }
        button { padding:10px; margin:10px; font-size:16px; }
    </style>
</head>
<body>

<h2>Driver (Bus)</h2>
<button id="pickStudentBtn">Pick Next Student</button>

<div id="map"></div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

<script>
// MAP AND ICONS
const collegeLat = <?= $collegeLat ?>;
const collegeLng = <?= $collegeLng ?>;

const map = L.map('map').setView([collegeLat, collegeLng], 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(map);

const busIcon = L.icon({iconUrl:'https://maps.google.com/mapfiles/ms/icons/blue-dot.png', iconSize:[32,32], iconAnchor:[16,32]});
const studentIcon = L.icon({iconUrl:'https://maps.google.com/mapfiles/ms/icons/green-dot.png', iconSize:[32,32], iconAnchor:[16,32]});
const collegeIcon = L.icon({iconUrl:'https://maps.google.com/mapfiles/ms/icons/red-dot.png', iconSize:[32,32], iconAnchor:[16,32]});

L.marker([collegeLat, collegeLng], {icon:collegeIcon}).addTo(map).bindPopup("College");

let busMarker;
let busLat, busLng;

// Watch driver location
navigator.geolocation.watchPosition(pos => {
    busLat = pos.coords.latitude;
    busLng = pos.coords.longitude;

    // Save driver location
    fetch("save_location.php", {
        method:"POST",
        headers:{"Content-Type":"application/json"},
        body: JSON.stringify({lat:busLat, lng:busLng})
    });

    if(!busMarker){
        busMarker = L.marker([busLat, busLng], {icon:busIcon}).addTo(map).bindPopup("Bus (You)").openPopup();
    } else {
        busMarker.setLatLng([busLat, busLng]);
    }
}, () => alert("Location error"), { enableHighAccuracy:true });

// ROUTING
let route = L.Routing.control({waypoints:[], addWaypoints:false, draggableWaypoints:false, show:false}).addTo(map);

// ACTIVE STUDENTS
let students = [];
let currentIndex = 0;
let studentMarkers = {};

// Fetch active students every 5 seconds
function fetchStudents(){
    fetch("fetch_student_location.php")
        .then(res => res.json())
        .then(data => {
            students = data.filter(s => s.latitude && s.longitude);
            
            // Update student markers
            students.forEach(s => {
                if(!studentMarkers[s.id]){
                    studentMarkers[s.id] = L.marker([s.latitude, s.longitude], {icon:studentIcon})
                        .addTo(map)
                        .bindPopup(s.name);
                } else {
                    studentMarkers[s.id].setLatLng([s.latitude, s.longitude]);
                }
            });
        });
}
setInterval(fetchStudents, 5000);
fetchStudents();

// PICK NEXT STUDENT BUTTON
document.getElementById('pickStudentBtn').addEventListener('click', () => {
    if(students.length === 0){
        alert("No active students to pick. Going to college.");
        route.setWaypoints([
            L.latLng(busLat, busLng),
            L.latLng(collegeLat, collegeLng)
        ]);
        return;
    }

    if(currentIndex >= students.length){
        alert("All students picked. Going to college.");
        route.setWaypoints([
            L.latLng(busLat, busLng),
            L.latLng(collegeLat, collegeLng)
        ]);
        return;
    }

    const student = students[currentIndex];
    route.setWaypoints([
        L.latLng(busLat, busLng),
        L.latLng(student.latitude, student.longitude)
    ]);
    alert(`Next student to pick: ${student.name}`);
    currentIndex++;
});
</script>

</body>
</html>

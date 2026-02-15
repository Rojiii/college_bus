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

<h2>Driver</h2>
<button id="pickStudentBtn">Pick Next Student</button>

<div id="map"></div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

<!-- YOUR DIJKSTRA FILE -->
<script src="dijkstra.js"></script>

<script>

// ================= MAP SETUP =================
const collegeLat = <?= $collegeLat ?>;
const collegeLng = <?= $collegeLng ?>;

const map = L.map('map').setView([collegeLat, collegeLng], 14);

L.tileLayer(
'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
{ maxZoom:19 }
).addTo(map);


// ================= ICONS =================
const busIcon = L.icon({
iconUrl:'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
iconSize:[32,32],
iconAnchor:[16,32]
});

const studentIcon = L.icon({
iconUrl:'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
iconSize:[32,32],
iconAnchor:[16,32]
});

const collegeIcon = L.icon({
iconUrl:'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
iconSize:[32,32],
iconAnchor:[16,32]
});

// College Marker
L.marker([collegeLat, collegeLng], {icon:collegeIcon})
.addTo(map)
.bindPopup("College");


// ================= DRIVER GPS =================
let busMarker;
let busLat, busLng;

navigator.geolocation.watchPosition(pos => {

busLat = pos.coords.latitude;
busLng = pos.coords.longitude;

// Save location to DB
fetch("save_location.php",{
method:"POST",
headers:{"Content-Type":"application/json"},
body: JSON.stringify({lat:busLat,lng:busLng})
});

if(!busMarker){
busMarker = L.marker([busLat,busLng],{icon:busIcon})
.addTo(map)
.bindPopup("Bus (You)")
.openPopup();
}
else{
busMarker.setLatLng([busLat,busLng]);
}

},
()=>alert("Location error"),
{ enableHighAccuracy:true });


// ================= ROUTING =================
let route = L.Routing.control({
waypoints:[],
addWaypoints:false,
draggableWaypoints:false,
show:false
}).addTo(map);


// ================= STUDENTS =================
let students = [];
let currentIndex = 0;
let studentMarkers = {};

function fetchStudents(){
fetch("fetch_student_location.php")
.then(r=>r.json())
.then(data=>{

students = data.filter(s=>s.latitude && s.longitude);

students.forEach(s=>{
if(!studentMarkers[s.id]){
studentMarkers[s.id] = L.marker(
[s.latitude,s.longitude],
{icon:studentIcon}
).addTo(map).bindPopup(s.name);
}
else{
studentMarkers[s.id].setLatLng([s.latitude,s.longitude]);
}
});

});
}

setInterval(fetchStudents,5000);
fetchStudents();


// ================= COLLEGE ROUTE =================
function goCollege(){

if(!busLat){
alert("Waiting for GPS...");
return;
}

let nodes={
bus:{lat:busLat,lng:busLng},
college:{lat:collegeLat,lng:collegeLng}
};

let path = getShortestPath(nodes,"bus","college");

// fallback if algorithm fails
if(!path || path.length<2){
path = [
[busLat,busLng],
[collegeLat,collegeLng]
];
}

route.setWaypoints(
path.map(p=>L.latLng(p[0],p[1]))
);

alert("Going to College");

}


// ================= PICK NEXT STUDENT =================
document.getElementById("pickStudentBtn").onclick=function(){

if(!busLat){
alert("Waiting for GPS...");
return;
}

if(students.length===0){
goCollege();
return;
}

if(currentIndex < students.length){

let student = students[currentIndex];

let nodes={
bus:{lat:busLat,lng:busLng},
student:{lat:student.latitude,lng:student.longitude}
};

let path = getShortestPath(nodes,"bus","student");

// fallback if algorithm fails
if(!path || path.length<2){
path=[
[busLat,busLng],
[student.latitude,student.longitude]
];
}

route.setWaypoints(
path.map(p=>L.latLng(p[0],p[1]))
);

alert("Next Student: "+student.name);

currentIndex++;
return;
}

// After all students
goCollege();

};

</script>

</body>
</html>

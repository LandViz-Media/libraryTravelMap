<!DOCTYPE html>
<html>
<head>
<title>Library Map</title>
<style>
html, body {
    /*height:100%;*/
    margin:0;
    padding:0;
	height: 100vh;
}

#map {
	width: 100%;
	height: 600px;
	margin: 0;
	padding: 0;
}
</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.0.3/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.0.3/dist/leaflet.js"></script>




</head>

<body>
<div id="map"></div>




<script>
//42.04, -94.030556


//95.9482773   - 94  = 100 miles

//1.94929


//1.94250


//http://stevemorse.org/nearest/distance.php
d500 = 9.717525;  //ellipsoidal earth 500 miles at lat 42 is 9.717525

d100 = d500/5;

distance = 1800;

adjustDistance = (distance/100) * d100;



var map = L.map('map').setView([42.04, -94.030556], 2);

// add an OpenStreetMap tile layer
L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
  attribution: 'The map contributors',
  maxZoom: 18
}).addTo(map);


homeLong = -94.030556;

var marker = L.marker([42, homeLong ]).addTo(map);

newLong = homeLong + adjustDistance;

console.log(newLong);

var marker2 = L.marker([42, newLong ]).addTo(map);


</script>



</body>
</html>
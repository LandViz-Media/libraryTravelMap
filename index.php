<?php

//header('Content-type: text/plain');
require("conn.php");

$mysqli = new mysqli($hostname, $username, $password, $database);
// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}


$table = 'libraryTravelMap_teachers';
 $teacherSelect = "";

$sql = "SELECT teacher, grade FROM $table";


$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
	    $teacher = $row["teacher"];
	    $grade = $row["grade"];

	    $teacherSelect .= '<option value="'.$grade.'">'.$teacher.'</option>';
    }
}


$mysqli->close();




?>





<!DOCTYPE html>
<html>
<head>
<title>Ogden Elem. Library Map</title>
<style>
html, body {
    /*height:100%;*/
    margin:0;
    padding:0;
/* 	height: 100vh; */
}


#mainContainer {
    display: flex;
    width: 100%;
    padding: 0px;
    height: 524px;
    background: lightgrey;
    box-sizing: border-box;
    border: 4px solid black;
	}


#map {
	width: 516px;
	height: 516px;
	margin: 0px;
	padding: 0;
	box-sizing: border-box;
	background-color: black;

}


#classDataContainer {


	flex: 1; /* my goal is that the width always fills up independent of browser width */
	background:#e0ff79;
	margin-left: 0px;
		margin-top: 0px;
/* 	width: 100%; */
	height: 516px;
	box-sizing: border-box;
/* 	border: 0px, 0px, 0px, 4px solid black; */
/* 	border-left: 0px solid black; */
}


#classData {
	background:aqua;
	height: 516px;
	padding-left:10px;
	padding-right:10px;
	box-sizing: border-box;
	border-left: 2px solid black;
}


#classData h3{
	text-align: center;
	margin-top: 0;

}




</style>

<!-- Load jQuery -->
<link href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
<script src="https://code.jquery.com/jquery-3.1.0.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


<!-- Load Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.2.0/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.2.0/dist/leaflet.js"></script>

<!-- load external data -->
<script type="text/javascript" src="classes/countries-110m.js"></script>


<!-- load helper classes -->
<script src="classes/L.Graticule.js"></script>

<script type="text/javascript" src="classes/proj4js-compressed.js"></script>
<script type="text/javascript" src="classes/proj4leaflet.js"></script>





</head>

<body>
	<div id = 'mainContainer'>
		<div id="map"></div>
		<div id ="classDataContainer">
			<div id ="classData">
			<h3> Class Data </h3>


			  Class:
  <select id="selectedTeacher">
  <option value="-"></option>
  <?php print $teacherSelect ?>
</select>
<br>





			Total distance: <span id = "classTotalDistance"> 0 </span> miles as of <em>date</em>.
			</div>
		</div> <!-- End Class Data Div -->
	</div>  <!-- End Main Container Div -->













<script>


	var map;

// Shorthand for $( document ).ready()
$(function() {
    console.log( "ready!" );



    // Sphere Mollweide: http://spatialreference.org/ref/esri/53009/
    var crs = new L.Proj.CRS('ESRI:53009', '+proj=moll +lon_0=0 +x_0=0 +y_0=0 +a=6371000 +b=6371000 +units=m +no_defs', {
        resolutions: [65536, 32768, 16384, 8192, 4096, 2048]
    });






var map = L.map('map', {
        minZoom: 0,
        maxZoom: 10,
        worldCopyJump: false,
        //crs: crs,
		continuousWorld: false,
});







// add an OpenStreetMap tile layer
var osm = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
  attribution: 'LVM',
  maxZoom: 18,
  // noWrap: true
});


map.setView([42.04, -94.030556], 0);
map.fitWorld();



/*
// Specify bold red lines instead of thin grey lines
L.graticule({
	interval: 42,
    style: {
        color: '#f00',
        weight: 1
    }
}).addTo(map);





var layer = new L.TileLayer("http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    noWrap: true
});
*/



   var countries = L.geoJson(countries, {
        style: {
            color: '#000',
            weight: 0.5,
            opacity: 1,
            fillColor: '#fff',
            fillOpacity: 1
        }
    }).addTo(map);




  var graticuleOutline =  L.graticule({
        sphere: true,
        style: {
            color: '#777',
            opacity: 1,
            fillColor: '#ccf',
            fillOpacity: 0,
            weight: 2
        }
    }).addTo(map);


var graticule45 =  L.graticule({
	    sphere: false,
	    interval: 45,
        style: {
            color: '#777',
            weight: 1,
            opacity: 0.5
        }
    });

//show prime meridiean and Equator
      var graticule180 =  L.graticule({
	    sphere: false,
	    interval: 180,
        style: {
            color: '#777',
            weight: 2,
            opacity: 1,
            fillOpacity: 0,
        }
    }).addTo(map);











//Add Markers

//---------------
//42.04, -94.030556


//95.9482773   - 94  = 100 miles

//1.94929


//1.94250


//http://stevemorse.org/nearest/distance.php
d500 = 9.717525;  //ellipsoidal earth 500 miles at lat 42 is 9.717525

d100 = d500/5;

distance = 1800;

var adjustDistance = (distance/100) * d100;


//---------------



var homeLng = -94.030556;


var currentMarker = L.marker([42, -125 ]).addTo(map);

var ogden = L.marker([42, homeLng ]).addTo(map);
ogden.bindPopup('This is Ogden, Iowa.')

//draw a line along the route









//layer control
var baseMaps = {
	"Street Map": osm,
	"Countries": countries

/*
    "Grayscale": grayscale,
    "Streets": streets
*/
};

var overlayMaps = {
    "Ogden": ogden,
    "Current Location": currentMarker,
    "Prime Meridian & Equator": graticule180,
    "45th Parallel": graticule45
};


L.control.layers(baseMaps, overlayMaps).addTo(map);


    $("#classTotalDistance").html("<b>"+distance.toLocaleString()+"</b>");

    $("#selectedTeacher").change( function() {
	    currentMarker.remove();

		newLng = homeLng + adjustDistance;
		console.log(newLng);
		var currentMarker = L.marker([42, newLng ]).addTo(map);
		currentMarker.bindPopup('I am not really sure where I am ...<br>but I know I am '+distance+' miles from home!')
    });



});





</script>



</body>
</html>
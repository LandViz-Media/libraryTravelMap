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
	width: 516px;
	height: 520px;
	margin: 4px 0px 4px 4px;
	padding: 0;
	float:left;
	box-sizing: border-box;
/*
	border-left: 4px solid black;
	border-top: 4px solid black;
	border-bottom: 4px solid black;
*/
}


#classDataContainer {
	background:#e0ff79;
	width: 100%;
	height: 528px;
	box-sizing: border-box;
	border: 4px solid black;
/* 	border-left: 0px solid black; */
}


#classData {
	float:left;
	background:red;
	width: auto;
	margin-right: 0px
/*
	width: 100%;
	height: 520px;
	padding: 10px;
*/
box-sizing: border-box;
	border-left: 2px solid black;
	margin-left: 0px;
	box-sizing: border-box;
}


#classData h3{
	text-align: center;
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
	<div>
		<div id="map"></div>

		<div id ="classDataContainer">
			<div id ="classData">
			<h3> Class Data </h3>
			Total distance: <span id = "classTotalDistance"> 0 </span> miles as of <em>date</em>.
			</div>
		</div>
		<!-- End Class Data Div -->
	</div>


















<script>

    // Sphere Mollweide: http://spatialreference.org/ref/esri/53009/
    var crs = new L.Proj.CRS('ESRI:53009', '+proj=moll +lon_0=0 +x_0=0 +y_0=0 +a=6371000 +b=6371000 +units=m +no_defs', {
        resolutions: [65536, 32768, 16384, 8192, 4096, 2048]
    });






var map = L.map('map', {
        minZoom: 0,
        maxZoom: 3,
        worldCopyJump: false,
        //crs: crs,
		continuousWorld: false,
});







// add an OpenStreetMap tile layer
var osm = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
  attribution: 'The map contributors',
  maxZoom: 18,
  // noWrap: true
}).addTo(map);


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



    L.geoJson(countries, {
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
    }).addTo(map);

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

adjustDistance = (distance/100) * d100;

//---------------


homeLng = -94.030556;
var ogden = L.marker([42, homeLng ]).addTo(map);
ogden.bindPopup('This is Ogden, Iowa.')

newLng = homeLng + adjustDistance;
console.log(newLng);
var currentMarker = L.marker([42, newLng ]).addTo(map);
currentMarker.bindPopup('I am not really sure where I am ...<br>but I know I am '+distance+' miles from home!')

//draw a line along the route











//layer control
var baseMaps = {
	"Street Map": osm

/*
    "Grayscale": grayscale,
    "Streets": streets
*/
};

var overlayMaps = {
    "Ogden": ogden,
    "Current Location": currentMarker
};







// Shorthand for $( document ).ready()
$(function() {
    console.log( "ready!" );
    $("#classTotalDistance").html("<b>"+distance.toLocaleString()+"</b>");
});





</script>



</body>
</html>
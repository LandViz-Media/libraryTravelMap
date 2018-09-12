<?php

//header('Content-type: text/plain');
require("../../conn1.php");

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

<meta charset="UTF-8">



<title>Ogden Elem. Library Map 2018</title>
<style>
html, body {
    height:100%;
    margin:0;
    padding:0;
/* 	height: 100vh; */
}



#mainContainer {
/*     display: flex; */
    width: 100%;
    padding: 0px;
    height: 100%;
/*     background: lightgrey; */
    box-sizing: border-box;
/*     border: 4px solid black; */
	}


#map {
	width: 100%;
	height: calc(100% - 300px);
/* 	position: absolute; */
	margin: 0px;
	padding: 0;
	box-sizing: border-box;
	background-color: grey;
}



#classDataContainer {
/* 	flex: 1; /* my goal is that the width always fills up independent of browser width */ */
	background: rgba(0, 0, 0, 0.05);
	margin-left: 0px;
	margin-top: 0px;
	padding: 0px;
/* 	height: 516px; */
/* 	height: 30%; */
	box-sizing: border-box;
/* 	border: 2px solid black; */
}

#classData {
	background: white;
/* 	height: 516px; */
	padding: 10px;
	padding-top: 10px;
	height: 50px;
	box-sizing: border-box;
/* 	border-left: 2px solid black; */
	margin: 0px;

}

#barChart {
	min-width: 310px;
	height: 250px;
	margin: 0 auto;
}


</style>


<!-- Load jQuery -->
<link href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
<script src="https://code.jquery.com/jquery-3.1.0.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


<!-- Load turf --><script src="https://cdn.jsdelivr.net/npm/@turf/turf@5/turf.min.js"></script>


<!-- Load Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.4/dist/leaflet.css"
   integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA=="
   crossorigin=""/>
<!-- Make sure you put this AFTER Leaflet's CSS -->
 <script src="https://unpkg.com/leaflet@1.3.4/dist/leaflet.js"
   integrity="sha512-nMMmRyTVoLYqjP9hrbed9S+FzjZHW5gY1TWCHA5ckwXZBadntCNs8kEqAWdrb9O7rxbCaA4lKTIWjDXZxflOcA=="
   crossorigin=""></script>

  <!-- load highcharts -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script



<!-- load helper classes -->
<script src="classes/icons.js"></script>
<script src="classes/L.Graticule.js"></script>
<script src="classes/moment.js"></script>


<script src="https://indicator.extension.iastate.edu/classes/leaflet-ajax-gh-pages/dist/leaflet.ajax.min.js"></script>

<script src="https://indicator.extension.iastate.edu/classes/Leaflet.Geodesic-master/Leaflet.Geodesic.js"></script>

<link rel="stylesheet" href="https://indicator.extension.iastate.edu/classes/leaflet-awesome-markersv2/dist/leaflet.awesome-markers.css" />

<script src="https://indicator.extension.iastate.edu/classes/leaflet-awesome-markersv2/dist/leaflet.awesome-markers.js"></script>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />

<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" type="text/css" />


<script type="text/javascript" src="classes/proj4js-compressed.js"></script>
<script type="text/javascript" src="classes/proj4leaflet.js"></script>


<!-- load external data -->
<script type="text/javascript" src="classes/mapTiles.js"></script>
<script type="text/javascript" src="classes/countries-110m.js"></script>
<script type="text/javascript" src="classes/mapOverlays.js"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>




</head>

<body>

		<div id = "mainContainer">


			<div id="map"></div>

	<div id ="classDataContainer">
		<div id ="classData">
<!-- 		<h3> Class Data </h3> -->

		Class: <select id="selectedTeacher">
			<option value="-"></option>
			<?php print $teacherSelect ?>
		</select>
			&nbsp; 	&nbsp; 	&nbsp;

		<img id="selectColorMarker" src="images/whiteSpace.png" alt="color marker" height="21" width="13"> <span id = "classTotalDistanceDate"></span>
		</div>
		<div id="barChart"></div>
	</div> <!-- End Class Data Div -->
</div>  <!-- End Main Container Div -->

<script>
//var currentMarker;
var map, line, along;
var dates = '';
var miles = '';


// Shorthand for $( document ).ready()
$(function() {
  console.log("ready!");



  //var dist_a = 0;
  var classMarkerColor = 'white';
  var classMarker = 'empty';

  var map = L.map('map').setView([47.0, -97.5], 3);


url1 = 'https://c.tiles.wmflabs.org/hillshading/${z}/${x}/${y}.png';
url2 = 'http://{s}.tile.osm.org/{z}/{x}/{y}.png';

  // add an OpenStreetMap tile layer as base map
  L.tileLayer(url2, {
    attribution: 'The map contributors',
    maxZoom: 18
  }).addTo(map);



  function statePopup(feature, layer) {
    layer.bindPopup("Name: " + feature.properties.name + "<br>Density: " + feature.properties.density + " people / sq mile");
  }

  function cityPopup(feature, layer) {
    layer.bindPopup(feature.properties.name + ", " + feature.properties.state);
  }




  var states = new L.GeoJSON.AJAX("https://raw.githubusercontent.com/dakotaBear/libraryTravelMap/master/states.geojson", {
    onEachFeature: statePopup,
    style: function(feature) {
      return {
        fillColor: feature.properties.regionColor,
        color: 'black',
        weight: 1,
        fillOpacity: 0.15
      };
    }
  }).addTo(map);






  var cityStar = L.icon({
    iconUrl: 'https://raw.githubusercontent.com/dakotaBear/libraryTravelMap/master/star.png',
    iconSize: [12, 12], // size of the icon
    iconAnchor: [6, 6], // point of the icon which will correspond to marker's location
    popupAnchor: [0, -6] // point from which the popup should open relative to the iconAnchor
  });




  var stateCapitols = new L.GeoJSON.AJAX("https://raw.githubusercontent.com/dakotaBear/libraryTravelMap/master/stateCapitolCity.geojson", {

    pointToLayer: function(feature, latlng) {
      return L.marker(latlng, {
        icon: cityStar
      });
    },
    onEachFeature: cityPopup
  }).addTo(map);


  usRouteURL = "https://raw.githubusercontent.com/dakotaBear/libraryTravelMap/master/usTravelMap_2018.geojson";


  //this would draw a non geodesic route line
  //var geojsonLayer = new L.GeoJSON.AJAX(usRouteURL);
  //geojsonLayer.addTo(map);

  //style for geodesic route
  var geodesicLayer = L.geodesic([], {
    weight: 3,
    opacity: 0.95,
    color: 'red',
    steps: 50,
    wrap: false,
  }).addTo(map);

  //parse out the coords for use with Turf.js
  jQuery.when(
    jQuery.getJSON(usRouteURL)
  ).done(function(json) {

    //Draw the route as a geodesic line - not sure why this has to us json and cannot use usRouteURL
    geodesicLayer.geoJson(json);





    //console.log(json.features[0].geometry.coordinates);
    console.log("segments: " + json.features[0].geometry.coordinates.length);
    routeLength = json.features[0].geometry.coordinates.length;


    var routeArray = [];
    for (var i = 0; i < routeLength; i++) {
      x = json.features[0].geometry.coordinates[i][0];
      y = json.features[0].geometry.coordinates[i][1];
      routeArray.push([x, y]);
    }


    //draws a non geodesic turfline
    line = turf.lineString(routeArray);
    //L.geoJson(line, {color:"grey", weight:13, opacity:0.25}).addTo(map);

    var routeDistanceMiles = turf.length(line, {
      units: 'miles'
    });
    console.log("Route Distance in Miles " + routeDistanceMiles);

  });


  var ogden = L.marker([42, -94.030556], {
    icon: bulldog21
  }).addTo(map);
  ogden.bindPopup('<img src="images/bulldogWordMark.png" alt="Ogden Community School" height="78" width="200">')

  $("#selectedTeacher").change(function() {

    //Load in the marker data
    grade = $("#selectedTeacher").val();
    selectedTeacher = $("#selectedTeacher :selected").text();
    console.log(selectedTeacher);



    $.getJSON("dataSumByAllClasses.php?selectedTeacher=" + selectedTeacher, function(data1) {
      console.log(data1.length);
      console.log(data1[0].totalMiles);
      dist_a = data1[0].totalMiles;
      dayColor = data1[0].dayColor;

      percentComplete = numeral(dist_a/14450).format('0.0%');

	  $("#classTotalDistanceDate").html("Class has traveled "+ numeral(dist_a).format('0,0')+" miles. "+percentComplete+" of the way to Hawaii!");


      var options = {
        units: 'miles'
      };

      var along = turf.along(line, dist_a, options);
      //L.geoJson(along).addTo(map);
      //L.geoJson(along, {icon: blueIcon}).addTo(map);

      console.log(along);

      map.setView([along.geometry.coordinates[1], along.geometry.coordinates[0]], 6);

      if (classMarker != "empty") {

        map.removeLayer(classMarker)
      }



      //set color of marker
      function getMarkerColor(d) {
        return d == 'yellow' ? 'http://www.googlemapsmarkers.com/v1/ff0' :
          d == 'green' ? 'http://www.googlemapsmarkers.com/v1/00b205' :
          d == 'red' ? 'http://www.googlemapsmarkers.com/v1/ff0000' :
          d == 'blue' ? 'http://www.googlemapsmarkers.com/v1/007dcd' :
          d == 'yellow' ? 'http://www.googlemapsmarkers.com/v1/FFFF00' :
          'http://www.googlemapsmarkers.com/v1/FFFFFF'; //white
      }


      classMarker = L.marker([along.geometry.coordinates[1], along.geometry.coordinates[0]], {
        icon: L.icon({
          iconUrl: getMarkerColor(dayColor),
          iconSize: [20, 34],
          iconAnchor: [10, 34],
          popupAnchor: [0, -20]
        }),
        title: dist_a + " miles",
        opacity: 1.0
      }).addTo(map);





      //Add Color marker behind Teacher's Name





   //Get Graph Info
		var processed_json_dates = new Array();
		var processed_json_miles = new Array();
		$.getJSON( "dataAllSelectedClass.php?selectedTeacher="+selectedTeacher, function( data ) {
			//console.log(data.length);
			barChartColor = data[0].dayColorCode;



			$.each(data, function(i, item) {
				processed_json_dates.push(moment(item.date).format('M-D'));
				//dates = dates + date+", ";
				//miles = miles + " " +parseInt(item.miles)+", ";
				processed_json_miles.push(parseInt(item.miles));

			});

			dates = dates.replace(/,\s*$/, "");
			miles = miles.replace(/,\s*$/, "");



/*
			console.log(miles);
			console.log(dates);
			console.log(processed_json_miles);
*/






			//Draw the HighCharts

			Highcharts.chart('barChart', {
			    chart: {
			        type: 'column'
			    },
			    title: {
			        //text: 'Daily Mileage Graph'
			        text: null
			    },
				legend: {
					enabled: false
				},
			    exporting: {
				    enabled: false
				},
			    xAxis: {
			        categories: processed_json_dates
			    },
			    yAxis: {
				 	max: 500,
				 	tickInterval: 100,
				 	lineColor: '#FF0000',
				 	lineWidth: 1,
				 	title: {
				 		text: 'Potential Miles'
            		},
				},
			    credits: {
			        enabled: false
			    },
			    series: [{
			        name: "Class: " + selectedTeacher,
			        data: processed_json_miles,
			        color: barChartColor
			    }]
			}); //end highcharts



		});






    });

  });

});

</script>



</body>
</html>
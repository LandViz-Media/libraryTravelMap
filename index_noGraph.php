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
<title>Ogden Elem. Library Map</title>
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
    height: 75%;
    background: lightgrey;
    box-sizing: border-box;
    border: 4px solid black;
	}


#map {
	width: 100%;
	height: 100%;
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
	box-sizing: border-box;
}

#classData {
	background: white;
/* 	height: 516px; */
	padding-left: 10px;
	padding-right: 10px;
	box-sizing: border-box;
	border-left: 2px solid black;
	margin: 0px;
}


#classData h3{
	text-align: center;
	margin-top: 0px;
	padding-top: 10px;
}




</style>

<!-- Load jQuery -->
<link href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
<script src="https://code.jquery.com/jquery-3.1.0.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


<!-- Load Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.2.0/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.2.0/dist/leaflet.js"></script>


<!-- load helper classes -->
<script src="classes/icons.js"></script>
<script src="classes/L.Graticule.js"></script>
<script src="classes/moment.js"></script>


<script type="text/javascript" src="classes/proj4js-compressed.js"></script>
<script type="text/javascript" src="classes/proj4leaflet.js"></script>


<!-- load external data -->
<script type="text/javascript" src="classes/mapTiles.js"></script>
<script type="text/javascript" src="classes/countries-110m.js"></script>
<script type="text/javascript" src="classes/mapOverlays.js"></script>

<!-- load highcharts -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script





</head>

<body>
	<div id = 'mainContainer'>
		<div id="map"></div>

	</div>  <!-- End Main Container Div -->

	<div id ="classDataContainer">
		<div id ="classData">
		<h3> Class Data </h3>

		Class: <select id="selectedTeacher">
			<option value="-"></option>
			<?php print $teacherSelect ?>
		</select>
		<br><br>

		<img id="selectColorMarker" src="images/whiteSpace.png" alt="color marker" height="21" width="13"> <span id = "classTotalDistanceDate"></span>
		</div>
	</div> <!-- End Class Data Div -->

	<div id="container1" style="min-width: 310px; height: 300px; margin: 0 auto"></div>







<script>


	//var currentMarker;
	var map, markerColor, classPolyline;

// Shorthand for $( document ).ready()
$(function() {
    console.log( "ready!" );



//map layers are loaded through an external script











    // Sphere Mollweide: http://spatialreference.org/ref/esri/53009/
    var crs = new L.Proj.CRS('ESRI:53009', '+proj=moll +lon_0=0 +x_0=0 +y_0=0 +a=6371000 +b=6371000 +units=m +no_defs', {
        resolutions: [65536, 32768, 16384, 8192, 4096, 2048]
    });


	var map = L.map('map', {
	        minZoom: 1,
	        maxZoom: 16,
	        worldCopyJump: false,
	        //crs: crs,
			continuousWorld: false,
	});

	map.setView([42.04, -94.030556], 3);
	//map.fitWorld();
	Stamen_Toner.addTo(map);
	graticuleOutline.addTo(map);
	graticule45.addTo(map);





/*

	var layer = new L.TileLayer("http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
	    noWrap: true
	});
	*/






	//---------------Distance Calculations
	//42.04, -94.030556  is Ogden

	//95.9482773   - 94  = 1.9482773  = 100 miles
	//1.94929  alternative calcualtion
	//1.94250  alternative calcualtion


	//http://stevemorse.org/nearest/distance.php
	d500 = 9.717525;  //ellipsoidal earth 500 miles at lat 42 is 9.717525
	d100 = d500/5;




	var homeLng = -94.030556;

	var distance, adjustDistance;

	var dates = '';
	var miles = '';



	var ogden = L.marker([42, homeLng ], {icon: bulldog21}).addTo(map);
	ogden.bindPopup('This is Ogden, Iowa.')

	//---------------





    $("#selectedTeacher").change( function() {
/*
		if (typeof currentMarker != "undefined") {
			//console.log("removed current marker");
			//currentMarker.remove();
		}
*/

		if (typeof allMarkers != "undefined") {
			allMarkers.clearLayers();
			classPolylines.remove();
		}


		//Load in the marker data
		grade = $("#selectedTeacher").val();
		selectedTeacher = $("#selectedTeacher :selected").text();
		console.log(selectedTeacher);



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

			Highcharts.chart('container1', {
			    chart: {
			        type: 'column'
			    },
			    title: {
			        text: 'Daily Mileage Graph'
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
			});









		});




		//Get Map Info
		$.getJSON( "dataSumByClass.php?grade="+grade, function( data ) {
			//console.log(data.length);

			var dateLine = false;

			//var cities = L.layerGroup([littleton, denver, aurora, golden]);

			allMarkers = L.layerGroup([]);
			classPolylines = L.layerGroup([]);

			$.each(data, function(i, item) {

				distance = item.totalMiles;
				adjustDegrees = (distance/100) * d100;
				newLng = homeLng + adjustDegrees;

/*
				if (newLng=>180) {
					x = newLng - 180;
					newLng = -180 + x;
					dateLine = true;
				}
*/

				dateLine = false;


				date = item.date;

				date = moment(date).format('MMMM Do');


				markerColor = eval(item.dayColor+"IconSmall");


				if (item.teacher == selectedTeacher) {

					markerColor = eval(item.dayColor+"Icon");

					$("#classTotalDistanceDate").html("Total distance: <b>"+distance.toLocaleString()+"</b> miles.")
					// as of "+date);

					//$("#classData").css("background", item.dayColorCode );

					$("#selectColorMarker").attr('src','https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-'+item.dayColor+'.png');


					pointA = new L.LatLng(42, homeLng);
					pointB = new L.LatLng(42, newLng);

					if (dateLine==true) {
						pointB = new L.LatLng(42, 180)
						pointC = new L.LatLng(42, -180)
						pointD = new L.LatLng(42, newLng)
						pointList = [pointA, pointB];
						pointList2 = [pointC, pointD];

						classPolyline = new L.Polyline(pointList, {
						    color: item.dayColorCode,
						    weight: 3,
						    opacity: 0.8,
						    smoothFactor: 1,
						    dashArray: '10,5',
						    lineJoin: 'round'
						});
						classPolyline.addTo(classPolylines);

						classPolyline = new L.Polyline(pointList2, {
						    color: item.dayColorCode,
						    weight: 3,
						    opacity: 0.8,
						    smoothFactor: 1,
						    dashArray: '10,5',
						    lineJoin: 'round'
						});
						classPolyline.addTo(classPolylines);


					}else{
						pointList = [pointA, pointB];

						classPolyline = new L.Polyline(pointList, {
							color: item.dayColorCode,
							weight: 3,
						    opacity: 0.8,
						    smoothFactor: 1,
						    dashArray: '10,5',
						    lineJoin: 'round'
						});
						classPolyline.addTo(classPolylines);
					}

					classPolylines.addTo(map);
				};









					newMarker = L.marker([42, newLng ], {icon: markerColor});
					newMarker.bindPopup('Class: '+item.teacher+'<br>I am not really sure where I am ...<br>but I know I am '+distance+' miles from home!')





					newMarker.addTo(allMarkers);


					////L.marker([51.5, -0.09], {icon: greenIcon}).addTo(map);





			});

				allMarkers.addTo(map);






		}); //end get JSON for selected teacher

    });  //end selected teacher change function




    	//layer control
	var baseMaps = {
		"B/W State & Country": Stamen_Toner,
		"Grey State & Country": Stamen_TonerLite,
		"OpenStreetMap - International": osm,
		"World Street Map": Esri_WorldStreetMap,
		"National Geographic": Esri_NatGeoWorldMap,
		"World Imagery": Esri_WorldImagery,
		"Physical World": Esri_WorldPhysical,
		"Ocean Basemap": Esri_OceanBasemap,
		"Terrain Background": Stamen_TerrainBackground,
		"Open Topo": OpenTopoMap,
		"Shaded Relief": Esri_WorldShadedRelief,
		"Earth at Night 2012": NASAGIBS_ViirsEarthAtNight2012,
		"Dark Matter!": CartoDB_DarkMatter,
		//"Countries": countriesMap
	};

	var overlayMaps = {
	    "Ogden": ogden,
	    //"Current Location": currentMarker,
	    "Prime Meridian & Equator": graticule180,
	    "45th Parallel": graticule45
	};
	L.control.layers(baseMaps, overlayMaps).addTo(map);


});





</script>



</body>
</html>
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
<title>Library Travel Map Data Entry</title>
<style>
html, body {
    /*height:100%;*/
    margin:10px;
    padding:0;
	height: 100vh;
}
</style>



<!-- Load jQuery -->
<link href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
<script src="https://code.jquery.com/jquery-3.1.0.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


<script>
$( function() {

	var teacher;
	var grade;

    $( "#datepicker" ).datepicker();
	$( "#datepicker" ).datepicker('setDate', new Date());


	$( "#selectedTeacher" ).change( function() {;
		teacher = $( "#selectedTeacher option:selected" ).text();
		grade = $( "#selectedTeacher" ).val();

		$('#pastEntries').load('dataList.php', { teacher: teacher });
	});




	// this is the id of the form
$("#submitForm").submit(function(e) {

	miles = $( "#miles" ).val();
	earnedDate = $( "#datepicker" ).val();
	teacher = $( "#selectedTeacher option:selected" ).text();
	grade = $( "#selectedTeacher" ).val();


    $.post( "dataAdd.php", { teacher: teacher, grade: grade, miles: miles, earnedDate: earnedDate }, function(data, status){
        console.log("Data: " + data + "\nStatus: " + status);

        if (status == "success") {
	        $('#pastEntries').load('dataList.php', { teacher: teacher });

	       $( "#miles" ).val('');
	       $( "#datepicker" ).datepicker('setDate', new Date());
	       $( "#selectedTeacher option:selected" ).val('-');
		  $( "#selectedTeacher" ).val('');

        }




    });

    e.preventDefault(); // avoid to execute the actual submit of the form.
});







});
</script>
</head>

<body>


	<form id="submitForm">
  Class:

  <select id="selectedTeacher">
  <option value="-"></option>
  <?php print $teacherSelect ?>
</select>
<br>

  Miles: <input type="text" id="miles" value="0"><br>

Date: <input type="text" id="datepicker" name="earnedDate"></p>

  <input type="submit" value="Submit">
</form>

<hr>

<div id= 'pastEntries'> </div>

</body>
</html>
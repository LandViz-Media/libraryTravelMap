<?php
//header('Content-type: text/plain');
require("../../conn1.php");

$mysqli = new mysqli($hostname, $username, $password, $database);
// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}


$table = 'libraryTravelMap_miles';

$teacher = $_POST['teacher'];
$grade = $_POST['grade'];
$miles = $_POST['miles'];
$earnedDate = $_POST['earnedDate'];

if (isset($earnedDate)) {
	$earnedDate= date('Y-m-d', strtotime(str_replace('-', '/', $earnedDate)));


	$sql = "INSERT INTO $table (teacher, grade, miles, date)
	VALUES ('$teacher', '$grade', '$miles', '$earnedDate')";

	if ($mysqli->query($sql) === TRUE) {
	    echo "New record created successfully".$earnedDate;
	} else {
	    echo "Error: " . $sql . "<br>" . $mysqli->error;
	}


}


$mysqli->close();
?>
<?php
//header('Content-type: text/plain');
require("conn.php");

$mysqli = new mysqli($hostname, $username, $password, $database);
// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}


$table = 'libraryTravelMap_miles';

$teacher = "Ms. Daby";



date_default_timezone_set('UTC');

$text = '';
$totalMiles = 0;


$sql = "SELECT miles, date FROM $table WHERE teacher = '$teacher'";


$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {

	    $date = date("m-d-Y", strtotime($row["date"]));

	    $miles = $row["miles"];
	    $totalMiles = $totalMiles + $miles;

        echo $date. " ".$miles. "<br>";
    }
} else {
    echo "0 results";
}


print "<br>Total Miles: $totalMiles";


$mysqli->close();
?>
<?php
require("../../conn1.php");

$mysqli = new mysqli($hostname, $username, $password, $database);
// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}



// Turn off all error reporting
error_reporting(0);


$table1 = 'libraryTravelMap_miles';
$table2 = 'libraryTravelMap_teachers';

//$grade = $_GET['grade'];
//$grade = "K";


$teacher = $_GET['selectedTeacher'];
//$teacher = "Ms. Daby";





$sql = "SELECT $table1.miles, $table1.date, $table2.dayColor, $table2.dayColorCode FROM $table1, $table2 WHERE $table1.teacher = '$teacher' AND $table1.teacher = $table2.teacher  ORDER BY date ASC";


$result = $mysqli->query($sql);

header('Content-Type: application/json');


$rows = array();
while($r = mysqli_fetch_assoc($result)) {
    $rows[] = $r;
}
print json_encode($rows);



/*


$result = mysql_query("SELECT ...");
 $rows = array();
   while($r = mysql_fetch_assoc($result)) {
     $rows['object_name'][] = $r;
   }

 print json_encode($rows);
*/





$mysqli->close();
?>
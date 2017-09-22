<!DOCTYPE html>
<html>
<head>
<title>Library Travel Map Data Entry</title>
<style>
html, body {
    /*height:100%;*/
    margin:0;
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
    $( "#datepicker" ).datepicker();
  } );
</script>
</head>

<body>


	<form>
  Class:

  <select id="selectedClass">
  <option value="-"></option>
  <?php print $classSelect ?>
</select>
<br>

  Miles: <input type="text" name="miles" value="0"><br>

Date: <input type="text" id="datepicker"></p>

  <input type="submit" value="Submit">
</form>



</body>
</html>
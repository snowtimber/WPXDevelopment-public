<?php

//require pointin polygon function
require('point_in_polygon.php');

//get the url encoded coords
//echo 'Encoded coordinates passed: ' . htmlspecialchars($_POST["coords"]);

//passed coordinates to an array and min max lat and lon
require('polygon_points_decode.php');

echo "<br><br>";
$Normalized_Production_array = array();

//create an array of dim well id to loop through to get the Cumulative Production DataVision
$Dim_Well_array = array();

$con1=odbc_connect('DataVision','datavision_query','pw');
if (!$con1) {
  exit("Connection Failed: " . $con1);
}

//sample wellname:  $wellname = 'GM 341-14';
//$wellname = $_GET["well_name"];
$sql="SELECT dim_well_id, field_name, well_name, ow_well_id, bottom_lat, bottom_lon, surface_lat, surface_lon, ow_well_name FROM dbo.Dim_Well where bottom_lat>='".$min_lat."' AND bottom_lat<='".$max_lat."' AND bottom_lon<='".$max_lon."' AND bottom_lon>='".$min_lon."' ";
$rs1=odbc_exec($con1,$sql);
if (!$rs1) {
  exit("Error in SQL");
}

$dim_well_string = null;
$i=0;
while (odbc_fetch_row($rs1)) {
  
  $bottom_lat=odbc_result($rs1,"bottom_lat");
  $bottom_lon=odbc_result($rs1,"bottom_lon");
  $pointLocation = new pointLocation();
  $point = $bottom_lat." ".$bottom_lon;
  $well_name=odbc_result($rs1,"well_name");
  $dim_well_id=odbc_result($rs1,"dim_well_id");
  
  if ( $pointLocation->pointInPolygon($point, $polygon) == "inside") {
	echo "well " . $i . " ($well_name)($point): " . $pointLocation->pointInPolygon($point, $polygon) . "<br>";
	
	
	//build  string of dim well  ids
	if ($i == 0) {
	$dim_well_string = $dim_well_id;
	} else {
	$dim_well_string .= '-'.$dim_well_id;
	}
	
	$i++;
	}
	
	if ( $pointLocation->pointInPolygon($point, $polygon) == "vertex") {
	echo "well " . $i . " ($well_name)($point): " . $pointLocation->pointInPolygon($point, $polygon) . "<br>";
	$i++;
	}
	
	/*
  $surface_lat=odbc_result($rs1,"surface_lat");
  $surface_lon=odbc_result($rs1,"surface_lon");
  $ow_well_name=odbc_result($rs1,"ow_well_name");
  $dim_well_id=odbc_result($rs1,"dim_well_id");
  $field_name=odbc_result($rs1,"field_name");
  $well_name=odbc_result($rs1,"well_name");
  $ow_well_id=odbc_result($rs1,"ow_well_id");
  */
  }
  odbc_close($con1);
  
  /*
  //build array of normalized cumulative production data for each well
  foreach ($Dim_Well_array as $array) {
	foreach($array as $x=>$x_value) {
		if( $x == "dim_well"){ 
			$dim_well_id=$x_value;
			require('CumulativeProductionDataRequest.php');
		}
	}
}*/

// Get Production Data for wells identified by dim well id

//clean the dim_well_string from double quotes at the end
//str_replace("\"","",$dim_well_string);

echo '
	<br><br>
	Create a production analysis with the following well IDs: <br>
	<form action="CumulativeProductionDataRequest.php" method="get" target="_blank">
		<input type="text" name="ID" value='. $dim_well_string .'>
		<br><input type="submit">
	</form>';

  require('UserRegister.php');
  //require('production_curve_html.php');
?>

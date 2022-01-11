<?php
//format the encoded points as $polygon = array("-50 30","50 70","100 50","80 10","110 -10","110 -30","-20 -50","-30 -40","10 -10","-10 10","-30 -20","-50 30");
//last and first point in polygon need to be the same
$polygon = str_replace("_", ".", htmlspecialchars($_POST["coords"]));
$polygon = str_replace("|", " ", $polygon);
$polygon = str_replace(",", ',', $polygon);
$position = strpos($polygon, ",");
$first_coord = substr($polygon,0, $position);
//polygon must have first and last point the same
$polygon = $polygon.','.$first_coord;
//echo '<br>decoded polygon: ' . $polygon;

//make coordinates into an array
$polygon = explode(",", $polygon);
$arrlength=count($polygon)-1;
echo "<br>polygon vertices:".$arrlength;

//get max and min lat long to limit the sql query
foreach($polygon as $key => $polygon_points) {
	$position = strpos($polygon_points, " ");
	$lat = substr($polygon_points,0, $position);
	$lon = substr($polygon_points,$position,strlen($polygon_points)-$position);
	//echo "<br><br>lat is ".$lat." and long is ".$lon;

	if ($key == 0) {
		$min_lat= $lat;
		$max_lat= $lat;
		$min_lon= $lon;
		$max_lon= $lon;
	} 
	else {
		if ($lat < $min_lat) {
		$min_lat = $lat;
		} elseif ($lat> $max_lat){
		$max_lat = $lat;
		} 
		if ($lon < $min_lon) {
		$min_lon = $lon;
		} elseif ($lon> $max_lon){
		$max_lon = $lon;
		} 
	}
}

$min_lat = round($min_lat, 8);
$max_lat = round($max_lat, 8);
$min_lon = round($min_lon, 8);
$max_lon = round($max_lon, 8);

//echo "<br><br>min_lat is ".$min_lat." and max_lat is ".$max_lat;
//echo "<br>min_lon is ".$min_lon." and max_lon is ".$max_lon;
?>

<?php
//query general well info and unique identifier from datavision
//___________________________________________________________________________________________________________

$con1=odbc_connect('DataVision','datavision_query','pw');
if (!$con1) {
  exit("Connection Failed: " . $con1);
}

//sample wellname:  $wellname = 'GM 341-14';
//$wellname = $_GET["well_name"];
$sql="SELECT * FROM dbo.Dim_Well where well_name='".$wellname."'";
$rs1=odbc_exec($con1,$sql);
if (!$rs1) {
  exit("Error in SQL");
}

echo "<table><tr>";
echo "<th>dim_well_id</th>";
echo "<th>dim_pad_id</th>";
echo "<th>business_unit_name</th>";
echo "<th>field_name</th>";
echo "<th>well_name</th>";
echo "<th>api</th>";
echo "<th>uwi</th>";
echo "<th>ow_well_id</th>";
echo "<th>surface_lat</th>";
echo "<th>surface_lon</th>";
echo "<th>bottom_lat</th>";
echo "<th>bottom_lon</th>";
echo "<th>ow_well_name</th>";
echo "<th>wsn</th>";
echo "<th>ow_datum_elevation</th>";
while (odbc_fetch_row($rs1)) {
  $dim_well_id=odbc_result($rs1,"dim_well_id");
  $dim_pad_id=odbc_result($rs1,"dim_pad_id");
  $business_unit_name=odbc_result($rs1,"business_unit_name");
  $field_name=odbc_result($rs1,"field_name");
  $well_name=odbc_result($rs1,"well_name");
  $api=odbc_result($rs1,"api");
  $uwi=odbc_result($rs1,"uwi");
  $ow_well_id=odbc_result($rs1,"ow_well_id");
  $bottom_lat=odbc_result($rs1,"bottom_lat");
  $bottom_lon=odbc_result($rs1,"bottom_lon");
  $surface_lat=odbc_result($rs1,"surface_lat");
  $surface_lon=odbc_result($rs1,"surface_lon");
  $ow_well_name=odbc_result($rs1,"ow_well_name");
  $wsn=odbc_result($rs1,"wsn");
  $ow_datum_elevation=odbc_result($rs1,"ow_datum_elevation");  
  echo "<tr><td>$dim_well_id</td>";
  echo "<td>$dim_pad_id</td>";
  echo "<td>$business_unit_name</td>";
  echo "<td>$field_name</td>";
  echo "<td>$well_name</td>";
  echo "<td>$api</td>";
  echo "<td>$uwi</td>";
  echo "<td>$ow_well_id</td>";
  echo "<td>$surface_lat</td>";
  echo "<td>$surface_lon</td>";
  echo "<td>$bottom_lat</td>";
  echo "<td>$bottom_lon</td>";
  echo "<td>$ow_well_name</td>";
  echo "<td>$wsn</td>";
  echo "<td>$ow_datum_elevation</td></tr>";
}
echo "</table>";

$Bottom_lat = $bottom_lat;
$Bottom_lon = $bottom_lon;
$Surface_lat = $surface_lat;
$Surface_lon = $surface_lon;
$WELL_NAME = $well_name;
odbc_close($con1);

?>

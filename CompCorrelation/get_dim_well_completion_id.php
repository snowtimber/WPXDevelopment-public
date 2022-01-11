<?php
//declare/initialize arrays for storing gathered data
//$d2_array = array();
//These are now initialised once before the different wells are looped through in polygonquery.php
//echo "<br><br>dim_well_id =".$dim_well_id;

$con4=odbc_connect('DataVision','datavision_query','pw');
if (!$con4) {
  exit("Connection Failed: " . $con4);
}

//$sql="SELECT * FROM EDMDB_OW_PIC_P.dbo.CD_SURVEY_STATION_T WHERE well_id='".$ow_well_id."' ORDER BY sequence_no ASC, md ASC";
$sql="SELECT * FROM dbo.Dim_Well_Completion where dim_well_id='".$dim_well_id."'";
$rs4=odbc_exec($con4,$sql);
if (!$rs4) {
  exit("Error in SQL");
}
if (isset($_GET["debug"])) {
echo "<table><tr>";
echo "<th>dim_well_completion_id</th>";
echo "<th>dim_well_id</th>";
echo "<th>eu_number</th>";
echo "<th>eu_wellname</th>";
}
//initialise wellname of null
$wellname = null;
while (odbc_fetch_row($rs4)) {
  $var0=odbc_result($rs4,"dim_well_completion_id");
  $var1=odbc_result($rs4,"dim_well_id");
  $var2=odbc_result($rs4,"eu_number");
  $var3=odbc_result($rs4,"eu_wellname");
  
  $dim_well_completion_id = $var0;
  //echo "<br><br>dim_well_completion_id =".$dim_well_completion_id;
  
  // gather an array of wellnames
  $wellname = $var3;
  //$wellname_array[] = $wellname;
  
  if (isset($_GET["debug"])) {
  echo "<tr><td>$var0</td>";
  echo "<td>$var1</td>";
  echo "<td>$var2</td>";
  echo "<td>$var3</td></tr>";
  }
}
//insert into well name outside of while loop so that even if dim_well_id is not found a placeholder is made
$wellname_array[] = $wellname;
if (isset($_GET["debug"])) {
echo "</table>";
}
odbc_close($con4);
?>

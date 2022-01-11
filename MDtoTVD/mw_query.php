<?php
//get mw from ow
//____________________________________________________________________________________________________________________

$con4=odbc_connect('DataVision','datavision_query','pw');
if (!$con4) {
  exit("Connection Failed: " . $conn);
}

//$sql="SELECT * FROM EDMDB_OW_PIC_P.dbo.CD_SURVEY_STATION_T WHERE well_id='".$ow_well_id."' ORDER BY sequence_no ASC, md ASC";
$sql="SELECT * FROM dbo.Fact_Drilling_Day where ow_well_id='".$ow_well_id."' ORDER BY mud_md ASC";
$rs4=odbc_exec($con4,$sql);
if (!$rs4) {
  exit("Error in SQL");
}
if (isset($_GET["debug"])) {
echo "<table><tr>";
echo "<th>report_number</th>";
echo "<th>mud_md</th>";
echo "<th>mud_weight</th>";
echo "<th>dim_well_id</th>";
echo "<th>asset_team</th>";
}
while (odbc_fetch_row($rs4)) {
  $var0=odbc_result($rs4,"report_number");
  $var1=odbc_result($rs4,"mud_md")+$ow_datum_elevation;
  $var2=odbc_result($rs4,"mud_weight");
  $var3=odbc_result($rs4,"dim_well_id");
  $var4=odbc_result($rs4,"asset_team");
  
  //mw*.052*TVD = pressure
  $mud_pressure = $var2*.052*interpolate_tvd($var1, $surveys_array_filtered);
  
  if($var2==0){
		$var2=null;
	}
	if($mud_pressure==0){
		$mud_pressure=null;
	}
  
  //add data to 1d_array
  $d1_array[] = array('tvd' => interpolate_tvd($var1, $surveys_array_filtered), 'mud_weight' => $var2, 'drilling_pressure' => $mud_pressure);
  
  if (isset($_GET["debug"])) {
  echo "<tr><td>$var0</td>";
  echo "<td>$var1</td>";
  echo "<td>$var2</td>";
  echo "<td>$var3</td>";
  echo "<td>$var4</td></tr>";
  }
}
if (isset($_GET["debug"])) {
echo "</table>";
}
odbc_close($con4);
?>

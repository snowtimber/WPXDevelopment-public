<?php
//____________________________________________________________________________________________________________________
//  Get petra Gross Swiped Sand data from datavision for Correlations
 $gross_swiped_sand_array[$iteration] = null;
 
$con8=odbc_connect('DataVision','datavision_query','pw');
if (!$con8) {
  exit("Connection Failed: " . $con8);
}
//echo "connection successs<br><br>";

$ZID = 5;
$FID = 902;

//$sql="SELECT * FROM EDMDB_OW_PIC_P.dbo.CD_SURVEY_STATION_T WHERE well_id='".$ow_well_id."' ORDER BY sequence_no ASC, md ASC";
//  ZID of 5 correlates to the RSVR_PERFMIN_MAX zone data and FID of 40 is SUMGIP2 gross
$sql="SELECT * FROM dbo.View_Piceance_Petra_ZDATA where ZID= '".$ZID."' AND FID= '".$FID."' AND WSN ='".$wsn."' ";
//echo "<br>".$sql;
$rs6=odbc_exec($con8,$sql);
if (!$rs6) {
  exit("Error in SQL");
}
if (isset($_GET["debug"])) {
echo "<table><tr>";
echo "<th>WSN</th>";
echo "<th>FID</th>";
echo "<th>ZID</th>";
echo "<th>Z</th>";
}

while (odbc_fetch_row($rs6)) {
  $var0=odbc_result($rs6,"Z");
  $var1=odbc_result($rs6,"WSN");
  $var2=odbc_result($rs6,"FID");
  $var3=odbc_result($rs6,"ZID");
  
//add data to an array that will be organized by the order that dim_well_ids were processed
//echo "<br><br>swiped sand is right here: ".$iteration." ".$var0;
 $gross_swiped_sand_array[$iteration] = round($var0,4);
  
  if (isset($_GET["debug"])) {
  echo "<tr><td>$var0</td>";
  echo "<td>$var1</td>";
  echo "<td>$var2</td>";
  echo "<td>$var3</td></tr>";
  }
}
if (isset($_GET["debug"])) {
echo "</table>";
}

odbc_close($con8);
?>

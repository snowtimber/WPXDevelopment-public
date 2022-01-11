<?php
//____________________________________________________________________________________________________________________
//  Get WSN from dim_well table in datavision from dim_well_id

$con6=odbc_connect('DataVision','datavision_query','pw');
if (!$con6) {
  exit("Connection Failed: " . $con6);
}
//echo "connection successs<br><br>";

//$sql="SELECT * FROM EDMDB_OW_PIC_P.dbo.CD_SURVEY_STATION_T WHERE well_id='".$ow_well_id."' ORDER BY sequence_no ASC, md ASC";
$sql="SELECT * FROM dbo.Dim_Well where dim_well_id= '".$dim_well_id."' ";
//echo $sql;
$rs6=odbc_exec($con6,$sql);
if (!$rs6) {
  exit("Error in SQL");
}
if (isset($_GET["debug"])) {
echo "<table><tr>";
echo "<th>dim_pad_id</th>";
echo "<th>business_unit_name</th>";
echo "<th>project_area_name</th>";
echo "<th>well_name</th>";
echo "<th>wsn</th>";
}

while (odbc_fetch_row($rs6)) {
  $var0=odbc_result($rs6,"dim_pad_id");
  $var1=odbc_result($rs6,"business_unit_name");
  $var2=odbc_result($rs6,"project_area_name");
  $var3=odbc_result($rs6,"well_name");
  $var4=odbc_result($rs6,"wsn");
  
//add data to an array that will be organized by the order that dim_well_ids were processed
 //echo "<br><br>wsn: ".$var4;
 $wsn = $var4;
 $WSN[] = $var4;
  
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

odbc_close($con6);
?>

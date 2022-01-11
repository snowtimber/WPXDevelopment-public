<?php
//____________________________________________________________________________________________________________________
//  Get petra Gross Swiped Sand data from datavision for Correlations
 $gross_swiped_sand_array[$iteration] = null;
 
$con8=odbc_connect('DataVision','datavision_query','pw');
if (!$con8) {
  exit("Connection Failed: " . $con8);
}
//echo "connection successs<br><br>";

$dim_well_completion_id; = $completion_id_array[$iteration];
$cum_cost = 0;

//$sql="SELECT * FROM EDMDB_OW_PIC_P.dbo.CD_SURVEY_STATION_T WHERE well_id='".$ow_well_id."' ORDER BY sequence_no ASC, md ASC";
//  ZID of 5 correlates to the RSVR_PERFMIN_MAX zone data and FID of 40 is SUMGIP2 gross
$sql="SELECT * FROM Oneline.Cost_Data where dim_well_completion_id='".$dim_well_completion_id."' ORDER BY ACTUAL_AMOUNT ASC";
//echo "<br>".$sql;
$rs6=odbc_exec($con8,$sql);
if (!$rs6) {
  exit("Error in SQL");
}
if (isset($_GET["debug"])) {
echo "<table><tr>";
echo "<th>WELL</th>";
echo "<th>ACTUAL_AMOUNT</th>";
//echo "<th>BUDGET_AMOUNT</th>";
//echo "<th>TASK_SUB_ACCT_DESC</th>";
}

while (odbc_fetch_row($rs6)) {
  $var0=odbc_result($rs6,"WELL");
  $var1=odbc_result($rs6,"ACTUAL_AMOUNT");
 //$var2=odbc_result($rs6,"BUDGET_AMOUNT");
 // $var3=odbc_result($rs6,"TASK_SUB_ACCT_DESC");
  
$cum_cost = $cum_cost + $var1;
  
  if (isset($_GET["debug"])) {
  echo "<tr><td>$var0</td>";
  echo "<td>$var1</td>";
//  echo "<td>$var2</td>";
//  echo "<td>$var3</td></tr>";
  }
}
$cost_array[$iteration] = $cum_cost;

if (isset($_GET["debug"])) {
echo "</table>";
}

odbc_close($con8);
?>

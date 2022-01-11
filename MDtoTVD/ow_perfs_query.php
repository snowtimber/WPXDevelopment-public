<?php
//get perfs from Openwells
//____________________________________________________________________________________________________________________

$con3=odbc_connect('EDM_Win_Auth','EDMDB_OW_PIC_P','pw');
if (!$con3) {
  exit("Connection Failed: " . $conn);
}

$sql="SELECT * FROM EDMDB_OW_PIC_P.dbo.CD_PERF_INTERVAL_T WHERE well_id='".$ow_well_id."' ORDER BY md_top_shot ASC";
$rs3=odbc_exec($con3,$sql);
if (!$rs3) {
  exit("Error in SQL");
}

if (isset($_GET["debug"])) {
echo "<table><tr>";
echo "<th>perf_id</th>";
echo "<th>perf_interval_id</th>";
echo "<th>carrier_size</th>";
echo "<th>charge_phasing</th>";
echo "<th>charge_size</th>";
echo "<th>casing_collar_top_shot</th>";
echo "<th>interval_type</th>";
echo "<th>md_bottom_shot</th>";
echo "<th>md_top_shot</th>";
}
$k=0;
while (odbc_fetch_row($rs3)) {
  $var1=odbc_result($rs3,"perf_id");
  $var2=odbc_result($rs3,"perf_interval_id");
  $var3=odbc_result($rs3,"carrier_size");
  $var4=odbc_result($rs3,"charge_phasing");
  $var5=odbc_result($rs3,"charge_size");
  $var6=odbc_result($rs3,"csg_collar_top_shot");
  $var7=odbc_result($rs3,"interval_type");
  $var8=odbc_result($rs3,"md_bottom_shot")+$ow_datum_elevation;
  $var9=odbc_result($rs3,"md_top_shot")+$ow_datum_elevation;
  
  if ($k==0) {
  $top_limit = interpolate_tvd($var9, $surveys_array_filtered) - 200;
  }
  
  $bottom_limit =  interpolate_tvd($var9, $surveys_array_filtered) +50;

  
  $k++;
  
  $average_depth= ($var8+$var9)/2;
  //echo "<br><br>".interpolate_tvd($average_depth, $surveys_array_filtered);
    //add data to 1d_array
 //$d1_array[] = array('tvd' => interpolate_tvd($average_depth,$surveys_array_filtered), 'md_top_shot' => $var9, 'md_bottom_shot' => $var8, 'interval_type' => $var7, 'charge_size' => $var5);
  $d1_array[] = array('tvd' => interpolate_tvd($average_depth, $surveys_array_filtered), 'interval_type' => $var7, 'tvd_avg_shot' => 0, 'charge_size' => $var5);
  $d1_array[] = array('tvd' => interpolate_tvd($average_depth, $surveys_array_filtered), 'interval_type' => $var7, 'tvd_avg_shot' => 5000, 'charge_size' => $var5);
  $d1_array[] = array('tvd' => 0);
  if (isset($_GET["debug"])) {
  echo "<tr><td>".$var1."</td>";
  echo "<td>$var2</td>";
  echo "<td>$var3</td>";
  echo "<td>$var4</td>";
  echo "<td>$var5</td>";
  echo "<td>$var6</td>";
  echo "<td>$var7</td>";
  echo "<td>$var8</td>";
  echo "<td>$var9</td></tr>";
  }
}
if (isset($_GET["debug"])) {
echo "</table><br><br>";
}

//close odbc connection ->IMPORTANT!!!!
odbc_close($con3);
?>

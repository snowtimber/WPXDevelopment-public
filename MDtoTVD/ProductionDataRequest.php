<?php
//get production data from datavision and create scatter plot with best fit line
//____________________________________________________________________________________________________________________

//get dim well completion id from datavision based off of well name
//____________________________________________________________________________________________________________________

//declare/initialize arrays for storing gathered data
//$d2_array = array();
$Production_array = array();

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
while (odbc_fetch_row($rs4)) {
  $var0=odbc_result($rs4,"dim_well_completion_id");
  $var1=odbc_result($rs4,"dim_well_id");
  $var2=odbc_result($rs4,"eu_number");
  $var3=odbc_result($rs4,"eu_wellname");
  
  $dim_well_completion_id = $var0;
  
  //add data to 1d_array
  //$d1_array[] = array('tvd' => interpolate_tvd($var1, $surveys_array_filtered), 'mud_weight' => $var2, 'drilling_pressure' => $mud_pressure);
  
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
odbc_close($con4);

//____________________________________________________________________________________________________________________
//  Get Production data based off of the dim_well_completion_id

$con5=odbc_connect('DataVision','datavision_query','pw');
if (!$con5) {
  exit("Connection Failed: " . $con5);
}
//echo "connection successs<br><br>";

//$sql="SELECT * FROM EDMDB_OW_PIC_P.dbo.CD_SURVEY_STATION_T WHERE well_id='".$ow_well_id."' ORDER BY sequence_no ASC, md ASC";
$sql="SELECT * FROM dbo.Fact_Production where dim_well_completion_id='".$dim_well_completion_id."' ORDER BY production_date_id ASC";
//echo $sql;
$rs5=odbc_exec($con5,$sql);
if (!$rs5) {
  exit("Error in SQL");
}
if (isset($_GET["debug"])) {
echo "<table><tr>";
echo "<th>dim_well_completion_id</th>";
echo "<th>production_date_id</th>";
echo "<th>gas_production_volume</th>";
echo "<th>oil_production_volume</th>";
echo "<th>casing_production_pressure</th>";
echo "<th>tubing_production_pressure</th>";
echo "<th>sales_volume_oil</th>";
echo "<th>sales_volume_gas</th>";
}
while (odbc_fetch_row($rs5)) {
  $var0=odbc_result($rs5,"dim_well_completion_id");
  $var1=odbc_result($rs5,"production_date_id");
  $var2=odbc_result($rs5,"gas_production_volume");
  $var3=odbc_result($rs5,"oil_production_volume");
  $var4=odbc_result($rs5,"casing_production_pressure");
  $var5=odbc_result($rs5,"tubing_production_pressure");
  $var6=odbc_result($rs5,"sales_volume_oil");
  $var7=odbc_result($rs5,"sales_volume_gas");
  
	if($var2==0){
		$var2=null;
	}
	if($var3==0){
		$var3=null;
	}
  
  //add data to 1d_array
  $Production_array[] = array('date' => $var1, 'gas_production_volume' => $var2, 'oil_production_volume' => $var3);

  if (isset($_GET["debug"])) {
  echo "<tr><td>$var0</td>";
  echo "<td>$var1</td>";
  echo "<td>$var2</td>";
  echo "<td>$var3</td>";
  echo "<td>$var4</td>";
  echo "<td>$var5</td>";
  echo "<td>$var6</td>";
  echo "<td>$var7</td></tr>";
  }
}
if (isset($_GET["debug"])) {
echo "</table>";
}
odbc_close($con5);

//____________________________________________________________________________________________________________________
//  Use javascript to display production data using google visualization scatter plot
?>

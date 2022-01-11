<?php
//____________________________________________________________________________________________________________________
//  Get Production data based off of the dim_well_completion_id

$con5=odbc_connect('DataVision','datavision_query','pw');
if (!$con5) {
  exit("Connection Failed: " . $con5);
}
//echo "connection successs<br><br>";

//$sql="SELECT * FROM EDMDB_OW_PIC_P.dbo.CD_SURVEY_STATION_T WHERE well_id='".$ow_well_id."' ORDER BY sequence_no ASC, md ASC";
$sql="SELECT * FROM dbo.Fact_Production_Month where dim_well_completion_id='".$dim_well_completion_id."' ORDER BY disposition_date_id ASC";
//echo $sql;
$rs5=odbc_exec($con5,$sql);
if (!$rs5) {
  exit("Error in SQL");
}
if (isset($_GET["debug"])) {
echo "<table><tr>";
echo "<th>dim_well_completion_id</th>";
echo "<th>disposition_date_id</th>";
echo "<th>gas_production_volume</th>";
echo "<th>oil_production_volume</th>";
//echo "<th>casing_production_pressure</th>";
//echo "<th>tubing_production_pressure</th>";
echo "<th>sales_volume_oil</th>";
echo "<th>sales_volume_gas</th>";
}

$L=0;
$cum_oil = 0;
$cum_gas = 0;
while (odbc_fetch_row($rs5)) {
  $var0=odbc_result($rs5,"dim_well_completion_id");
  $var1=odbc_result($rs5,"disposition_date_id");
  $var2=odbc_result($rs5,"gas_production_volume");
  $var3=odbc_result($rs5,"oil_production_volume");
  //$var4=odbc_result($rs5,"casing_production_pressure");
  //$var5=odbc_result($rs5,"tubing_production_pressure");
  $var6=odbc_result($rs5,"sales_volume_oil");
  $var7=odbc_result($rs5,"sales_volume_gas");
  
  $cum_oil = $cum_oil + round($var3,0);
  $cum_gas = $cum_gas + round($var2,0);
  
	if($var2==0){
		$var2=null;
	}
	if($var3==0){
		$var3=null;
	}

  //add data to 2d array
  //$Production_array[] = array('date' => $var1, 'gas_production_volume' => $var2, 'oil_production_volume' => $var3);

  //  Normalize date
$date = $var1;
$year = substr($date,0,4);
$month = substr( $date, 4,2);
$day = 01;
$month_since_1900 = $year *12 +$month;
//$your_date = strtotime($year."-".$month."-".$day);
//$date = floor($your_date/(60*60*24*30.4375));
//$date1 = floor($your_date/(60*60*24));

if( $L== 0) {
	$initial_date = $month_since_1900;
	}
	$date = ($month_since_1900- $initial_date);
//echo $initial_date."<br>";
//echo "<br>month is: ".$var1." and your date= ".$your_date."and date floor: ".$date1." and cum date:".$date;

//add data to normalized cumulative 2d array

$Normalized_Production_array[] = array('date' => $date, 'wellname' => $wellname, 'gas_production_volume' => $cum_gas, 'oil_production_volume' => $cum_oil);
//$Normalized_Production_array[] = array('date' => $date, 'wellname' => $wellname, 'gas_production_volume' => $cum_gas);

  
  $L++;
  if (isset($_GET["debug"])) {
  echo "<tr><td>$var0</td>";
  echo "<td>$var1</td>";
  echo "<td>$var2</td>";
  echo "<td>$var3</td>";
  //echo "<td>$var4</td>";
  //echo "<td>$var5</td>";
  echo "<td>$var6</td>";
  echo "<td>$var7</td></tr>";
  }
}
if (isset($_GET["debug"])) {
echo "</table>";
}

$final_date_array[] = $date;
$final_cum_gas_array[] = $cum_gas;
$final_cum_oil_array[] = $cum_oil;
odbc_close($con5);
?>

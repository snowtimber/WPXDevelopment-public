<html>
  <head>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Year', 'Sales', 'Expenses'],
          ['2004',  1000,      null],
          ['2005',  1170,      null],
          ['2006',  660,       null],
          ['2007',  1030,      540],
		  ['2008',  1030,      540]
        ]);

        var options = {
			title: 'Company Performance',
			curveType: 'function',
			orientation: 'vertical',
        };

        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));

        chart.draw(data, options);
      }
    </script>
  </head>
<body>

<form action="well_lookup_to_surveys.php" method="get">
Well Name: <input type="text" name="well_name"><br>
MD value to interpolate to TVD: <input type="text" name="interpolate_value"><br>
<input type="submit">
</form>

<div id="chart_div" style="width: 900px; height: 500px;"></div>

</body>
<?php
//query general well info and unique identifier from datavision
//___________________________________________________________________________________________________________

$con1=odbc_connect('DataVision','datavision_query','pw');
if (!$con1) {
  exit("Connection Failed: " . $con1);
}

//sample wellname:  $wellname = 'GM 341-14';
$wellname = $_GET["well_name"];
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
  echo "<td>$bottom_lat</td>";
  echo "<td>$bottom_lon</td>";
  echo "<td>$ow_well_name</td>";
  echo "<td>$wsn</td>";
  echo "<td>$ow_datum_elevation</td></tr>";
}
echo "</table>";
odbc_close($con1);

//query surveys from Openwells based off ow_well_id
//____________________________________________________________________________________________________________________


$conn=odbc_connect('EDM_Win_Auth','EDMDB_OW_PIC_P','pw');
if (!$conn) {
  exit("Connection Failed: " . $conn);
}

/*
$result = odbc_columns($conn, 'EDMDB_OW_PIC_P', "", '%', "%");
while (odbc_fetch_row($result)) {
    echo odbc_result_all($result);
}
*/
/*
   $result = odbc_tables($conn);

   $tables = array();
   while (odbc_fetch_row($result)){
     //if(odbc_result($result,"TABLE_TYPE")=="TABLE")
       echo"<br>".odbc_result($result,"TABLE_NAME");
	   echo"<br>".odbc_result($result,"TABLE_TYPE");
	   echo"<br>".odbc_result($result,"REMARKS");

   }
   */
$sql="SELECT * FROM EDMDB_OW_PIC_P.dbo.CD_SURVEY_STATION_T WHERE well_id='".$ow_well_id."' ORDER BY sequence_no ASC, md ASC";
$rs=odbc_exec($conn,$sql);
if (!$rs) {
  exit("Error in SQL");
}

$surveys_array=array();

echo "<table><tr>";
echo "<th>well_id</th>";
echo "<th>wellbore_id</th>";
echo "<th>project_id</th>";
echo "<th>md</th>";
echo "<th>tvd</th>";
echo "<th>tvd elevation</th>";
echo "<th>sequence_no</th>";
while (odbc_fetch_row($rs)) {
  $var1=odbc_result($rs,"well_id");
  $var2=odbc_result($rs,"wellbore_id");
  $var3=odbc_result($rs,"project_id");
  $var4=floatval($ow_datum_elevation)+floatval(odbc_result($rs,"md"));
  $var5=floatval($ow_datum_elevation)+floatval(odbc_result($rs,"tvd"));
  $var7=floatval(odbc_result($rs,"tvd"))*(-1);
  $var6=odbc_result($rs,"sequence_no");
  
  //create the surveys array to be used for interpolation (really an array of arrays creating a 2-d array)
  $surveys_array[] = array(odbc_result($rs,"sequence_no"), $var4,  $var5, $var7);
  
  echo "<tr><td>$var1</td>";
  echo "<td>$var2</td>";
  echo "<td>$var3</td>";
  echo "<td>$var4</td>";
  echo "<td>$var5</td>";
  echo "<td>$var7</td>";
  echo "<td>$var6</td></tr>";
}
echo "</table><br><br>";

//close odbc connection ->IMPORTANT!!!!
odbc_close($conn);

//declare surveys_array_filtered
$surveys_array_filtered=array();

//Length of the array
$surveys_array_length = count($surveys_array);
//echo $surveys_array_length."<br><br>";

//set sequence number last to null
$sequence_no_last = "";

//preview array
for ($row = 0; $row < $surveys_array_length; $row++) {
/*
  echo "<p><b>Row number $row</b></p>";
  echo "<ul>";
  */
  
  $sequence_no_now = $surveys_array[$row][0];
  
  //since the sql query orders md and sequece_no by asc, we want the first unique sequence_no row of any similar sequence_no rows, and compile into surveys_array_filtered
  if ($sequence_no_now <> $sequence_no_last) {
	$surveys_array_filtered[] = array($surveys_array[$row][0], $surveys_array[$row][1],  $surveys_array[$row][2], $surveys_array[$row][3]);
  }
  /*
  for ($col = 0; $col < 4; $col++) {
    echo $surveys_array[$row][$col]."&nbsp";
  }
  echo "</ul>";
  */
  
  $sequence_no_last = $sequence_no_now;
}

//Length of the array
$surveys_array_filtered_length = count($surveys_array_filtered);
//echo $surveys_array_filtered_length;

//Preview the filtered array
/*
for ($row = 0; $row < 20; $row++) {
  echo "<p><b>Row number $row</b></p>";
  echo "<ul>";
  for ($col = 0; $col < 4; $col++) {
    echo "<li>".$surveys_array_filtered[$row][$col]."</li>";
  }
  echo "</ul>";
}
*/


//Code to interpolate a given value
//___________________________________________________________________________________________________________
//sample interpolated value: 2555' MD and we want TVD
$interpolate_value = $_GET["interpolate_value"];

// If an interpolate_value has been entered by the user do the following
if (isset($interpolate_value)) {
	
	//declare initial values
	$last_md = 0;
	$last_tvd = 0;

	//iterate through rows until the survey md value is greater than entered interpolate_value
	for ($row = 0; $row < $surveys_array_filtered_length; $row++) {
	  
	  //current values during for loop processing
	  $sequence_no_now = $surveys_array[$row][0];
	  $md_now = $surveys_array_filtered[$row][1];
	  $tvd_now = $surveys_array_filtered[$row][2];
	  
	  if ($md_now == $interpolate_value) {
		$interpolate_result_tvd = $surveys_array_filtered[$row][2];
		}
	  
	  
	  if ($interpolate_value < $surveys_array_filtered[$row][1] and $interpolate_value > $last_md) {
		$interpolate_result_tvd = (($interpolate_value-$last_md)*($tvd_now-$last_tvd)/($md_now-$last_md))+$last_tvd;
		echo "<br><br>Interpolated TVD is: ".$interpolate_result_tvd;
	  
		break 1;
	  }
	  
	  //set previous numbers for next loop
	  $last_md = $surveys_array_filtered[$row][1];
	  $last_tvd =$surveys_array_filtered[$row][2];
	  
	}
	if (isset($interpolate_result_tvd)) {	
	} else {
	echo "<br><br>MD is not within survey range";
	}
	

}  else {
	echo "never performed interpolation";
}

//get frac gradient initial and final for various stim reports
//____________________________________________________________________________________________________________________

$con2=odbc_connect('DataVision','datavision_query','pw');
if (!$con2) {
  exit("Connection Failed: " . $conn);
}

//$sql="SELECT * FROM EDMDB_OW_PIC_P.dbo.CD_SURVEY_STATION_T WHERE well_id='".$ow_well_id."' ORDER BY sequence_no ASC, md ASC";
$sql="SELECT * FROM OneLine.pic_Stim WHERE well_id='".$ow_well_id."'";
$rs2=odbc_exec($con2,$sql);
if (!$rs2) {
  exit("Error in SQL");
}

echo "<table><tr>";
echo "<th>well_common_name</th>";
echo "<th>site_name</th>";
echo "<th>wellbore_id</th>";
echo "<th>zone</th>";
echo "<th>datum</th>";
echo "<th>TOP INTERVAL</th>";
echo "<th>BASE INTERVAL</th>";
echo "<th>job_date</th>";
echo "<th>Job Size</th>";
echo "<th>break_pressure</th>";
echo "<th>Frac Gradient Initial</th>";
echo "<th>Frac Gradient Final</th>";
while (odbc_fetch_row($rs2)) {
  $var1=odbc_result($rs2,"well_common_name");
  $dim_pad_id=odbc_result($rs2,"site_name");
  $business_unit_name=odbc_result($rs2,"wellbore_id");
  $field_name=odbc_result($rs2,"zone");
  $well_name=odbc_result($rs2,"datum");
  $api=odbc_result($rs2,"TOP INTERVAL");
  $uwi=odbc_result($rs2,"BASE INTERVAL");
  $var8=odbc_result($rs2,"job_date");
  $bottom_lat=odbc_result($rs2,"Job Size");
  $bottom_lon=odbc_result($rs2,"break_pressure");
  $ow_well_name=odbc_result($rs2,"Frac Gradient Initial");
  $ow_datum_elevation=odbc_result($rs2,"Frac Gradient Final");  
  echo "<tr><td>$var1</td>";
  echo "<td>$dim_pad_id</td>";
  echo "<td>$business_unit_name</td>";
  echo "<td>$field_name</td>";
  echo "<td>$well_name</td>";
  echo "<td>$api</td>";
  echo "<td>$uwi</td>";
  echo "<td>$var8</td>";
  echo "<td>$bottom_lat</td>";
  echo "<td>$bottom_lon</td>";
  echo "<td>$ow_well_name</td>";
  echo "<td>$ow_datum_elevation</td></tr>";
}
echo "</table>";
odbc_close($con2);

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
while (odbc_fetch_row($rs3)) {
  $var1=odbc_result($rs3,"perf_id");
  $var2=odbc_result($rs3,"perf_interval_id");
  $var3=odbc_result($rs3,"carrier_size");
  $var4=odbc_result($rs3,"charge_phasing");
  $var5=odbc_result($rs3,"charge_size");
  $var6=odbc_result($rs3,"csg_collar_top_shot");
  $var7=odbc_result($rs3,"interval_type");
  $var8=odbc_result($rs3,"md_bottom_shot");
  $var9=odbc_result($rs3,"md_top_shot");
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
echo "</table><br><br>";

//close odbc connection ->IMPORTANT!!!!
odbc_close($con3);

//get mw from ow
//____________________________________________________________________________________________________________________

$con4=odbc_connect('DataVision','datavision_query','pw');
if (!$con4) {
  exit("Connection Failed: " . $conn);
}

//$sql="SELECT * FROM EDMDB_OW_PIC_P.dbo.CD_SURVEY_STATION_T WHERE well_id='".$ow_well_id."' ORDER BY sequence_no ASC, md ASC";
$sql="SELECT * FROM dbo.Fact_Drilling_Day where ow_well_id='".$ow_well_id."' ORDER BY report_number ASC";
$rs4=odbc_exec($con4,$sql);
if (!$rs4) {
  exit("Error in SQL");
}

echo "<table><tr>";
echo "<th>report_number</th>";
echo "<th>mud_md</th>";
echo "<th>mud_weight</th>";
echo "<th>dim_well_id</th>";
echo "<th>asset_team</th>";
while (odbc_fetch_row($rs4)) {
  $var0=odbc_result($rs4,"report_number");
  $var1=odbc_result($rs4,"mud_md");
  $var2=odbc_result($rs4,"mud_weight");
  $var3=odbc_result($rs4,"dim_well_id");
  $var4=odbc_result($rs4,"asset_team");
  echo "<tr><td>$var0</td>";
  echo "<td>$var1</td>";
  echo "<td>$var2</td>";
  echo "<td>$var3</td>";
  echo "<td>$var4</td></tr>";
}
echo "</table>";
odbc_close($con4);







?>



</body>
</html>

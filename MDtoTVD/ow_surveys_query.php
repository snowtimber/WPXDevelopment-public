<?php
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
$surveys_array = array();   
   
$sql="SELECT * FROM EDMDB_OW_PIC_P.dbo.CD_SURVEY_STATION_T WHERE well_id='".$ow_well_id."' ORDER BY sequence_no ASC, md ASC";
$rs=odbc_exec($conn,$sql);
if (!$rs) {
  exit("Error in SQL");
}

$surveys_array=array();
if (isset($_GET["debug"])) {
echo "<table><tr>";
echo "<th>well_id</th>";
echo "<th>wellbore_id</th>";
echo "<th>project_id</th>";
echo "<th>md</th>";
echo "<th>tvd</th>";
echo "<th>tvd elevation</th>";
echo "<th>sequence_no</th>";
}
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
  
  if (isset($_GET["debug"])) {
  echo "<tr><td>$var1</td>";
  echo "<td>$var2</td>";
  echo "<td>$var3</td>";
  echo "<td>$var4</td>";
  echo "<td>$var5</td>";
  echo "<td>$var7</td>";
  echo "<td>$var6</td></tr>";
  }
}
if (isset($_GET["debug"])) {
echo "</table><br><br>";
}

//close odbc connection ->IMPORTANT!!!!
odbc_close($conn);


// Create a cleaned up survey array
//declare surveys_array_filtered
$surveys_array_filtered=array();

//Length of the array
$surveys_array_length = count($surveys_array);
//echo $surveys_array_length."<br><br>";

//set sequence number last to null
$sequence_no_last = "";
$md_last = 0;

//preview array
for ($row = 0; $row < $surveys_array_length; $row++) {
/*
  echo "<p><b>Row number $row</b></p>";
  echo "<ul>";
  */
  
  $sequence_no_now = $surveys_array[$row][0];
   $reality_comparison = $md_last+800;

  
  //since the sql query orders md and sequece_no by asc, we want the first unique sequence_no row of any similar sequence_no rows, and compile into surveys_array_filtered
  if ($sequence_no_now <> $sequence_no_last) {
	if($surveys_array[$row][1] < $reality_comparison) {
		$surveys_array_filtered[] = array($surveys_array[$row][0], $surveys_array[$row][1],  $surveys_array[$row][2], $surveys_array[$row][3]);
	}
  }
  /*
  for ($col = 0; $col < 4; $col++) {
    echo $surveys_array[$row][$col]."&nbsp";
  }
  echo "</ul>";
  */
  
  $sequence_no_last = $sequence_no_now;
  $md_last = $surveys_array[$row][1];
}

//Length of the array
$surveys_array_filtered_length = count($surveys_array_filtered);
//echo $surveys_array_filtered_length;

//Preview the filtered array

/*
for ($row = 0; $row < count($surveys_array_filtered); $row++) {
  echo "<p><b>Row number $row</b></p>";
  echo "<ul>";
  for ($col = 0; $col < 4; $col++) {
    echo "<li>".$surveys_array_filtered[$row][$col]."</li>";
  }
  echo "</ul>";
}
*/


?>

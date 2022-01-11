<?php
//query surveys from Openwells based off ow_well_id
//____________________________________________________________________________________________________________________

$ow_well_id = "G5oIjCB0f3";

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
   
$sql="SELECT * FROM EDMDB_OW_PIC_P.dbo.CD_Perf_interval WHERE well_id='".$ow_well_id."' ORDER BY md_top_shot ASC";
$rs=odbc_exec($conn,$sql);
if (!$rs) {
  exit("Error in SQL");
}

while (odbc_fetch_row($rs)) {
    echo odbc_result_all($rs);
}

/*
$surveys_array=array();
//if (isset($_GET["debug"])) {
echo "<table><tr>";
echo "<th>well_id</th>";
echo "<th>wellbore_id</th>";
echo "<th>md_bottom_shot</th>";
echo "<th>md_top_shot</th>";
echo "<th>perf_diameter</th>";
echo "<th>total_shots</th>";
echo "<th>date_report</th>";
//}
while (odbc_fetch_row($rs)) {
  $var1=odbc_result($rs,"well_id");
  $var2=odbc_result($rs,"wellbore_id");
  $var3=odbc_result($rs,"md_bottom_shot");
  $var4=odbc_result($rs,"md_top_shot");
  $var5=odbc_result($rs,"perf_diameter");
  $var7=odbc_result($rs,"total_shots");
  $var6=odbc_result($rs,"date_report");
  
  //create the surveys array to be used for interpolation (really an array of arrays creating a 2-d array)
  //$surveys_array[] = array(odbc_result($rs,"sequence_no"), $var4,  $var5, $var7);
  
 // if (isset($_GET["debug"])) {
  echo "<tr><td>$var1</td>";
  echo "<td>$var2</td>";
  echo "<td>$var3</td>";
  echo "<td>$var4</td>";
  echo "<td>$var5</td>";
  echo "<td>$var7</td>";
  echo "<td>$var6</td></tr>";
 // }
//}if (isset($_GET["debug"])) {

//echo "</table><br><br>";
}
*/
//close odbc connection ->IMPORTANT!!!!
odbc_close($conn);

?>

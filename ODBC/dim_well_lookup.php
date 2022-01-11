<?php

$con1=odbc_connect('DataVision','datavision_query','pw');
if (!$con1) {
  exit("Connection Failed: " . $con1);
}

$wellname = 'GM 254-2';
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
echo "<th>uwi</th>";
echo "<th>ow_well_id</th>";
echo "<th>bottom_lat</th>";
echo "<th>bottom_lon</th>";
echo "<th>ow_well_name</th>";
echo "<th>ow_datum_elevation</th>";
while (odbc_fetch_row($rs1)) {
  $PAD_NAME=odbc_result($rs1,"dim_well_id");
  $FirstOfWellname=odbc_result($rs1,"dim_pad_id");
  $RIG=odbc_result($rs1,"business_unit_name");
  $RIG_ON=odbc_result($rs1,"field_name");
  $RIG_OFF=odbc_result($rs1,"well_name");
  $APD_MIN_STATUS=odbc_result($rs1,"api");
  $APD_MAX_STATUS=odbc_result($rs1,"uwi");
  $STATE_MIN_EXP=odbc_result($rs1,"ow_well_id");
  $FED_MIN_STATUS=odbc_result($rs1,"bottom_lat");
  $FED_MAX_STATUS=odbc_result($rs1,"bottom_lon");
  $FED_MIN_EXP=odbc_result($rs1,"ow_well_name");
  $LAND=odbc_result($rs1,"ow_datum_elevation");  
  echo "<tr><td>$PAD_NAME</td>";
  echo "<td>$FirstOfWellname</td>";
  echo "<td>$RIG</td>";
  echo "<td>$RIG_ON</td>";
  echo "<td>$RIG_OFF</td>";
  echo "<td>$APD_MIN_STATUS</td>";
  echo "<td>$APD_MAX_STATUS</td>";
  echo "<td>$STATE_MIN_EXP</td>";
  echo "<td>$FED_MAX_STATUS</td>";
  echo "<td>$FED_MIN_STATUS</td>";
  echo "<td>$FED_MIN_EXP</td>";
  echo "<td>$LAND</td></tr>";
}
echo "</table>";
/*
$conn=odbc_connect('EDM_Win_Auth','EDMDB_OW_PIC_P','pw');
if (!$conn) {
  exit("Connection Failed: " . $conn);
}
*/
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
/*
$sql="SELECT * FROM EDMDB_OW_PIC_P.dbo.CD_SURVEY_STATION";
$rs=odbc_exec($conn,$sql);
if (!$rs) {
  exit("Error in SQL");
}

echo "<table><tr>";
echo "<th>well_id</th>";
echo "<th>wellbore_id</th>";
echo "<th>md</th>";
echo "<th>tvd</th>";
while (odbc_fetch_row($rs)) {
  $var1=odbc_result($rs,"well_id");
  $var2=odbc_result($rs,"wellbore_id");
  $var3=odbc_result($rs,"md");
  $var4=odbc_result($rs,"tvd");
  echo "<tr><td>$var1</td>";
  echo "<td>$var2</td>";
  echo "<td>$var3</td>";
  echo "<td>$var4</td></tr>";
}
echo "</table>";
*/
odbc_close($con1);
//odbc_close($conn);
?>



</body>
</html>

<?php
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

odbc_close($conn);
?>



</body>
</html>

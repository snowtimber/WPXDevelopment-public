<html>
<body>

<?php
$conn=odbc_connect('DataVision','datavision_query','pw');
if (!$conn) {
  exit("Connection Failed: " . $conn);
}

$sql="SELECT * FROM OneLine.pic_Pad_Visit_Summary";
$rs=odbc_exec($conn,$sql);
if (!$rs) {
  exit("Error in SQL");
}

echo "<table><tr>";
echo "<th>PAD_NAME</th>";
echo "<th>FirstOfWellname</th>";
echo "<th>RIG</th>";
echo "<th>RIG_ON</th>";
echo "<th>RIG_OFF</th>";
echo "<th>APD_MIN_STATUS</th>";
echo "<th>APD_MAX_STATUS</th>";
echo "<th>STATE_MIN_EXP</th>";
echo "<th>FED_MIN_STATUS</th>";
echo "<th>FED_MAX_STATUS</th>";
echo "<th>FED_MIN_EXP</th>";
echo "<th>LAND</th>";
while (odbc_fetch_row($rs)) {
  $PAD_NAME=odbc_result($rs,"PAD_NAME");
  $FirstOfWellname=odbc_result($rs,"FirstOfWellname");
  $RIG=odbc_result($rs,"RIG");
  $RIG_ON=odbc_result($rs,"RIG_ON");
  $RIG_OFF=odbc_result($rs,"RIG_OFF");
  $APD_MIN_STATUS=odbc_result($rs,"APD_MIN_STATUS");
  $APD_MAX_STATUS=odbc_result($rs,"APD_MAX_STATUS");
  $STATE_MIN_EXP=odbc_result($rs,"STATE_MIN_EXP");
  $FED_MIN_STATUS=odbc_result($rs,"FED_MIN_STATUS");
  $FED_MAX_STATUS=odbc_result($rs,"FED_MAX_STATUS");
  $FED_MIN_EXP=odbc_result($rs,"FED_MIN_EXP");
  $LAND=odbc_result($rs,"LAND");  
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
odbc_close($conn);
echo "</table>";
?>

</body>
</html>

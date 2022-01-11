<html>
<body>

<?php
$conn=odbc_connect('DataVision','datavision_query','pw');
if (!$conn) {
  exit("Connection Failed: " . $conn);
}

$sql_ZONEDEF="SELECT * FROM View_Piceance_Petra_ZONEDEF";
$rs=odbc_exec($conn,$sql_ZONEDEF);
if (!$rs) {
  exit("Error in SQL");
}

echo "<table><tr>";
echo "<th>project_number</th>";
echo "<th>ZID</th>";
echo "<th>NAME</th>";
echo "<th>DESC</th>";
while (odbc_fetch_row($rs)) {
  $project_number=odbc_result($rs,"project_number");
  $ZID=odbc_result($rs,"ZID");
  $NAME=odbc_result($rs,"NAME");
  $DESC=odbc_result($rs,"DESC");
  echo "<tr><td>$project_number</td>";
  echo "<td>$ZID</td>";
  echo "<td>$NAME</td>";
  echo "<td>$DESC</td></tr>";
}
odbc_close($conn);
echo "</table>";
?>

</body>
</html>

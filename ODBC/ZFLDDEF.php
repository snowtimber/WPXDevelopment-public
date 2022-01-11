<html>
<body>

<?php
$conn=odbc_connect('DataVision','datavision_query','pw');
if (!$conn) {
  exit("Connection Failed: " . $conn);
}

$sql_ZFLDDEF="SELECT * FROM View_Piceance_Petra_ZFLDDEF";
$rs=odbc_exec($conn,$sql_ZFLDDEF);
if (!$rs) {
  exit("Error in SQL");
}

echo "<table><tr>";
echo "<th>project_number</th>";
echo "<th>FID</th>";
echo "<th>ZID</th>";
echo "<th>NAME</th>";
echo "<th>SOURCE</th>";
echo "<th>DESC</th>";
while (odbc_fetch_row($rs)) {
  $project_number=odbc_result($rs,"project_number");
  $FID=odbc_result($rs,"FID");
  $ZID=odbc_result($rs,"ZID");
  $NAME=odbc_result($rs,"NAME");
  $SOURCE=odbc_result($rs,"SOURCE");
  $DESC=odbc_result($rs,"DESC");
  echo "<tr><td>$project_number</td>";
  echo "<td>$FID</td>";
  echo "<td>$ZID</td>";
  echo "<td>$NAME</td>";
  echo "<td>$SOURCE</td>";
  echo "<td>$DESC</td></tr>";

}
odbc_close($conn);
echo "</table>";
?>

</body>
</html>

<!doctype html>
<?php
//OPEN ODBC CONNECTION TO DATAVISION
$conn=odbc_connect('DataVision','datavision_query','pw');
if (!$conn) {
  exit("Connection Failed: " . $conn);
}
//open OW ODBC CONNECTION
$con2=odbc_connect('EDM_Win_Auth','EDMDB_OW_PIC_P','pw');
if (!$con2) {
  exit("Connection Failed: " . $con2);
}

//select one record with source = OW and most recent date from PETRA
$sql="SELECT * FROM (Petra ODBC) WHERE (source)='"OPENWELL"' ORDER BY (date) LIMIT 0, 1";
$rs=odbc_exec($conn,$sql);
if (!$rs) {
  exit("Error in SQL");
}
while (odbc_fetch_row($rs)) {
  $var1=odbc_result($rs,"field_name");
  $var2=odbc_result($rs,"Pad_Name");
  $var3=odbc_result($rs,"well_name");
  $var4=odbc_result($rs,"well_spud");
  $var5=odbc_result($rs,"date");
  
  $lastdate = $var5
}
echo "The following perfs have been created in OW since ".$lastdate.", which is the most recent OW perf date uploaded to Petra";

//select all OW perfs that have occurred since the $lastdate, list them and allow them to be made into a csv file.

select all records in OW
//take name parameter sent from well selector and display OW perfs

$wsn = $_GET["wsn"];
//$well_name = $_GET["name"];
//ECHO $well_name."<br><br>";




/*
$sql="SELECT * FROM OneLine.pic_Dim_Well WHERE well_name=".$well_name."";
//echo "<br>".$sql;
$rs=odbc_exec($conn,$sql);
if (!$rs) {
  exit("Error in SQL");
}
*/

//$sql="SELECT * FROM OneLine.pic_Dim_Well ORDER BY wsn DESC";
$sql="SELECT * FROM OneLine.pic_Dim_Well WHERE wsn='".$wsn."' ORDER BY wsn ASC";
//echo "<br>".$sql;
$rs=odbc_exec($conn,$sql);
if (!$rs) {
  exit("Error in SQL");
}

while (odbc_fetch_row($rs)) {
  $var1=odbc_result($rs,"field_name");
  $var2=odbc_result($rs,"Pad_Name");
  $var3=odbc_result($rs,"well_name");
  $var4=odbc_result($rs,"well_spud");
  $var5=odbc_result($rs,"log_date");
  $var7=odbc_result($rs,"aries_first_dlvr_date");
  $var8=odbc_result($rs,"dim_well_id");
  $var9=odbc_result($rs,"wsn");
  $ow_well_id=odbc_result($rs,"ow_well_id");
  
  echo "<br><br>well name: ".$var3;
  echo "<br>wsn: ".$var9;
  }
  
  
//request perf data from OW via DV
$sql2="SELECT * FROM dbo.dim_Perforation_Interval WHERE dim_well_id='".$var8."' ORDER BY md_top_shot ";
//echo "<br>".$sql;
$rs2=odbc_exec($conn,$sql2);

if (!$rs2) {
  exit("Error in DV SQL");
}
echo "<br>OW Data stored in DV:";
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

while (odbc_fetch_row($rs2)) {
  $var1=odbc_result($rs2,"perf_id");
  $var2=odbc_result($rs2,"perf_interval_id");
  $var3=odbc_result($rs2,"carrier_size");
  $var4=odbc_result($rs2,"charge_phasing");
  $var5=odbc_result($rs2,"charge_size");
  $var6=odbc_result($rs2,"csg_collar_top_shot");
  $var7=odbc_result($rs2,"interval_type");
  $var8=odbc_result($rs2,"md_bottom_shot");
  $var9=odbc_result($rs2,"md_top_shot");
  
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

echo "<br>OW Data directly from OW:";
$sql="SELECT * FROM EDMDB_OW_PIC_P.dbo.CD_Perf_interval WHERE well_id='".$ow_well_id."' ORDER BY md_top_shot ASC";
$rs3=odbc_exec($con2,$sql);
if (!$rs3) {
  exit("Error in SQL");
}

while (odbc_fetch_row($rs3)) {
    echo odbc_result_all($rs3);
}


//close odbc connection ->IMPORTANT!!!!
odbc_close($conn);
odbc_close($con2); 
?>
 </html>

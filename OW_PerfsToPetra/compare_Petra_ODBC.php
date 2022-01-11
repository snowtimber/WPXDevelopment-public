<html>
<body>

<?php
//-----------------------------------------------------------------------------------------------
//Compare OW perfs versus Petra perfs
//1.Count number or wells with perfs in petra not OW
//2. Count number of wells with perfs in petra not OW
//-----------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------
//************ Lists that need creating **************
//---------------------------------------------------------------------------------------
//List of wells with perfs that are not in Petra
//List of wells with perfs that are not in OW
//List of wells with unmatching but not contradicting perfs
//List of wells with contradicting perfs
//---------------------------------------------------------------------------------------


//$ow_well_id = G5oIjCB0f3;

//OPEN ODBC CONNECTION TO DATAVISION
$conn=odbc_connect('DataVision','datavision_query','pw');
if (!$conn) {
  exit("Connection Failed: " . $conn);
}

//OPEN ODBC CONNECTION TO Petra
$conn2=odbc_connect("DRIVER={DBISAM 3 ODBC Driver};ConnectionType=Local;CatalogName=Z:/;","","");
if (!$conn2) {
  exit("Connection Failed: " . $conn2);
}

//set counting variables to zero
$OW_reports_with_perfs = 0;
$petra_reports_with_perfs = 0;
$dim_well_wells = 0;
	

//$sql="SELECT * FROM OneLine.pic_Dim_Well ORDER BY wsn DESC";
$sql="SELECT * FROM OneLine.pic_Dim_Well ORDER BY wsn ASC";
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
  
  echo "<br><br>well name: ".$var3;
  echo "<br>wsn: ".$var9;
  
  if ($var9>0){
  $dim_well_wells++;

	//request perf data from OW via DV
	$sql2="SELECT * FROM dbo.dim_Perforation_Interval WHERE dim_well_id='".$var8."' ORDER BY md_top_shot ";
	//echo "<br>".$sql;
	$rs2=odbc_exec($conn,$sql2);
	if (!$rs2) {
	  exit("Error in DV SQL");
	}
	  
	  $OW = 0;
	while (odbc_fetch_row($rs2)) {
	/*
	  $var11=odbc_result($rs2,"dim_well_id");
	  //echo "<br>DV ow perf found with dim well id: ".$var11;
	  $var12=odbc_result($rs2,"date_interval_shot");
	  $var13=odbc_result($rs2,"md_top_shot");
	  $var14=odbc_result($rs2,"md_bottom_shot");
	  $var15=odbc_result($rs2,"interval_type");
	  $var16=odbc_result($rs2,"shot_density");
	  $var17=odbc_result($rs2,"gun_diam_max");
	  */
	  $OW++;
	 
	 }
	 IF($OW>0){
	 //increment the count
	 $OW_reports_with_perfs++;
	 }
	 //echo "var9: ".$var9;
	 
	 //request perf data from petra via petra ODBC
    $sql3="SELECT *
    FROM PERFS
    WHERE WSN=".$var9;
	
	/*$sql3="SELECT COLUMN_NAME
	FROM INFORMATION_SCHEMA.COLUMNS
	WHERE TABLE_NAME = 'PERFS'
	ORDER BY ORDINAL_POSITION"
	*/
	//echo $sql3;
	
	$rs3=odbc_exec($conn2,$sql3);
	if (!$rs3) {
	  exit("Error in Petra ODBC SQL");
	}
	
	  $PETRA=0;
	while (odbc_fetch_row($rs3)) {
	/*
	  $var21=odbc_result($rs3,"WSN");
	  //echo "<br>petra wsn found as: ".$var21;
	  $var22=odbc_result($rs3,"Date");
	  $var23=odbc_result($rs3,"TOP");
	  $var24=odbc_result($rs3,"BASE");
	  $var25=odbc_result($rs3,"DIAMETER");
	  $var26=odbc_result($rs3,"NUMSHOTS");
	  $var27=odbc_result($rs3,"PERFTYPE");
	 */
	  $PETRA++;
	 }
	 IF($PETRA>0){
	 //increment the count
	 $petra_reports_with_perfs++;
	 }

/*
//$surveys_array=array();
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
*/

echo "<br>OW_reports_with_perfs: ".$OW_reports_with_perfs;
echo "<br>petra_reports_with_perfs: ".$petra_reports_with_perfs;
echo "<br>dim_well_wells: ". $dim_well_wells."<br><br>";
}
}

echo "OW_reports_with_perfs: ".$OW_reports_with_perfs;
echo "petra_reports_with_perfs".$petra_reports_with_perfs;
echo "dim_well_wells". $dim_well_wells;

//close odbc connection ->IMPORTANT!!!!
odbc_close($conn);
odbc_close($conn2);

?>

</body>
</html>

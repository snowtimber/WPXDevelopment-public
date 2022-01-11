
<?php

//calendar function to convert date from days since 1-1-1900 to gregorian calendar
function date_convert($format, $xl_date) 
{ 
    $greg_start = gregoriantojd(12, 30, 1899); 
    return date($format, jdtounix($greg_start + $xl_date)); 
} 
//-----------------------------------------------------------------------------------------------
//Compare OW perfs versus Petra perfs
//1.Count number or wells with perfs in petra not OW
//2. Count number of wells with perfs in petra not OW
//-----------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------
//************ Lists that need creating **************
//---------------------------------------------------------------------------------------
//List of all perfs in Petra and save to csv file
//---------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------
//************ Initialize txt files for containg lists **************
//---------------------------------------------------------------------------------------
	//open/create the txt files
	$petra_perfs = fopen("petra_perfs.txt", "w") or die("Unable to open file!");

	//close the txt files
	fclose($petra_perfs);
//---------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------
//************ Procedure **************
	//1. Query dim_wells for list of wells and corresponding well data
	//2. For each well, query OW and Petra for perfs
	//3. Store perfs in arrays
	//4.  Compare arrays against each other
		//4.5 Do this on both an order and value basis and then on just a value basis
	//5. Logic for counting and placing in txt files
	//6.  Return Count Results
//---------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------
//Step 1. Query dim_wells for list of wells and corresponding well data
//---------------------------------------------------------------------------------------

//OPEN ODBC CONNECTION TO DATAVISION
$conn=odbc_connect('DataVision','datavision_query','pw');
if (!$conn) {
  exit("Connection Failed: " . $conn);
}

//set counting variables to zero
$OW_reports_with_perfs = 0;
$petra_reports_with_perfs = 0;
$perfect_match = 0;
$conflicting = 0;
$dim_well_wells = 0;
$OWnotPETRAcount = 0;
$PETRAnotOWcount = 0;
$noPerfs = 0;
$perfect_match_on_values = 0;
$conflicting_on_values = 0;

//$sql="SELECT * FROM OneLine.pic_Dim_Well ORDER BY wsn DESC";
$sql="SELECT * FROM OneLine.pic_Dim_Well WHERE petra_operator= 'WPX' ORDER BY wsn ASC";
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
  echo "<br>wsn: ".$var9."<br>";
  
	//---------------------------------------------------------------------------------------
	//Step 2. For each well, query OW and Petra for perfs
	//---------------------------------------------------------------------------------------
  //make sure wsn is greater than 0
  if ($var9>0){
		//increment the number of wells analyzed
		$dim_well_wells++;
	 
		 //request perf data from petra via DV ODBC
		 $sql3="SELECT * FROM dbo.View_Piceance_Petra_PERFS WHERE WSN='".$var9."' ORDER BY 'TOP' ";
		 //echo $sql3;
		
		$rs3=odbc_exec($conn,$sql3);
		if (!$rs3) {
		  exit("Error in DV ODBC SQL");
		}
	  
		while (odbc_fetch_row($rs3)) {
		
			$var21=odbc_result($rs3,"WSN");
			//echo "<br>petra wsn found as: ".$var21;
			$var22=odbc_result($rs3,"DATE");
			$var23=odbc_result($rs3,"TOP");
			$var24=odbc_result($rs3,"BASE");
			$var25=odbc_result($rs3,"DIAMETER");
			$var26=odbc_result($rs3,"NUMSHOTS");
			$var27=odbc_result($rs3,"PERFTYPE");

			//---------------------------------------------------------------------------------------
			//Step 3. save every PETRA perf as a line item to csv file
			//---------------------------------------------------------------------------------------
			
			$txt = $var21.",".date_convert('Y-m-d', $var22).",".$var23.",".$var24.",".$var25.",".$var26.",".$var27.",PETRA
";
			echo "<br>".$txt;
			
			//save petra perfs to txt file
			$file = 'petra_perfs.txt';
			//current = file_get_contents($file);
			//$current .= trim($txt);
			file_put_contents($file,$txt, FILE_APPEND);
		}
	
	}
}
//close odbc connection ->IMPORTANT!
odbc_close($conn);

?>

</body>
</html>

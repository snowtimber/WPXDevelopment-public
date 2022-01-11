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

//---------------------------------------------------------------------------------------
//************ Initialize txt files for containg lists **************
//---------------------------------------------------------------------------------------
	//open/create the txt files
	$OW_not_Petra = fopen("OW_not_Petra.txt", "w") or die("Unable to open file!");
	$Petra_not_OW = fopen("Petra_not_OW.txt", "w") or die("Unable to open file!");
	$Exact_Matches = fopen("Exact_Matches.txt", "w") or die("Unable to open file!");
	$Conflicting = fopen("Conflicting.txt", "w") or die("Unable to open file!");
	$Exact_Matches_Values = fopen("Exact_Matches.txt", "w") or die("Unable to open file!");
	$Conflicting_Values = fopen("Conflicting.txt", "w") or die("Unable to open file!");

	//close the txt files
	fclose($OW_not_Petra);
	fclose($Petra_not_OW);
	fclose($Exact_Matches);
	fclose($Conflicting);
	fclose($Exact_Matches_Values);
	fclose($Conflicting_Values);
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
  echo "<br>wsn: ".$var9."<br>";
  
	//---------------------------------------------------------------------------------------
	//Step 2. For each well, query OW and Petra for perfs
	//---------------------------------------------------------------------------------------
  //make sure wsn is greater than 0
  if ($var9>0){
		//increment the number of wells analyzed
		$dim_well_wells++;

		//set OW results and Petra results count to zero
		$OW = 0;
		$PETRA=0;
		$OW_array = array();
		$PETRA_array = array();
		$PETRA_perf_line_item_array = array();
		$OW_perf_line_item_array = array();
		$txtOW = null;
		$txtPETRA = null;

		//request perf data from OW via DV
		$sql2="SELECT * FROM dbo.dim_Perforation_Interval WHERE dim_well_id='".$var8."' ORDER BY md_top_shot ";
		//echo "<br>".$sql;
		$rs2=odbc_exec($conn,$sql2);
		
		if (!$rs2) {
		  exit("Error in DV SQL");
		}
	  
	 
	while (odbc_fetch_row($rs2)) {
	
		$var11=odbc_result($rs2,"dim_well_id");
		//echo "<br>DV ow perf found with dim well id: ".$var11;
		$var12=odbc_result($rs2,"date_interval_shot");
		$var13=odbc_result($rs2,"md_top_shot");
		$var14=odbc_result($rs2,"md_bottom_shot");
		$var15=odbc_result($rs2,"interval_type");
		$var16=odbc_result($rs2,"shot_density");
		$var17=odbc_result($rs2,"gun_diam_max");

		$OW++;
	  	
		//---------------------------------------------------------------------------------------
		//Step 3. Store perfs in arrays
		//---------------------------------------------------------------------------------------
		
		//add perf data for well as a line item
		$OW_perf_line_item_array[$OW] = ($var13."-".$var14);
		echo $var13."-".$var14."-".$var12."-".$var15;
		 
		//add the variables to a string that can be exported to csv
		$txtOW .= $var9.",". $var3.",".$var13.",".$var14.",".$var12.",".$var15.",OW
		";

	 }
	 
	 IF($OW>0){
		 //increment the count
		 $OW_reports_with_perfs++;
	 }
	 
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

		$PETRA++;

		//---------------------------------------------------------------------------------------
		//Step 3. Store perfs in arrays
		//---------------------------------------------------------------------------------------
		//add perf data for well as a line item
		$PETRA_perf_line_item_array[$PETRA] = ($var23."-".$var24);
		echo $var23."-".$var24."-".$var22."-".$var27;
		
		//add the variables to a string that can be exported to csv
		$txtPETRA .= $var9.",". $var3.",".$var23.",".$var24.",".$var22.",".$var27.",PETRA
		";

	}
	IF($PETRA>0){
		//increment the count
		$petra_reports_with_perfs++;
	}
	 
	//Feed data to respective txt files depending on criteria:
	//txt = WSN-Wellname(carriage return)
	$txt = $var9.",". $var8."\n";

	//IF In OW and not Petra, then save wsn and wellname to a txt file
	IF($OW>0 and $PETRA == 0){
		$file = 'OW_not_Petra.txt';
		$txt = $txtOW;
		$current = file_get_contents($file);
		$current .= $txt;
		file_put_contents($file, $current);
		$OWnotPETRAcount++;
	}

	//IF In Petra and not OW, then save wsn and wellname to a txt file
	IF($OW==0 and $PETRA > 0){
		$file = 'Petra_not_OW.txt';
		$txt = $txtPETRA;
		$current = file_get_contents($file);
		$current .= $txt;
		file_put_contents($file, $current);
		$PETRAnotOWcount++;
	}

	//---------------------------------------------------------------------------------------
	//Step 4.  Compare arrays against each other
	//---------------------------------------------------------------------------------------
	//Check if Petra and OW both have perfs
	if($PETRA>0 AND $OW>0){
	
		//find differences in perfs
		//this checks array keys and values (checks the order of perfs)
		$OW_not_Petra_perfs=array_diff_assoc($OW_perf_line_item_array,$PETRA_perf_line_item_array);
		$Petra_not_OW_perfs=array_diff_assoc($PETRA_perf_line_item_array,$OW_perf_line_item_array);
		
		//this checks just values
		$OW_not_Petra_perfs_values=array_diff($OW_perf_line_item_array,$PETRA_perf_line_item_array);
		$Petra_not_OW_perfs_values=array_diff($PETRA_perf_line_item_array,$OW_perf_line_item_array);
		
	//---------------------------------------------------------------------------------------
	//Step 5. Logic for counting and placing in txt files
	//---------------------------------------------------------------------------------------		
	
		//define txt to add to files
		$txt = $txtOW.$txtPETRA;
		
		//IF Perfs Match Exactly on order and values, then save wsn and wellname to a txt file
		if($OW_not_Petra_perfs == null and $Petra_not_OW_perfs == null){
			$file = 'Exact_Matches.txt';
			$current = file_get_contents($file);
			$current .= $txt;
			file_put_contents($file, $current);
			$perfect_match++;
		}
		
		//IF conflicting on order and values, then save wsn and wellname to a txt file
		if($OW_not_Petra_perfs <> null or $Petra_not_OW_perfs <> null){
			$file = 'Conflicting.txt';
			$current = file_get_contents($file);
			$current .= $txt;
			file_put_contents($file, $current);
			$conflicting++;
		}
		
		//IF Perfs Match Exactly on values only, then save wsn and wellname to a txt file
		if($OW_not_Petra_perfs_values == null and $Petra_not_OW_perfs_values == null){
			$file = 'Exact_Matches_Values.txt';
			$current = file_get_contents($file);
			$current .= $txt;
			file_put_contents($file, $current);
			$perfect_match_on_values++;
		}
		
		//IF conflicting on values only, then save wsn and wellname to a txt file
		if($OW_not_Petra_perfs_values <> null or $Petra_not_OW_perfs_values <> null){
			$file = 'Conflicting_Values.txt';
			$current = file_get_contents($file);
			$current .= $txt;
			file_put_contents($file, $current);
			$conflicting_on_values++;
		}
	} elseif($PETRA==0 AND $OW==0){
	$noPerfs++;
	}
	
//---------------------------------------------------------------------------------------
//Step 6.  Return Count Results
//---------------------------------------------------------------------------------------

echo "<br>OW_wells_with_perfs: ".$OW_reports_with_perfs;
echo "<br>petra_wells_with_perfs: ".$petra_reports_with_perfs;
echo "<br>OW without Petra: ".$OWnotPETRAcount;
echo "<br>Petra without OW: ".$PETRAnotOWcount;
echo "<br>perfect matches on order and values: ".$perfect_match;
echo "<br>conflicting on order and values: ".$conflicting;
echo "<br>perfect matches on values: ".$perfect_match_on_values;
echo "<br>conflicting on values: ".$conflicting_on_values;
echo "<br>No Perfs: ".$noPerfs;
echo "<br>dim_well_wells: ". $dim_well_wells."<br><br>";
}
}
	 
//close odbc connection ->IMPORTANT!
odbc_close($conn);

?>

</body>
</html>

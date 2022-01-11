<!doctype html>
<?php
//take name parameter sent from well selector and display OW perfs

//$wsn = $_GET["wsn"];

//---------------------------------------------------------------------------------------
//************ Initialize txt files for containing lists **************
//---------------------------------------------------------------------------------------
	//open/create the txt files 
	//this will clear anything in the file
	$OW_perfs = fopen("OPENWELL_perfs.txt", "w") or die("Unable to open file!");

	//close the txt files
	fclose($OW_perfs);
	
	//make the header line in the csv file.
	$txt = "api,md_top_shot,md_bottom_shot,interval_type,Source,date_interval_shot,carrier_size,method,comptype,num_shots
";
	
	//save petra perfs to txt file
	$file = 'OPENWELL_perfs.txt';
	//current = file_get_contents($file);
	//$current .= trim($txt);
	file_put_contents($file,$txt, FILE_APPEND);
	
//----------------------------------------------------------------------------------------
//calendar function to convert date from days since 1-1-1900 to gregorian calendar
function date_convert($format, $xl_date) 
{ 
    $greg_start = gregoriantojd(12, 30, 1899); 
    return date($format, jdtounix($greg_start + $xl_date)); 
} 
//-----------------------------------------------------------------------------------------

//OPEN ODBC CONNECTION TO DATAVISION
$conn=odbc_connect('DataVision','datavision_query','pw');
if (!$conn) {
  exit("Connection Failed: " . $conn);
}

//SQL TO GRAB DIM WELL INFO FROM DV
//$sql="SELECT * FROM OneLine.pic_Dim_Well ORDER BY wsn DESC";
$sql="SELECT * FROM OneLine.pic_Dim_Well ORDER BY wsn ASC";
//echo "<br>".$sql;
$rs=odbc_exec($conn,$sql);
if (!$rs) {
  exit("Error in SQL");
}

/*
//OPEN ODBC CONNECTION TO OW
$con3=odbc_connect('EDM_Win_Auth','EDMDB_OW_PIC_P','pw');
if (!$con3) {
  exit("Connection Failed: " . $con3);
}
*/

//
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
  $uwi =odbc_result($rs,"uwi");
  
  
  $wsn = $var9;
  
  if($wsn>0){
  
	echo "<br><br>well name: ".$var3;
	echo "<br>wsn: ".$wsn;
	echo "<br>uwi: ".$uwi."<br>";
  
	//request perf data from OW via DV
	$sql2="SELECT * FROM dbo.dim_Perforation_Interval WHERE dim_well_id='".$var8."' ORDER BY md_top_shot ASC";
	//echo "<br>".$sql;
	$rs2=odbc_exec($conn,$sql2);
	
	//error handling
	if (!$rs2) {
		exit("Error in DV SQL");
	}
	

		while (odbc_fetch_row($rs2)) {
			$var1=odbc_result($rs2,"perf_id");
			$var2=odbc_result($rs2,"perf_interval_id");
			$var3=odbc_result($rs2,"carrier_size");
			$var4=odbc_result($rs2,"charge_phasing");
			$var5=round(odbc_result($rs2,"charge_size"),3);
			$var6=odbc_result($rs2,"csg_collar_top_shot");
			$var7=odbc_result($rs2,"interval_type");
			$var8=round(odbc_result($rs2,"md_bottom_shot"),0);
			$var9=round(odbc_result($rs2,"md_top_shot"),0);
			$var10=substr(odbc_result($rs2,"date_interval_shot"),0, 10);
			$var11=round(odbc_result($rs2,"gun_diam_max"),3);
			$var12=odbc_result($rs2,"comments"); //this field appears to be hijacked for number of shots
			$interval=odbc_result($rs2,"top_shot_bottom_shot_interval");
			$shot_density=round(odbc_result($rs2,"shot_density"),1);
			
			//initialize Comptype (used as a remark to specify additional perftypes)
			$CompType = null;
			
			//"comments" should hold the interval, but if not, shot_density * interval should yield the same result.
			$numshots = round($var12,2);
			if($numshots == null || $numshots<= 0){
			$numshots = round($interval*$shot_density,0);
			if($numshots<= 0){
				$numshots = null;
				}
			}
			
			//change date formate to mm/dd/yyyy 
			if(isset($var10) and strpos($var10, "-") >0){
			$date = explode("-", $var10);
			$date2 = $date[1]."/".$date[2]."/".$date[0];
			} else {
			$date2 = null;
			}
			
			// To match Petras perf interval format
			//If interval is 1 make bottom perf the same as top perf to make the interval zero
			if($var8-$var9 == 1){
			$var8 = $var9;
			}
			
			//change perf type names
			if($var7 =="PERFORATED"){
				$var7 = 'Active';
			};
			
			//if the charge_size is less than or equal to zero use gun_diam_max
			//if both or less than or equal to zero, use null
			if($var5 <= 0){
				$var5 = $var11;
				
				if($var5 <= 0){
					$var5 = null;
				}
			};
			
			//Change perftype to reflect Petra's options
			if($var7 =="PERFORATED  "){
				$var7 = 'Active';
			};
			if($var7 =="PERFORATED "){
				$var7 = 'Active';
			};
			if($var7 =="INACTIVE MECHANI"){
				$var7 = 'Inactive';
			};
			if($var7 =="SQUEEZE PERF"){
				$var7 = 'Squeezed';
			};
			if($var7 =="SQUEEZED PERF"){
				$var7 = 'Squeezed';
			};
			if($var7 =="REMEDIATION PERF"){
				$var7 = 'Squeezed';
			};
			if($var7 =="NON-STIM PERF"){
				$var7 = 'Non Stim';
				$CompType = "TypOW:NoStm";
			};
			if($var7 ==""){
				$var7 = 'Undefined';
				$CompType = "TypOW:Unk";
			};
			
			//Method and CompType are the same since we simply want to specify if the 
			//perftype is TypOW:Unk (null) or TypOW:NoStim
			$Method = $CompType;
			
			//Since Petra only allows for certain perftype entries (does not include Non-Stim or undefined),
			//Use the Comptype field in Petra to specify whether the perf is Non-Stim or undefined
			
			//csv sample
			//              wsn,        Top,     Base,       Type,        Source, FromDate, Diameter, Method,     CompType (Method and Comptype to be used as a remark for "NON-STIM" and "undefined" perftypes
			
			  $txt = $uwi.", ".$var9.", ".$var8.", ".$var7.", OPENWELL, ".$date2.", ".$var3.",".$Method.",".$CompType.", ".$numshots."
";
			  echo "<br>".$txt;
		  
			//---------------------------------------------------------------------------------------
			//Write the perfs to a csv file
			//---------------------------------------------------------------------------------------
			
			/*
			$txt = $var21.",".date_convert('Y-m-d', $var22).",".$var23.",".$var24.",".$var25.",".$var26.",".$var27.",PETRA
";
			echo "<br>".$txt;
			*/
			
			//save petra perfs to txt file
			$file = 'OPENWELL_perfs.txt';
			//current = file_get_contents($file);
			//$current .= trim($txt);
			file_put_contents($file,$txt, FILE_APPEND);
	  
		}
	}
 }
  
//close odbc connection ->IMPORTANT!!!!
odbc_close($conn);
odbc_close($con2); 
//odbc_close($con3); 
?>
 </html>

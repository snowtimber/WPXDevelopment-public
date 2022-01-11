<?php
//get frac gradient initial and final for various stim reports
//____________________________________________________________________________________________________________________

$con2=odbc_connect('DataVision','datavision_query','pw');
if (!$con2) {
  exit("Connection Failed: " . $conn);
}

//$sql="SELECT * FROM EDMDB_OW_PIC_P.dbo.CD_SURVEY_STATION_T WHERE well_id='".$ow_well_id."' ORDER BY sequence_no ASC, md ASC";
$sql="SELECT * FROM OneLine.pic_Stim WHERE well_id='".$ow_well_id."' ORDER BY 'TOP INTERVAL' ASC";
$rs2=odbc_exec($con2,$sql);
if (!$rs2) {
  exit("Error in SQL");
}
if (isset($_GET["debug"])) {
echo "<table><tr>";
echo "<th>well_common_name</th>";
echo "<th>site_name</th>";
echo "<th>wellbore_id</th>";
echo "<th>zone</th>";
echo "<th>datum</th>";
echo "<th>TOP INTERVAL</th>";
echo "<th>BASE INTERVAL</th>";
echo "<th>job_date</th>";
echo "<th>Job Size</th>";
echo "<th>break_pressure</th>";
echo "<th>Frac Gradient Initial</th>";
echo "<th>Frac Gradient Final</th>";
echo "<th>initial_pressure</th>";
echo "<th>initial_shut_in_pressure</th>";
echo "<th>final_shut_in_pressure</th>";
echo "<th>final_pressure</th>";
echo "<th>average_pressure</th>";
echo "<th>Prop In Formation</th>";
echo "<th>Max Pressure</th>";
echo "<th>Main Body</th>";
}
while (odbc_fetch_row($rs2)) {
  $var1=odbc_result($rs2,"well_common_name");
  $dim_pad_id=odbc_result($rs2,"site_name");
  $business_unit_name=odbc_result($rs2,"wellbore_id");
  $zone=odbc_result($rs2,"zone");
  $well_name=odbc_result($rs2,"datum");
  $TOP_INTERVAL=odbc_result($rs2,"TOP INTERVAL");
  $BASE_INTERVAL=odbc_result($rs2,"BASE INTERVAL");
  $var8=odbc_result($rs2,"job_date");
  $bottom_lat=odbc_result($rs2,"Job Size");
  $break_pressure=odbc_result($rs2,"break_pressure");
  $frac_gradient_initial=odbc_result($rs2,"Frac Gradient Initial");
  $frac_gradient_final=odbc_result($rs2,"Frac Gradient Final");  
  $initial_pressure=odbc_result($rs2,"initial_pressure");  
  $initial_shut_in_pressure=odbc_result($rs2,"initial_shut_in_pressure");  
  $final_shut_in_pressure=odbc_result($rs2,"final_shut_in_pressure");  
  $final_pressure=odbc_result($rs2,"final_pressure");  
  $average_pressure=odbc_result($rs2,"average_pressure");  
  $Prop_In_Formation=odbc_result($rs2,"Prop In Formation");  
  $Max_Pressure=odbc_result($rs2,"Max Pressure");  
  $Main_Body=odbc_result($rs2,"Main Body");  
  
	if($frac_gradient_initial==0){
		$frac_gradient_initial=null;
	}
	if($frac_gradient_final==0){
		$frac_gradient_final=null;
	}
	 if($break_pressure==0){
		$break_pressure=null;
	}
	 if($initial_pressure==0){
		$initial_pressure=null;
	}
	 if($initial_shut_in_pressure==0){
		$initial_shut_in_pressure=null;
	}
	 if($final_shut_in_pressure==0){
		$final_shut_in_pressure=null;
	}
	 if($final_pressure==0){
		$final_pressure=null;
	}
	if($average_pressure==0){
		$average_pressure=null;
	}
	if($Max_Pressure==0){
		$final_pressure=null;
	}
	if($Main_Body==0){
		$Main_Body=null;
	}
	if($TOP_INTERVAL==0){
		$TOP_INTERVAL=null;
	}
	if($BASE_INTERVAL==0){
		$BASE_INTERVAL=null;
	}
	
	//----------------------------------------------------------------------------------------------------------------------------------
    //calcs for 1d_array and ultimately 2d_array
  $average_depth = ($TOP_INTERVAL+$BASE_INTERVAL)/2;
  //echo "<br><br>".$average_depth;
  //echo "<br>".interpolate_tvd($average_depth, $surveys_array_filtered);
  
  //preserve the raw frac gradients for the html table output 
  $frac_gradient_initial1 = $frac_gradient_initial;
  $frac_gradient_final1 = $frac_gradient_final;
  
  //calcs for turning frac gradient (psi/ft into pressure)
  $frac_gradient_initial = round($frac_gradient_initial*interpolate_tvd($average_depth, $surveys_array_filtered) , 2);
  $frac_gradient_final = round($frac_gradient_final*interpolate_tvd($average_depth, $surveys_array_filtered) , 2);
  
  //  Assuming 2%NacCl equivalent produced water or 8.45 ppg ->  (.052)(8.45) + .44 psi/ft
  $initial_shut_in_pressure = round(.433*interpolate_tvd($average_depth, $surveys_array_filtered)+$initial_shut_in_pressure,2);
  $final_shut_in_pressure =round( .433*interpolate_tvd($average_depth, $surveys_array_filtered)+$final_shut_in_pressure,2);
  if($initial_pressure != null){
  $initial_pressure = round($initial_pressure,2);
  }
  $break_pressure = round($break_pressure,2);
  if($final_pressure != null){
  $final_pressure= round($final_pressure,2);
  }
  $average_pressure= round($average_pressure,2);
  $Max_Pressure= round($Max_Pressure,2);
  
  //normal gradient
  $normal_gradient = round((.433*interpolate_tvd($average_depth, $surveys_array_filtered)),2);
  $overburden_gradient = round((1.07*interpolate_tvd($average_depth, $surveys_array_filtered)),2);
  
  //----------------------------------------------------------------------------------------------------------------------------------------
  //add data from the Geo's  Pressure Model
  
  //Calculate Pressure - see "Piceance Pressure Modeling.xlsx" - sheet "C-TVD SS vs PP"
    //Press = (2604.7 * Exp(-0.000316 * STVD)) 'Mean pressure
	//(From Volumetrics_MechPro) this is the C charp program that populates Petra
	
	  //used for Petra Model
  $inSTVD = $ow_datum_elevation - round(interpolate_tvd($average_depth, $surveys_array_filtered),2);
  
	if(strpos($field_name,'RYAN') !== false || strpos($field_name,'BARCUS') !== false  || strpos($field_name,'SULPHUR') !== false ){
		
		// RG/RGU/BCU Pressure
		$petra_pressure = .000009 * pow($inSTVD, 2) - 0.6261* $inSTVD + 2649.1;
	} else {
		// "inSTVD" decreases as we move down-hole (greater TVD)
                     if ($inSTVD > 1072){
					 
					 // Min pressure curve
                        $petra_pressure = (-0.6283 * $inSTVD) + 2545.6;	
					} else {
					// Max pressure curve (Debbie Patskowski - 2014-08-27)
                        $petra_pressure = (-1.4342 * $inSTVD) + 2757.6;
                        //////
                        ////// Max pressure curve - old method
                        //////press = (0.0001 * Math.Pow(inSTVD, 2)) - (1.0734 * inSTVD) + 2907.8;
                     }
				 }
	$petra_max_pressure = (-1.4342 * $inSTVD) + 2757.6;
	$petra_min_pressure = (-0.6283 * $inSTVD) + 2545.6;
	
	if ($normal_gradient > $petra_pressure) {
		$petra_pressure = $normal_gradient;
		}
  
  
  //--------------------------------------------------------------------------------------------------------------------------------------------
  
  //add data to 1d_array
  $d1_array[] = array('tvd' => interpolate_tvd($average_depth, $surveys_array_filtered), 'break pressure' => $break_pressure, 'Frac Gradient Initial' => $frac_gradient_initial, 'Frac Gradient Final' => $frac_gradient_final, 'zone' => $zone, 'Initial Pressure' => $initial_pressure, 'ISIP' => $initial_shut_in_pressure, 'FSIP' => $final_shut_in_pressure, 'Final Pressure' => $final_pressure, 'Avg Pressure' => $average_pressure, 'Max Pressure' => $Max_Pressure, 'Main_Body' => $Main_Body, 'Normal Gradient' => $normal_gradient, 'Overburden Gradient' => $overburden_gradient, 'Petra Pressure' => $petra_pressure, 'Petra Max Pressure' => $petra_max_pressure, 'Petra Min Pressure' => $petra_min_pressure);
  
  //create a new line for bottom and top data (this section has been moved to the bottom
  //$d1_array[] = array('tvd' => interpolate_tvd($TOP_INTERVAL, $surveys_array_filtered), 'zone' => $zone, 'top_interval' => 0);
  //$d1_array[] = array('tvd' => interpolate_tvd($TOP_INTERVAL, $surveys_array_filtered), 'zone' => $zone, 'top_interval' => 6000);
  //$d1_array[] = array('tvd' => interpolate_tvd($BASE_INTERVAL, $surveys_array_filtered), 'zone' => $zone, 'bottom_interval' => 0);
  //$d1_array[] = array('tvd' => interpolate_tvd($BASE_INTERVAL, $surveys_array_filtered), 'zone' => $zone, 'bottom_interval' => 6000);
if (isset($_GET["debug"])) {
  echo "<tr><td>$var1</td>";
  echo "<td>$dim_pad_id</td>";
  echo "<td>$business_unit_name</td>";
  echo "<td>$zone</td>";
  echo "<td>$well_name</td>";
  echo "<td>$TOP_INTERVAL</td>";
  echo "<td>$BASE_INTERVAL</td>";
  echo "<td>$var8</td>";
  echo "<td>$bottom_lat</td>";
  echo "<td>$break_pressure</td>";
  echo "<td>$frac_gradient_initial1</td>";
  echo "<td>$frac_gradient_final1</td>";
  echo "<td>$initial_pressure</td>";
  echo "<td>$initial_shut_in_pressure</td>";
  echo "<td>$final_shut_in_pressure</td>";
  echo "<td>$final_pressure</td>";
  echo "<td>$average_pressure</td>";
  echo "<td>$Max_Pressure</td>";
  echo "<td>$petra_pressure</td>";
  echo "<td>$petra_min_pressure</td>";
  echo "<td>$petra_max_pressure</td>";
  echo "<td>$Main_Body</td></tr>";
  }
}
if (isset($_GET["debug"])) {
echo "</table>";
}

//REPEAT TO HAVE FRAC INTERVALS NEXT TO EACH OTHER IN CHART
//$sql="SELECT * FROM EDMDB_OW_PIC_P.dbo.CD_SURVEY_STATION_T WHERE well_id='".$ow_well_id."' ORDER BY sequence_no ASC, md ASC";
$sql="SELECT * FROM OneLine.pic_Stim WHERE well_id='".$ow_well_id."' ORDER BY 'TOP INTERVAL' ASC";
$rs20=odbc_exec($con2,$sql);
if (!$rs20) {
  exit("Error in SQL");
}
while (odbc_fetch_row($rs20)) {
  $var1=odbc_result($rs20,"well_common_name");
  $dim_pad_id=odbc_result($rs20,"site_name");
  $business_unit_name=odbc_result($rs20,"wellbore_id");
  $zone=odbc_result($rs20,"zone");
  $well_name=odbc_result($rs20,"datum");
  $TOP_INTERVAL=odbc_result($rs20,"TOP INTERVAL");
  $BASE_INTERVAL=odbc_result($rs20,"BASE INTERVAL");
  $var8=odbc_result($rs20,"job_date");
  $bottom_lat=odbc_result($rs20,"Job Size");
  $break_pressure=odbc_result($rs20,"break_pressure");
  $frac_gradient_initial=odbc_result($rs20,"Frac Gradient Initial");
  $frac_gradient_final=odbc_result($rs20,"Frac Gradient Final");  
  $initial_pressure=odbc_result($rs20,"initial_pressure");  
  $initial_shut_in_pressure=odbc_result($rs20,"initial_shut_in_pressure");  
  $final_shut_in_pressure=odbc_result($rs20,"final_shut_in_pressure");  
  $final_pressure=odbc_result($rs20,"final_pressure");  
  $average_pressure=odbc_result($rs20,"average_pressure");  
  $Prop_In_Formation=odbc_result($rs20,"Prop In Formation");  
  $Max_Pressure=odbc_result($rs20,"Max Pressure");  
  $Main_Body=odbc_result($rs20,"Main Body");  
  
	if($TOP_INTERVAL==0){
		$TOP_INTERVAL=null;
	}
	if($BASE_INTERVAL==0){
		$BASE_INTERVAL=null;
	}
  
  //calcs for 1d_array and ultimately 2d_array
  $average_depth = ($TOP_INTERVAL+$BASE_INTERVAL)/2;
  
  //add data to 1d_array
  //$d1_array[] = array('tvd' => interpolate_tvd($average_depth, $surveys_array_filtered), 'break pressure' => $break_pressure, 'Frac Gradient Initial' => $frac_gradient_initial, 'Frac Gradient Final' => $frac_gradient_final, 'zone' => $zone, 'Initial Pressure' => $initial_pressure, 'ISIP' => $initial_shut_in_pressure, 'FSIP' => $final_shut_in_pressure, 'Final Pressure' => $final_pressure, 'Avg Pressure' => $average_pressure, 'Max Pressure' => $Max_Pressure, 'Main_Body' => $Main_Body);
  //create a new line for bottom and top data 
  $d1_array[] = array('tvd' => interpolate_tvd($TOP_INTERVAL, $surveys_array_filtered), 'zone' => $zone, 'top_interval' => 0);
  $d1_array[] = array('tvd' => interpolate_tvd($TOP_INTERVAL, $surveys_array_filtered), 'zone' => $zone, 'top_interval' => 6000);
  $d1_array[] = array('tvd' => interpolate_tvd($BASE_INTERVAL, $surveys_array_filtered), 'zone' => $zone, 'bottom_interval' => 0);
  $d1_array[] = array('tvd' => interpolate_tvd($BASE_INTERVAL, $surveys_array_filtered), 'zone' => $zone, 'bottom_interval' => 6000);
 }

odbc_close($con2);
?>

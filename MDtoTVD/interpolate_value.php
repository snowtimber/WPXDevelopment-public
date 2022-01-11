<?php
//Code to interpolate a given value
//___________________________________________________________________________________________________________
//sample interpolated value: 2555' MD and we want TVD
$interpolate_value = $_GET["interpolate_value"];

// If an interpolate_value has been entered by the user do the following
if (isset($interpolate_value)) {
	
	//declare initial values
	$last_md = 0;
	$last_tvd = 0;

	//iterate through rows until the survey md value is greater than entered interpolate_value
	for ($row = 0; $row < $surveys_array_filtered_length; $row++) {
	  
	  //current values during for loop processing
	  $sequence_no_now = $surveys_array[$row][0];
	  $md_now = $surveys_array_filtered[$row][1];
	  $tvd_now = $surveys_array_filtered[$row][2];
	  
	  if ($md_now == $interpolate_value) {
		$interpolate_result_tvd = $surveys_array_filtered[$row][2];
		}
	  
	  
	  if ($interpolate_value < $surveys_array_filtered[$row][1] and $interpolate_value > $last_md) {
		$interpolate_result_tvd = (($interpolate_value-$last_md)*($tvd_now-$last_tvd)/($md_now-$last_md))+$last_tvd;
		echo "<br><br>Interpolated TVD is: ".$interpolate_result_tvd;
	  
		break 1;
	  }
	  
	  //set previous numbers for next loop
	  $last_md = $surveys_array_filtered[$row][1];
	  $last_tvd =$surveys_array_filtered[$row][2];
	  
	}
	if (isset($interpolate_result_tvd)) {	
	} else {
	echo "<br><br>MD is not within survey range";
	}
	

}  else {
	echo "never performed interpolation";
}

?>

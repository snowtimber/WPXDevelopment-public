<?php

function interpolate_tvd($md_interpolate, $surveys_array_filtered) {

	//declare initial values
	$last_md = 0;
	$last_tvd = 0;
	
	//Length of the array
	$surveys_array_filtered_length = count($surveys_array_filtered);
	
	/*  Print out filtered array
	for ($row = 0; $row < count($surveys_array_filtered); $row++) {
	  echo "<p><b>Row number $row</b></p>";
	  echo "<ul>";
	  for ($col = 0; $col < 4; $col++) {
		echo "<li>".$surveys_array_filtered[$row][$col]."</li>";
	  }
	  echo "</ul>";
	}
	*/
	
	//iterate through rows until the survey md value is greater than entered md_interpolate
	for ($row = 0; $row < count($surveys_array_filtered); $row++) {
	  
	  //current values during for loop processing
	  $sequence_no_now = $surveys_array_filtered[$row][0];
	  $md_now = $surveys_array_filtered[$row][1];
	  $tvd_now = $surveys_array_filtered[$row][2];
	  
	  if ($row > 0) {
	  $last_md = $surveys_array_filtered[$row-1][1];
	  $last_tvd = $surveys_array_filtered[$row-1][2];
	  } else {
	  $last_md = 0;
	  $last_tvd = 0;
	  }
	  /* debug with this
	  echo "<br><br>row no:  ".$surveys_array_filtered[$row][0];
	  echo "<br>md_now:  ".$md_now;
	  echo "<br>tvd_now:  ".$tvd_now;
	  echo "<br>last_md:  ".$last_md;
	  echo "<br>last_tvd:  ".$last_tvd;
	  echo "<br>md_interpolate:  ".$md_interpolate;
	  echo "<br>count:  ".count($surveys_array_filtered);
	  */
	  if ($md_now == $md_interpolate) {
		$interpolate_result_tvd = $surveys_array_filtered[$row][2];
		
		return $interpolate_result_tvd;
	  
		break 2;
	  }
	  
	  
	  if ($md_interpolate < $surveys_array_filtered[$row][1] and $md_interpolate > $surveys_array_filtered[$row-1][1]) {
		//$interpolate_result_tvd = round((($md_interpolate-$last_md)*($tvd_now-$last_tvd)/($md_now-$last_md))+$last_tvd,2);
		$interpolate_result_tvd = round((($md_interpolate-$last_md)/($md_now-$last_md)*($tvd_now-$last_tvd))+$last_tvd,2);
		//echo "<br>criteria has been reached.  TVD:  ".$interpolate_result_tvd;
	    return $interpolate_result_tvd;
		
		break 2;
	  }
	  
	  //set previous numbers for next loop
	  //$last_md = $md_now;
	  //$last_tvd =$tvd_now;
	  
	}
}
?>

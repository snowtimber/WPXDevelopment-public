<html>
<body>

<?php

	//which file to grab?
	$filename = 'DrillScheduleNextYearHtml.txt';

	//open the previously locally saved txt file containing the html (The HTML is to be requested hourly unless UPDATE button is pressed by user)
	$file = file_get_contents('./'.$filename, FILE_USE_INCLUDE_PATH);
	
	//Count how many erroneous entries (sql results of zero) occured
	$Error_entries = 0;

	//get the Title
	$BegTitle = strstr($file, 'Rig Line Planning');
	$Title = strstr($BegTitle, '</title>', true);
	//put title in page
	echo $Title;
	
	
	// Get the rig on and rig off date from the title
	$from_position = strpos($Title, "from ");
	$to_position = strpos($Title, "to ");
	$for_position = strpos($Title, "for");
	$Date_Begin = substr($Title, $from_position+5, $to_position-$from_position-6);
	$Date_End = substr($Title, $to_position+3, $for_position-$to_position-4);
	$length = $for_position-$to_position;

	$year_beg = substr($Date_Begin, strlen($Date_Begin)-4,4);
	$year_beg_rest = substr($Date_Begin, 0, strlen($Date_Begin)-5);
	$begin_date_revised = str_replace("/","-", $year_beg."-".$year_beg_rest);
	
	$year_end = substr($Date_End, strlen($Date_End)-4,4);
	$year_end_rest = substr($Date_End, 0, strlen($Date_End)-5);
	$end_date_revised = str_replace("/","-", $year_end."-".$year_end_rest);
	
	if (isset($_GET["debug"])) {
	echo  "<br>Date_Begin:".$Date_Begin."<br> Date_End:".$Date_End;
	echo  "<br>begin_date_revised:".$begin_date_revised;
	echo  "<br>to position:".$to_position;
	echo  "<br>for position:".$for_position;
	echo  "<br>length:".$length;
	echo  "<br>year_end:".$year_end;
	echo  "<br>year_end_rest:".$year_end_rest;
	echo  "<br>end_date_revised:".$end_date_revised;
	}
	
	
	//get the style of the html
	$BegStyle = strstr($file, '<style xmlns="" type="text/css">table.data');
	$Style = strstr($BegStyle, '<script type="text/javascript">var WEBCORE_BASE', true);
	//put style in page
	echo $Style;

	//get the html table
	$BegTable = strstr($file, '<table');
	$FinalTable = strstr($BegTable, '<div xmlns="" id="excel-version">', true);
	//echo $FinalTable;
	
	//Include HTML DOM so that we can go thorugh and parse
	//cycle through  individual Pads to get more information
	include('./simple_html_dom.php');
	
	// Create DOM from string
	$html = str_get_html($FinalTable); // Parse the HTML, stored as a string in $string
	
	
	//setting width on the column header "th" had no result
	//Set a defined width for the column header by looping through th elements that contain colspan="6"

	foreach($html->find('th[colspan="6"]') as $column_header) {
	//set the width to 150 which corresponds to 150 px per month
	$column_header->width = 150;
	}

	foreach($html->find('td[class="padName"]') as $td_element) {
	//set the width to 150 which corresponds to 150 px per month
	//$td_element->width = 25;
	$td_element->style = "background-color: #00FFFF";
	}
	
	foreach($html->find('td[class="blankCell"]') as $td_element) {
	//set the width to 150 which corresponds to 150 px per month
	$td_element->width = 25;
	$td_element->style = "background-color: #969696";
	}
	
	
	
	// Find all columns with class=padName in the table class=data
	$wellpads = $html->find('table.data td.padName'); // Find all columns with class=padName in the table class=data
	
	//OPEN ODBC CONNECTION TO DATAVISION
	$conn=odbc_connect('DataVision','datavision_query','pw');
	if (!$conn) {
	  exit("Connection Failed: " . $conn);
	}
	
	$x = 0;
	
	//loop through pads
	foreach ($wellpads as $e) {
	
	if (isset($_GET["debug"])) {
	echo "<br>--------------------------------------------------------New Pad -------------------------------------------------------------";
	}
	//get parent which contains the rig name
	$tr_outertext = trim($e->parent () ->innertext, " ");  //this contains the rig name
	//$tr_outertext = str_replace(" ","", $tr_outertext);
	//echo "<br>parent outertext:".$tr_outertext;
	$first_arrow = strpos($tr_outertext, ">");
	$first_blank = strpos($tr_outertext, " ",$first_arrow);
	//echo "<br>first_blank:".$first_blank;
	$second_blank = strpos($tr_outertext, " ",$first_blank+2);
	//echo "<br>second_blank:".$second_blank;
	$Rig_contractor = substr($tr_outertext, $first_blank+1, $second_blank-$first_blank-1);
	$Rig_contractor = trim($Rig_contractor);
	if (strpos($Rig_contractor, '&') !== false) {
	//if (strpos($Rig_contractor,'"H"') !== false and strpos($Rig_contractor,'"P"') !== false ) {
	$Rig_contractor = "H & P";
	}
	if (isset($_GET["debug"])) {
	echo "<br>rig contractor:".$Rig_contractor;
	}
	
	
	//  Pad information has the format:  &nbsp;(19)&nbsp; Extra well info goes here &nbsp;                                     
	$last_nbsp_position = strripos($e, '&nbsp;');
	
	//a_iteration to zero
	$a_iteration = 0;
	
		foreach ($e->children() as $a) {
			
			//increment pad iteration
			//$a_iteration= $a_iteration +1;
			
			//view the iteration
			//echo "<br><br>a_iteration = " . $a_iteration;
			
			
			$etext = $e->innertext;
			$left_parentheses_pos = strpos($etext, "(");
			$right_parentheses_pos = strpos($etext, ")");
			$no_wells_on_pad = substr($etext, $left_parentheses_pos+1, $right_parentheses_pos-$left_parentheses_pos-1);
			//$no_wells_on_pad = substr($etext, $left_parentheses_pos+1, 2);
			if (isset($_GET["debug"])) {
			echo "<br>etext:".$etext;
			echo "<br>wells on pad:" . $no_wells_on_pad;
			}
			
			$PAD_NAME = $a->innertext;
			$PAD_NAME_outertext = $a->outertext;
			if (isset($_GET["debug"])) {
			echo '<br>PAD_NAME INNERTEXT: ' . $PAD_NAME;
			echo '<br>PAD_NAME OUTERTEXT: ' . $PAD_NAME_outertext;
			}
			
			//get information from datavision and store to variables based off of Pad_name
			//$sql="SELECT * FROM OneLine.pic_Pad_Visit_Summary WHERE PAD_NAME LIKE '%" . $PAD_NAME . "%' AND RIG_ON <= '".$end_date_revised."' AND RIG_OFF >='" . $begin_date_revised . "' ";
			$sql="SELECT * FROM OneLine.pic_Pad_Visit_Summary WHERE PAD_NAME LIKE '%" . $PAD_NAME . "%' AND RIG_ON <= '".$end_date_revised."' AND RIG_OFF >='" . $begin_date_revised . "' AND RIG LIKE '%" . $Rig_contractor . "%' AND WELLS_PER_VISIT = " . $no_wells_on_pad . " ";
			$rs=odbc_exec($conn,$sql);
			if (!$rs) {
			  exit("Error in SQL");
			}
			
			if (isset($_GET["debug"])) {
			echo "<table><tr>";
			echo "<th>Pad Name</th>";
			echo "<th>FirstOfWellname</th>";
			echo "<th>RIG</th>";
			echo "<th>WELLS_PER_VISIT</th>";
			echo "<th>RIG_ON</th>";
			echo "<th>RIG_OFF</th>";
			echo "<th>APD_MIN_STATUS</th>";
			echo "<th>APD_MAX_STATUS</th>";
			echo "<th>FED_MIN_STATUS</th>";
			echo "<th>FED_MAX_STATUS</th>";
			}
			$rows = 0;
			
			while (odbc_fetch_row($rs)) {
			$PAD_NAME=odbc_result($rs,"PAD_NAME");
			$FirstOfWellname=odbc_result($rs,"FirstOfWellname");
			$RIG=odbc_result($rs,"RIG");
			$RIG_ON=odbc_result($rs,"RIG_ON");
			$RIG_OFF=odbc_result($rs,"RIG_OFF");
			$WELLS_PER_VISIT=odbc_result($rs,"WELLS_PER_VISIT");
			$APD_MIN_STATUS=odbc_result($rs,"APD_MIN_STATUS");
			$APD_MAX_STATUS=odbc_result($rs,"APD_MAX_STATUS");
			$STATE_MIN_EXP=substr(odbc_result($rs,"STATE_MIN_EXP"), 0 , 10);
			$FED_MIN_STATUS=odbc_result($rs,"FED_MIN_STATUS");
			$FED_MAX_STATUS=odbc_result($rs,"FED_MAX_STATUS");
			$FED_MIN_EXP=substr(odbc_result($rs,"FED_MIN_EXP"), 0 , 10);
			$LAND=odbc_result($rs,"LAND");  
			$PAD_FIELD_NAME=odbc_result($rs,"PAD_FIELD_NAME");  
			$rows = $rows +1;
			
			if (isset($_GET["debug"])) {
			  echo "<tr><td>$PAD_NAME</td>";
			  echo "<td>$FirstOfWellname</td>";
			  echo "<td>$RIG</td>";
			  echo "<td>$WELLS_PER_VISIT</td>";
			  echo "<td>$RIG_ON</td>";
			  echo "<td>$RIG_OFF</td>";
			  echo "<td>$APD_MIN_STATUS</td>";
			  echo "<td>$APD_MAX_STATUS</td>";
			  echo "<td>$FED_MIN_STATUS</td>";
			  echo "<td>$FED_MAX_STATUS</td></tr>";
			  }
			}
			
			if (isset($_GET["debug"])) {
				echo "</table>";
			}
			
			//If no pads were found that matched dates and rig, search again without rig match but with RIG match
			if ($rows == 0){
				if (isset($_GET["debug"])) {
				echo "---------> reran sql query without wellcount match";
				}
				//get information from datavision and store to variables based off of Pad_name
				$sql="SELECT * FROM OneLine.pic_Pad_Visit_Summary WHERE PAD_NAME LIKE '%" . $PAD_NAME . "%' AND RIG_ON <= '".$end_date_revised."' AND RIG_OFF >='" . $begin_date_revised . "' AND RIG LIKE '%" . $Rig_contractor . "%' ";
				$rs=odbc_exec($conn,$sql);
				if (!$rs) {
				  exit("Error in SQL");
				}
				
				if (isset($_GET["debug"])) {
				echo "<table><tr>";
				echo "<th>Pad Name</th>";
				echo "<th>FirstOfWellname</th>";
				echo "<th>RIG</th>";
				echo "<th>WELLS_PER_VISIT</th>";
				echo "<th>RIG_ON</th>";
				echo "<th>RIG_OFF</th>";
				echo "<th>APD_MIN_STATUS</th>";
				echo "<th>APD_MAX_STATUS</th>";
				echo "<th>FED_MIN_STATUS</th>";
				echo "<th>FED_MAX_STATUS</th>";
				}
				$rows = 0;
				
				while (odbc_fetch_row($rs)) {
				$PAD_NAME=odbc_result($rs,"PAD_NAME");
				$FirstOfWellname=odbc_result($rs,"FirstOfWellname");
				$RIG=odbc_result($rs,"RIG");
				$RIG_ON=odbc_result($rs,"RIG_ON");
				$RIG_OFF=odbc_result($rs,"RIG_OFF");
				$WELLS_PER_VISIT=odbc_result($rs,"WELLS_PER_VISIT");
				$APD_MIN_STATUS=odbc_result($rs,"APD_MIN_STATUS");
				$APD_MAX_STATUS=odbc_result($rs,"APD_MAX_STATUS");
				$STATE_MIN_EXP=substr(odbc_result($rs,"STATE_MIN_EXP"), 0 , 10);
				$FED_MIN_STATUS=odbc_result($rs,"FED_MIN_STATUS");
				$FED_MAX_STATUS=odbc_result($rs,"FED_MAX_STATUS");
				$FED_MIN_EXP=substr(odbc_result($rs,"FED_MIN_EXP"), 0 , 10);
				$LAND=odbc_result($rs,"LAND");  
				$PAD_FIELD_NAME=odbc_result($rs,"PAD_FIELD_NAME");  
				$rows = $rows +1;
				
				if (isset($_GET["debug"])) {
				  echo "<tr><td>--->$PAD_NAME</td>";
				  echo "<td>$FirstOfWellname</td>";
				  echo "<td>$RIG</td>";
				  echo "<td>$WELLS_PER_VISIT</td>";
				  echo "<td>$RIG_ON</td>";
				  echo "<td>$RIG_OFF</td>";
				  echo "<td>$APD_MIN_STATUS</td>";
				  echo "<td>$APD_MAX_STATUS</td>";
				  echo "<td>$FED_MIN_STATUS</td>";
				  echo "<td>$FED_MAX_STATUS</td></tr>";
				  }
				}
				
				if (isset($_GET["debug"])) {
					echo "</table>";
				}
				
				//If no pads were found, search again with only rig_on later than 2014 and wellcount match
				if ($rows == 0){
					if (isset($_GET["debug"])) {
					echo "---------> reran again, the sql query for rig_on date later then Jan 1st 2014 and wellcount match";
					}
					//get information from datavision and store to variables based off of Pad_name
					$sql="SELECT * FROM OneLine.pic_Pad_Visit_Summary WHERE PAD_NAME LIKE '%" . $PAD_NAME . "%' AND RIG_ON > 2014-01-01 AND WELLS_PER_VISIT = " . $no_wells_on_pad . " ";
					$rs=odbc_exec($conn,$sql);
					if (!$rs) {
					  exit("Error in SQL");
					}
					
					if (isset($_GET["debug"])) {
					echo "<table><tr>";
					echo "<th>Pad Name</th>";
					echo "<th>FirstOfWellname</th>";
					echo "<th>RIG</th>";
					echo "<th>WELLS_PER_VISIT</th>";
					echo "<th>RIG_ON</th>";
					echo "<th>RIG_OFF</th>";
					echo "<th>APD_MIN_STATUS</th>";
					echo "<th>APD_MAX_STATUS</th>";
					echo "<th>FED_MIN_STATUS</th>";
					echo "<th>FED_MAX_STATUS</th>";
					}
					$rows = 0;
					
					while (odbc_fetch_row($rs)) {
					$PAD_NAME=odbc_result($rs,"PAD_NAME");
					$FirstOfWellname=odbc_result($rs,"FirstOfWellname");
					$RIG=odbc_result($rs,"RIG");
					$RIG_ON=odbc_result($rs,"RIG_ON");
					$RIG_OFF=odbc_result($rs,"RIG_OFF");
					$WELLS_PER_VISIT=odbc_result($rs,"WELLS_PER_VISIT");
					$APD_MIN_STATUS=odbc_result($rs,"APD_MIN_STATUS");
					$APD_MAX_STATUS=odbc_result($rs,"APD_MAX_STATUS");
					$STATE_MIN_EXP=substr(odbc_result($rs,"STATE_MIN_EXP"), 0 , 10);
					$FED_MIN_STATUS=odbc_result($rs,"FED_MIN_STATUS");
					$FED_MAX_STATUS=odbc_result($rs,"FED_MAX_STATUS");
					$FED_MIN_EXP=substr(odbc_result($rs,"FED_MIN_EXP"), 0 , 10);
					$LAND=odbc_result($rs,"LAND");  
					$PAD_FIELD_NAME=odbc_result($rs,"PAD_FIELD_NAME");  
					$rows = $rows +1;
					if($rows == 0) {
					echo "No Matches found for 3 queries";
					}
					
					if (isset($_GET["debug"])) {
					  echo "<tr><td>--->$PAD_NAME</td>";
					  echo "<td>$FirstOfWellname</td>";
					  echo "<td>$RIG</td>";
					  echo "<td>$WELLS_PER_VISIT</td>";
					  echo "<td>$RIG_ON</td>";
					  echo "<td>$RIG_OFF</td>";
					  echo "<td>$APD_MIN_STATUS</td>";
					  echo "<td>$APD_MAX_STATUS</td>";
					  echo "<td>$FED_MIN_STATUS</td>";
					  echo "<td>$FED_MAX_STATUS</td></tr>";
					  }
					}
					
					if (isset($_GET["debug"])) {
						echo "</table>";
					}
					
					//If no pads were found, search again with only rig_on later than 2014 
					if ($rows == 0){
						if (isset($_GET["debug"])) {
						echo "---------> reran again again, the sql query for rig_on date later then Jan 1st 2014";
						}
						//get information from datavision and store to variables based off of Pad_name
						$sql="SELECT * FROM OneLine.pic_Pad_Visit_Summary WHERE PAD_NAME LIKE '%" . $PAD_NAME . "%' AND RIG_ON > 2014-01-01";
						$rs=odbc_exec($conn,$sql);
						if (!$rs) {
						  exit("Error in SQL");
						}
						
						if (isset($_GET["debug"])) {
						echo "<table><tr>";
						echo "<th>Pad Name</th>";
						echo "<th>FirstOfWellname</th>";
						echo "<th>RIG</th>";
						echo "<th>WELLS_PER_VISIT</th>";
						echo "<th>RIG_ON</th>";
						echo "<th>RIG_OFF</th>";
						echo "<th>APD_MIN_STATUS</th>";
						echo "<th>APD_MAX_STATUS</th>";
						echo "<th>FED_MIN_STATUS</th>";
						echo "<th>FED_MAX_STATUS</th>";
						}
						$rows = 0;
						
						while (odbc_fetch_row($rs)) {
						$PAD_NAME=odbc_result($rs,"PAD_NAME");
						$FirstOfWellname=odbc_result($rs,"FirstOfWellname");
						$RIG=odbc_result($rs,"RIG");
						$RIG_ON=odbc_result($rs,"RIG_ON");
						$RIG_OFF=odbc_result($rs,"RIG_OFF");
						$WELLS_PER_VISIT=odbc_result($rs,"WELLS_PER_VISIT");
						$APD_MIN_STATUS=odbc_result($rs,"APD_MIN_STATUS");
						$APD_MAX_STATUS=odbc_result($rs,"APD_MAX_STATUS");
						$STATE_MIN_EXP=substr(odbc_result($rs,"STATE_MIN_EXP"), 0 , 10);
						$FED_MIN_STATUS=odbc_result($rs,"FED_MIN_STATUS");
						$FED_MAX_STATUS=odbc_result($rs,"FED_MAX_STATUS");
						$FED_MIN_EXP=substr(odbc_result($rs,"FED_MIN_EXP"), 0 , 10);
						$LAND=odbc_result($rs,"LAND");  
						$PAD_FIELD_NAME=odbc_result($rs,"PAD_FIELD_NAME");  
						$rows = $rows +1;
						if($rows == 0) {
						echo "No Matches found for 3 queries";
						}
						
						if (isset($_GET["debug"])) {
						  echo "<tr><td>--->$PAD_NAME</td>";
						  echo "<td>$FirstOfWellname</td>";
						  echo "<td>$RIG</td>";
						  echo "<td>$WELLS_PER_VISIT</td>";
						  echo "<td>$RIG_ON</td>";
						  echo "<td>$RIG_OFF</td>";
						  echo "<td>$APD_MIN_STATUS</td>";
						  echo "<td>$APD_MAX_STATUS</td>";
						  echo "<td>$FED_MIN_STATUS</td>";
						  echo "<td>$FED_MAX_STATUS</td></tr>";
						  }
						}
						
						if (isset($_GET["debug"])) {
							echo "</table>";
						}
						
						
					}
					
				}
			}
			
			//change out emptys for "none"
			/////////////////////////////////////////////////////////////////////////////////
			IF ($APD_MAX_STATUS == "") {
			$APD_MAX_STATUS = "none";
			} 
			IF ($FED_MAX_STATUS == "") {
			$FED_MAX_STATUS = "none";
			}
			
			IF ($STATE_MIN_EXP == "") {
			$STATE_MIN_EXP = "none";
			}
			
			IF ($FED_MIN_EXP == "") {
			$FED_MIN_EXP = "none";
			}
			
			IF ($LAND == "") {
			$LAND = "none";
			}
			
			//Format text color based on land type
			////////////////////////////////////////////////////////////////////////////////////
			IF ($LAND == "FEE" or $LAND == "") {
			$color = "color=\"gray\"";
			}
			
			IF ($LAND == "SPLIT ESTATE") {
			$color = "color=\"red\"";
			}
			
			IF ($LAND == "FED") {
			$color = "color=\"green\"";
			}
			
			//only do sql query to match pad on first innertext child element
			break;
		}	
		
	$multiple_solution_pads = 0;
	
	//<font face="verdana" color="green">This is some text!</font>
	if ($rows == 0) {
	$str_to_insert = 'NO RESULTS FOUND for this Pad Name around these Rig Dates with this Rig in Datavision';
	$Error_entries++;
	} elseif ($rows >= 2){
	$multiple_solution_pads++;
	$str_to_insert = 'Multiple Solutions Found';
	} else {
	$str_to_insert = '<font '. $color . '> | ' . $PAD_FIELD_NAME .  '-' . $LAND . ' | ST-' . $APD_MAX_STATUS. ' FED-' . $FED_MAX_STATUS. ' (St exp ' . $STATE_MIN_EXP . ')' . '(FED exp ' . $FED_MIN_EXP . ')</font>';  //define string here
	}
	$newstr = substr_replace($e, $str_to_insert, $last_nbsp_position, 0);  //add text after the pad information
	
	//save the edit to the DOM
	$e->outertext = $newstr;
	
	/*
	//Change background formatting if Land type is FED or Split Estate based on varying conditions
	if ($LAND == "FEE" or $LAND == "SPLIT ESTATE") {
	
		if (($APD_MAX_STATUS == "APP" AND $FED_MAX_STATUS == "none") or ($APD_MAX_STATUS == "none" AND $FED_MAX_STATUS == "APP")) {
		$addCLASS = ; //define class here
		$e->class = "padName " . $addCLASS; //whatever we want here
		}
	}
	*/
	/*
	echo '<br><br>';
	echo $e->tag; // Returns: " div"
	echo '<br><br>';
	echo $e->outertext; // Returns: " <div>foo <b>bar</b></div>"
	echo '<br><br>';
	echo $e->innertext; // Returns: " foo <b>bar</b>"
	echo '<br><br>';
	echo $e->plaintext; // Returns: " foo bar"
	echo '<br><br>';
	*/
	}
	
	//CLOSE ODBC CONNECTION
	odbc_close($conn);
	
	//echo the original html table from wellcore
	if (isset($_GET["debug"])) {
	echo $FinalTable;
	}
	
	//echo the revised table that includes Datavision data
	$str = $html;
	echo $str;
	
	//Set a debug checkbox
	?>
	<form action="ParseTo_HTML_Table.php" method="get">
		<?php
		if (isset($_GET["debug"])) {
		echo "<br><br><input type='checkbox' name='debug' value='true' checked>debug mode, ";
		} else {
		echo  "<br><br><input type='checkbox' name='debug' value='true'>debug mode, ";
		}?>
		<br><input type="submit">
		
	</form>
	
	<?php
	//Error reporting if debug is set to true
	if (isset($_GET["debug"])) {
	echo "<br>Failed Pad Name Matches:".$Error_entries;
	echo "<br>multiple solution pads:".$multiple_solution_pads;
	}
?>

</body>
</html>

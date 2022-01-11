<html>
<body>

<?php

	//which file to grab?
	$filename = 'DrillScheduleCustom.txt';

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
	
	//Include HTML DOM so that we can go through and parse html table
	//cycle through  individual Pads to get more information
	include('./simple_html_dom.php');
	
	// Create DOM from string
	$html = str_get_html($FinalTable); // Parse the HTML, stored as a string in $string

	foreach($html->find('th[colspan="6"]') as $column_header) {
	//set the width to 150 which corresponds to 150 px per month
	$column_header->width = 150;
	}

	//set borders around pad names
	foreach($html->find('td[class="padName"]') as $td_element) {
	//$td_element->style = "background-color: #00FFFF; border-left: 1pt solid black; border-right: 1pt solid black; border-top: 1pt solid black; border-bottom: 1pt solid black;";
	//remove any current style attributes
	$td_element->style = null;
	$td_element->style = "background-color: #ffffff; border-left: .5pt solid black; border-right: .5pt solid black; border-top: .5pt solid black; border-bottom: .5pt solid black; text-align: center; font-size: 21; font-family: 'arial'; font-weight: bold; vertical-align:middle";
	}
	
	//set top bottom borders on rows
	foreach($html->find('tr') as $tr_element) {
	$tr_element->style = "border-top: .5pt solid black; border-bottom: .5pt solid black;";
	}
	
	//set backgound on blank cells to gray
	foreach($html->find('td[class="blankCell"]') as $td_element) {
	//set the width to 150 which corresponds to 150 px per month
	$td_element->width = 25;
	$td_element->style = "background-color: #969696";
	}
	
	//set the style in html directly for rig names so that it shows up when imported into excel
	foreach($html->find('th') as $th_element) {
		if (isset($th_element->colspan)) {
		continue;
		}
		
		$class = $th_element->class;
		$class_position = strpos($Style, $class);
		$background_color_position = strpos($Style, "background-color : ",$class_position+1);
		$th_color= substr($Style, $background_color_position+19, 7);
		$th_element->style = "background-color: ".$th_color."; border-left: .5pt solid black; border-right: .5pt solid black; font-size: 18; ";
	}
	
	//reformat relative hyperlinks that work in wellcore to absolute hyperlinks
	$root = "http://wmstutwlcp1.wpx.wpxenergy.com:8080";
	foreach($html->find('a') as $e) {
	$value = $e->href;
	$e->href = $root.$value;
	}
	
	// Find all columns with class=padName in the table class=data
	$wellpads = $html->find('table.data td.padName'); // Find all columns with class=padName in the table class=data
	
	//OPEN ODBC CONNECTION TO DATAVISION
	$conn=odbc_connect('DataVision','datavision_query','pw');
	if (!$conn) {
	  exit("Connection Failed: " . $conn);
	}
	
	$x = 0;
	
	//-----------------------------------------------------------------------------------------------------------------
	//loop through pads
	//----------------------------------------------------------------------------------------------------------
	foreach ($wellpads as $e) {
	
	//if debug is on separate the pad queries
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
	$last_nbsp_position = strripos($e, '&nbsp;'); // I don't think this is being used anymore
	
	//a_iteration to zero
	$a_iteration = 0;
	
		//the first child of each pad contains the information we are interested in
		foreach ($e->children() as $a) {
			
			//retrieve number of wells on pad
			$etext = $e->innertext;
			$left_parentheses_pos = strpos($etext, "(");
			$right_parentheses_pos = strpos($etext, ")");
			$no_wells_on_pad = substr($etext, $left_parentheses_pos+1, $right_parentheses_pos-$left_parentheses_pos-1);
			//$no_wells_on_pad = substr($etext, $left_parentheses_pos+1, 2);
			if (isset($_GET["debug"])) {
			echo "<br>etext:".$etext;
			echo "<br>wells on pad:" . $no_wells_on_pad;
			}
			
			//retrieve Pad name
			$PAD_NAME = $a->innertext;
			$PAD_NAME_outertext = $a->outertext;
			if (isset($_GET["debug"])) {
			echo '<br>PAD_NAME INNERTEXT: ' . $PAD_NAME;
			echo '<br>PAD_NAME OUTERTEXT: ' . $PAD_NAME_outertext;
			}
			//----------------------------------------------------------------------------------------------------
			//run ODBC SQL queries on Datavision to find correct Pad
			//----------------------------------------------------------------------------------------------------
			
			//get information from datavision and store to variables based off of Pad_name
			//$sql="SELECT * FROM OneLine.pic_Pad_Visit_Summary WHERE PAD_NAME LIKE '%" . $PAD_NAME . "%' AND RIG_ON <= '".$end_date_revised."' AND RIG_OFF >='" . $begin_date_revised . "' ";
			$sql="SELECT * FROM OneLine.pic_Pad_Visit_Summary WHERE PAD_NAME LIKE '%" . $PAD_NAME . "%' AND RIG_ON <= '".$end_date_revised."' AND RIG_OFF >='" . $begin_date_revised . "' AND RIG LIKE '%" . $Rig_contractor . "%' AND WELLS_PER_VISIT = " . $no_wells_on_pad . " ";
			//echo "<br>".$sql;
			$rs=odbc_exec($conn,$sql);
			if (!$rs) {
			  exit("Error in SQL");
			}
			//display ouput table of odbc query if debug is true
			require('display_table.php');
			
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
				//display ouput table of odbc query if debug is true
				require('display_table.php');
				
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
					//display ouput table of odbc query if debug is true
					require('display_table.php');
					
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
						//display ouput table of odbc query if debug is true
						require('display_table.php');
					}
					
				}
			}
			//----------------------------------------------------------------------------------------------------
			//change out emptys for "none"
			//----------------------------------------------------------------------------------------------------
			IF ($APD_MAX_STATUS == "") {
			$APD_MAX_STATUS = "none";
			} 
			
			IF ($APD_MIN_STATUS == "") {
			$APD_MIN_STATUS = "none";
			} 
			
			IF ($FED_MAX_STATUS == "") {
			$FED_MAX_STATUS = "none";
			}
			
			IF ($FED_MIN_STATUS == "") {
			$FED_MIN_STATUS = "none";
			} 
			
			IF ($STATE_MIN_EXP == "") {
			$STATE_MIN_EXP = "none";
			}
			
			IF ($FED_MIN_EXP == "") {
			$FED_MIN_EXP = "none";
			}
			
			//set default pattern status to empty
			$pattern = "";
			
			//If Landtype is none
			IF ($LAND == "") {
			$LAND = "none";
			}

			//----------------------------------------------------------------------------------------------------
			//edit the style based upon the permit conditions and status
			//----------------------------------------------------------------------------------------------------
			
			//format if land = none or null
			IF ($LAND == "" or $LAND == "none") {
			$color = 'black';
			$addition = ' | ' ."ST-".$APD_MIN_STATUS." FED-".$FED_MIN_STATUS;
				
				IF (($APD_MIN_STATUS == "APP" or $APD_MAX_STATUS == "APP") AND ($FED_MIN_STATUS == "APP" OR $FED_MAX_STATUS == "APP")){
				//$pattern = "(format: solid)";
				$addition = ' | ' ."ST-APP FED-APP";
				
				//SET BACKGROUND TO BLUE
				$e->style = "background-color: #00FFFF; border-left: .5pt solid black; border-right: .5pt solid black; border-top: .5pt solid black; border-bottom: .5pt solid black; text-align: center; font-size: 21; font-family: 'arial'; font-weight: bold; vertical-align:middle;";
				}
				
				elseif (($APD_MIN_STATUS == "APP" or $APD_MAX_STATUS == "APP") AND ($FED_MIN_STATUS == "SUB" OR $FED_MAX_STATUS == "SUB")){
				$pattern = "(format: xlGray8 41)";
				$addition = ' | ' ."ST-APP FED-SUB";
				}
				
				elseif (($APD_MIN_STATUS == "SUB" or $APD_MAX_STATUS == "SUB") AND ($FED_MIN_STATUS == "APP" OR $FED_MAX_STATUS == "APP")){
				$pattern = "(format: xlGray8 41)";
				$addition = ' | ' ."ST-SUB FED-APP";
				}
				
				elseif (($APD_MIN_STATUS == "APP" or $APD_MAX_STATUS == "APP") AND ($FED_MIN_STATUS == "none" OR $FED_MAX_STATUS == "none")){
				$pattern = "(format: x1Up 8)";
				$addition = ' | ' ."ST-APP FED-NONE";
				}
				
				elseif (($APD_MIN_STATUS == "none" or $APD_MAX_STATUS == "none") AND ($FED_MIN_STATUS == "APP" OR $FED_MAX_STATUS == "APP")){
				$pattern = "(format: x1Up 8)";
				$addition = ' | ' ."ST-NONE FED-APP";
				}
				
				elseif (($APD_MIN_STATUS == "SUB" or $APD_MAX_STATUS == "SUB") AND ($FED_MIN_STATUS == "SUB" OR $FED_MAX_STATUS == "SUB")){
				//$pattern = "(format: solid 20)";
				$addition = ' | ' ."ST-SUB FED-SUB";
				$e->style = "background-color: #ccffff; border-left: .5pt solid black; border-right: .5pt solid black; border-top: .5pt solid black; border-bottom: .5pt solid black; text-align: center; font-size: 21; font-family: 'arial'; font-weight: bold; vertical-align:middle;";
				}
				
				elseif (($APD_MIN_STATUS == "SUB" or $APD_MAX_STATUS == "SUB") AND ($FED_MIN_STATUS == "none" OR $FED_MAX_STATUS == "none")){
				$pattern = "(format: xlLightHorizontal 36)";
				$addition = ' | ' ."ST-SUB FED-NONE";
				}
				
				elseif (($APD_MIN_STATUS == "none" or $APD_MAX_STATUS == "none") AND ($FED_MIN_STATUS == "SUB" OR $FED_MAX_STATUS == "SUB")){
				$pattern = "(format: xlLightHorizontal 36)";
				$addition = ' | ' ."ST-NONE FED-SUB";
				}
				
				elseif ($APD_MIN_STATUS == "none" AND $FED_MIN_STATUS == "none"){
				//$pattern = "(format: xlLightHorizontal 36)";
				//$addition = "ST-".$APD_MIN_STATUS." FED-".$FED_MIN_STATUS;
				$addition = "";
				}
			}
			
			//format if land = FEE
			IF ($LAND == "FEE") {
			$color = 'black';
			//$addition = "ST-".$APD_MIN_STATUS;
			$addition = "";
			
				IF ($APD_MIN_STATUS == "APP" or $APD_MAX_STATUS == "APP"){
				//$pattern = "(format: solid)";
				//$addition = "ST-APP";
				
				//SET BACKGROUND TO BLUE
				$e->style = "background-color: #00FFFF; border-left: .5pt solid black; border-right: .5pt solid black; border-top: .5pt solid black; border-bottom: .5pt solid black; text-align: center; font-size: 21; font-family: 'arial'; font-weight: bold; vertical-align:middle;";
				}
				
				elseif ($APD_MIN_STATUS == "SUB" or $APD_MAX_STATUS == "SUB"){
				//$addition = "ST-SUB";
				$e->style = "background-color: #ccffff; border-left: .5pt solid black; border-right: .5pt solid black; border-top: .5pt solid black; border-bottom: .5pt solid black; text-align: center; font-size: 21; font-family: 'arial'; font-weight: bold; vertical-align:middle;";
				}
			}
			
			//What to do if Landtype is Split Estate or FED
			IF ($LAND == "SPLIT ESTATE" or $LAND == "FED") {
			//If both State and Federal permits are approved on at least one well on the visit, then no change other than font color and additional comment.
			
				IF (($APD_MIN_STATUS == "APP" or $APD_MAX_STATUS == "APP") AND ($FED_MIN_STATUS == "APP" OR $FED_MAX_STATUS == "APP")){
				//$pattern = "(format: solid)";
				$addition = ' | ' ."ST-APP FED-APP";
				
				//SET BACKGROUND TO BLUE
				$e->style = "background-color: #00FFFF; border-left: .5pt solid black; border-right: .5pt solid black; border-top: .5pt solid black; border-bottom: .5pt solid black; text-align: center; font-size: 21; font-family: 'arial'; font-weight: bold; vertical-align:middle;";
				}
				
				elseif (($APD_MIN_STATUS == "APP" or $APD_MAX_STATUS == "APP") AND ($FED_MIN_STATUS == "SUB" OR $FED_MAX_STATUS == "SUB")){
				$pattern = "(format: xlGray8 41)";
				$addition = ' | ' ."ST-APP FED-SUB";
				}
				
				elseif (($APD_MIN_STATUS == "SUB" or $APD_MAX_STATUS == "SUB") AND ($FED_MIN_STATUS == "APP" OR $FED_MAX_STATUS == "APP")){
				$pattern = "(format: xlGray8 41)";
				$addition = ' | ' ."ST-SUB FED-APP";
				}
				
				elseif (($APD_MIN_STATUS == "APP" or $APD_MAX_STATUS == "APP") AND ($FED_MIN_STATUS == "none" OR $FED_MAX_STATUS == "none")){
				$pattern = "(format: x1Up 8)";
				$addition = ' | ' ."ST-APP FED-NONE";
				}
				
				elseif (($APD_MIN_STATUS == "none" or $APD_MAX_STATUS == "none") AND ($FED_MIN_STATUS == "APP" OR $FED_MAX_STATUS == "APP")){
				$pattern = "(format: x1Up 8)";
				$addition = ' | ' ."ST-NONE FED-APP";
				}
				
				elseif (($APD_MIN_STATUS == "SUB" or $APD_MAX_STATUS == "SUB") AND ($FED_MIN_STATUS == "SUB" OR $FED_MAX_STATUS == "SUB")){
				//$pattern = "(format: solid 20)";
				$addition = ' | ' ."ST-SUB FED-SUB";
				$e->style = "background-color: #ccffff; border-left: .5pt solid black; border-right: .5pt solid black; border-top: .5pt solid black; border-bottom: .5pt solid black; text-align: center; font-size: 21; font-family: 'arial'; font-weight: bold; vertical-align:middle;";
				}
				
				elseif (($APD_MIN_STATUS == "SUB" or $APD_MAX_STATUS == "SUB") AND ($FED_MIN_STATUS == "none" OR $FED_MAX_STATUS == "none")){
				$pattern = "(format: xlLightHorizontal 36)";
				$addition = ' | ' ."ST-SUB FED-NONE";
				}
				
				elseif (($APD_MIN_STATUS == "none" or $APD_MAX_STATUS == "none") AND ($FED_MIN_STATUS == "SUB" OR $FED_MAX_STATUS == "SUB")){
				$pattern = "(format: xlLightHorizontal 36)";
				$addition = ' | ' ."ST-NONE FED-SUB";
				}
				
				elseif ($APD_MIN_STATUS == "none" AND $FED_MIN_STATUS == "none"){
				//$pattern = "(format: xlLightHorizontal 36)";
				//$addition = "ST-".$APD_MIN_STATUS." FED-".$FED_MIN_STATUS;
				$addition = "";
				}
			}
			
			//format font color
			IF ($LAND == "SPLIT ESTATE") {
			$color = '#8C001A';
			}
			IF ($LAND == "FED") {
			$color = 'green';
			}
			
			
			//get href value
			$link = $a->href;
			//echo $link;
			
			//only do sql query to match pad on first innertext child element
			break;
		}	
	
	//set multiple solution pads to zero
	$multiple_solution_pads = 0;
	
	//see what the original innertext looks like (innertext is text that appears in on final display)(would include <a> tag and href but not <td> tag)
	//echo $e->innertext;
	
	//----------------------------------------------------------------------------------------------------
	// create custom messge that includes the Pad, Number of Wells, and Comments on the pad
	//$pad_number_comments that contains the pad, number of wells, and comments after without an href link
	//----------------------------------------------------------------------------------------------------

	//get the pad name, number of wells, and comments without a hyperlink:
	$first_arrow = strpos($e->innertext, ">",1);
	//echo "<br>pso first arrow:".$first_arrow;
	$pad_number_comments = substr($e->innertext, $first_arrow+1);
	//echo "<br>text of stuff after first arrow:".$pad_number_comments;
	$pad_number_comments = str_replace("</a>","",$pad_number_comments);
	$last_comment_nbsp_position = strripos($pad_number_comments, '&nbsp;');
	//echo"<br>last nbsp pos:".$last_comment_nbsp_position;
	$stuff_after_comments = substr($pad_number_comments, $last_comment_nbsp_position);
	$pad_number_comments = substr($pad_number_comments, 0,$last_comment_nbsp_position);
	//This is an additional comment that show up in blue in wellcore and comes on the line below the pad, no of wells, and comments
	$stuff_after_comments = str_replace("div","span",$stuff_after_comments);
	//$stuff_after_comments = str_replace("color : #0000ff","font-size: 30; color : #0000ff",$stuff_after_comments);
	//echo $stuff_after_comments;
	
	//$stuff_after_comments = preg_replace("span","span style=\"font-size: 21;\"",$stuff_after_comments,1);

	//declare no solutions and multiple solutions as blank
	$no_solutions = "";
	$multiple_solutions = "";
	
	//If their are no pad matches in Datavision report that on the pad
	if ($rows == 0) {
	$no_solutions = 'NO RESULTS FOUND for this Pad Name around these Rig Dates with this Rig in Datavision';
	//increment error entries for total that is displayed on bottom if debug=true
	$Error_entries++;
	
	} elseif ($rows >= 2){  //error handling for multiple possible solutions for a pad
	$multiple_solution_pads++;
	$multiple_solutions = 'Multiple Solutions Found';
	
	} else {  //what to insert if everything goes right (1 solution)
	//$str_to_insert = '<span style="color:'. $color . '; display:inline;">'.$pad_number_comments.' | ' . $LAND .' | ' . $addition . '</span>'.$pattern.'<br>'.$stuff_after_comments.' | <a style="font-size: 21;" href="'.$link.'">Webcore Link</a>';  //define string here
	//$str_to_insert = '<span style="color:'. $color . '; display:inline;">'.$pad_number_comments.' | ' . $LAND .' | ' . $addition . '</span>'.$pattern.'<br>'.$stuff_after_comments;  //adjustments for Kim's desired formatting
	$str_to_insert = '<span style="color:'. $color . '; display:inline;">'.$pad_number_comments. $addition . '</span>'.$pattern.'<br>'.$stuff_after_comments;  //adjustments for Kim's desired formatting
	}
	
	//add error messages to possible string
	$str_to_insert = $no_solutions.$multiple_solutions.$str_to_insert;
	
	//$newstr = $str_to_insert;  //add text after the pad information
	//$newstr = substr_replace($e, $str_to_insert, 198, 0);  //add text before the pad information
	//$newstr = substr_replace($e, $str_to_insert, $last_nbsp_position, 0);  //add text after the pad information
	//$newstr = substr_replace($e->innertext, $str_to_insert, $first_arrow+1, 0);  //add text after the pad information
	$newstr = $str_to_insert;
	
	//save the edit to the DOM
	$e->innertext = $newstr;
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
	
/*	Stuff for PHPexcel 
require_once ('PHPExcel/Classes/PHPExcel.php');
file_put_contents('tmp.html',$str);
$objReader = new PHPExcel_Reader_HTML;
$objPHPExcel = $objReader->load('tmp.html');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("myExcelFile.xlsx");

echo "Excel File Saved!";
*/
?>

</body>
</html>

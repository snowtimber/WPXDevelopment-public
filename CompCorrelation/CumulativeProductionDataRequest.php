<?php
//get production data from datavision and create normalized scatter plot with average production line
//____________________________________________________________________________________________________________________

//collect dim well ids from get request attached to url
require('collect_dimwell_ids.php');

$iteration = 0;
foreach ($dim_well_ids as $dim_well_id) {

	//  Get the dim well completion id from the datavision dim.well_completion table via odbc
	require('get_dim_well_completion_id.php');
	//  Get Production data based off of the dim_well_completion_id via odbc and generate a date normalized production array 
	require('get_production_data_odbc.php');
}

require('create_average_normalized_production_curve.php');

// available arrays:
/*
print_r($wellname_array);
echo "<br><br>";
print_r($final_date_array);
echo "<br><br>";
print_r($final_cum_gas_array);
*/
//echo "<br><br>".min($final_date_array);
//echo "<br><br>max: ".max(array_keys($final_date_array, max($final_date_array)));
//echo "<br><br>min: ".min(array_keys($final_date_array, min($final_date_array)));

// get the Production Curve Percentage (PCP) numbers based off the shortest (time) running well
$mindate = min($final_date_array);  //-1 so as to make sure that it is before the shortest well stops reporting
/*
$mindate = floor($mindate/ 10) * 10-10;
if ($mindate < 200) {
$mindate = array_sum($final_date_array)/ (4*count($final_date_array));
}
$mindate = floor($mindate/ 10) * 10;
*/
//echo "<br>mindate: ".$mindate;
$min_date_key = min(array_keys($final_date_array, min($final_date_array)));//this is basically which well has the mindate:

//get the average production on mindate
$average_production_on_mindate = $average_production_array[$mindate]['gas_production_volume'];
//print_r($average_production_array);
//echo "<br><br>".$average_production_on_mindate;
//echo "<br><br>average gas production on mindate: ".$average_production_array[$mindate] ['gas_production_volume'];

//initialize PCP_array with null values
$iter = 0;
foreach ($wellname_array as $wellname_element) {
$PCP_array[$iter] = null;
$iter++;
}

//get the gas production on the $min_date for all curves
foreach ($Normalized_Production_array as $key => $val) {
	if ($val['date'] == $mindate) {
		foreach ($wellname_array as $wellname_element) {
			if ($wellname_element == $val['wellname'] ) {
				$well_key_order = min(array_keys($wellname_array,$val['wellname']));
				//echo "well key order: ".min(array_keys($wellname_array,$val['wellname']));
				//declare the production on the min_date using the wellname_array order
				$production_array_on_mindate[$well_key_order] = $val['gas_production_volume'];
				//declare the PCP on the min_date using the wellname_array order
				$PCP_array[$well_key_order] = round($val['gas_production_volume']/$average_production_on_mindate, 3);
			}
		}
	}
}
$iteration = 0;
foreach ($dim_well_ids as $dim_well_id) {
 
//____________________________________________________________________________________________________________________
//  Get WSN from dim_well table in datavision from dim_well_id
require('WSN_from_dim_well_id.php');

//____________________________________________________________________________________________________________________
//  Get petra GIP data from datavision for Correlations
require('petra_GIP_query.php');

//____________________________________________________________________________________________________________________
//  Get petra Gross Swiped Sand data from datavision for Correlations
require('petra_SwipedSand_query.php');

//____________________________________________________________________________________________________________________
//  Get Cost data
require('cost_query.php');

$iteration++;
}
/*   //more available printable arrays:
echo "<br><br>WSN:";
print_r($WSN);
echo "<br><br>production on mindate:";
print_r($production_array_on_mindate);
echo "<br><br>wellname array:";
print_r($wellname_array);
echo "<br><br>PCP:";
print_r($PCP_array);
echo "<br><br>GIP:";
print_r($SUMGIP2_gross);
echo "<br><br>swiped pay:";
print_r($gross_swiped_sand_array);
*/
?>


<!-- html for page contents -->
<html>
	<head>
		<meta name="viewport" content="initial-scale=1.0, user-scalable=yes">
		<meta charset="utf-8">
		<title>Performance Correlations</title>
		 <!-- load font  -->
		<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
		<style>
		  html, body, #map-canvas {
			font-family: 'open sans', serif;
			height: 100%;
			margin: 0px;
			padding: 0px
		  }
		</style>
		<!--Load the AJAX API for the horizontal Pressure vs. Depth Chart-->
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		
		<!--Load the formatting and generation of the Google Visualization Line Chart-->
		<?php require('gvis_production_curve.php');?> <!-- using $Normalized_Production_array -->
		<?php require('GIPvsPCP.php');?> <!-- using $wellname_array $PCP_array, $SUMGIP2_gross -->
		<?php require('SwipedSandvsPCP.php');?> <!-- using $wellname_array $PCP_array, $gross_swiped_sand_array -->
		<?php require('CostvsPCP.php');?> <!-- using $wellname_array $PCP_array, $cost_array -->
	 </head>
	<body>
		
	<!--A chart for cumulative production on each well and average production -->
	<div align="center" style="height:900px;width:100%x" id="chart_div2"></div> <!-- using $Normalized_Production_array -->
	<table style="width:100%">
	<tr>
			<td width="50%"><div align="left" style="height:450px;width:100%" id="chart_GIPvsPCP"></div></td>
			<td width="50%"><div align="right" style="height:450px;width:100%" id="chart_GrossSwipedSandvsPCP"></div></td>
		</tr>
		<tr>
			<td width="50%"><div align="left" style="height:450px;width:100%" id="chart_CostvsPCP"></div></td>
			<!--<td width="50%"><div align="right" style="height:450px;width:100%" id="chart_GrossSwipedSandvsPCP"></div></td>-->
		</tr>
	</table>

	</body>
</html>

<?php
   //more available printable arrays:
echo "<br>mindate: ".$mindate;
echo "<br><br>WSN:";
print_r($WSN);
echo "<br><br>production on mindate:";
print_r($production_array_on_mindate);
echo "<br><br>final date:";
print_r($final_date_array);
echo "<br><br>wellname array:";
print_r($wellname_array);
echo "<br><br>PCP:";
print_r($PCP_array);
echo "<br><br>GIP:";
print_r($SUMGIP2_gross);
echo "<br><br>swiped pay:";
print_r($gross_swiped_sand_array);
echo "<br><br>cost:";
print_r($cost_array);
//echo "<br><br>average production array:";
//print_r($average_production_array);
?>

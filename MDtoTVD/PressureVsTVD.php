<html>
	<?php
	//Pressure vs. TVD chart
	//Logan Meyer 11/3/14
	
	//declare/initialize arrays for storing gathered data
	//$d2_array = array();
	$d1_array = array();
	
	
	//Get info from HTTP GET request
	if (isset($_GET["well_name"])) {
	$wellname = $_GET["well_name"];
	} else {
	echo "Showing results for default well of:  GM 341-14 <br><br>";
	$wellname = 'GM 341-14';
	}
	
	// Load PHP files and build array of data from various datasets
	require('interpolate_tvd_function.php');
	require('dim_well_request.php');
	require('ow_surveys_query.php');
	//require('interpolate_value.php'); this is covered now by the interpolate tvd function
	require('frac_gradient_query.php');
	require('ow_perfs_query.php');
	require('mw_query.php');
	require('ProductionDataRequest.php');
	//$md_interpolate = $_GET["interpolate_value"];
	
	
	// if debug is checked and requested, print interpolated tvd info based off of GET interplolate_value param
	if (isset($_GET["debug"])) {
		$debug = $_GET["debug"];
		if (isset($_GET["interpolate_value"])) {
			$md_interpolate = $_GET["interpolate_value"];
			echo "<br><br>".$md_interpolate."<br>";
			echo "<br><br>Interpolated TVD is: ".interpolate_tvd($md_interpolate,$surveys_array_filtered);
		}
	}
	
	//if check all is checked, check mark all of the options
	if (isset($_GET["check all"])) {
	$_GET["break_pressure"] = 'true';
	$_GET["bottom_frac_interval"] = 'true';
	$_GET["top_frac_interval"] = 'true';
	$_GET["frac_gradient_initial"] = 'true';
	$_GET["frac_gradient_final"] = 'true';
	$_GET["perfs"] = 'true';
	$_GET["initial_pressure"] = 'true';
	$_GET["ISIP"] = 'true';
	$_GET["FSIP"] = 'true';
	$_GET["final_pressure"] = 'true';
	$_GET["average_pressure"] = 'true';
	$_GET["max_pressure"] = 'true';
	$_GET["normal_gradient"] = 'true';
	$_GET["overburden_gradient"] = 'true';
	$_GET["drilling_pressure"] = 'true';
	$_GET["petra_pressure"] = 'true';
	$_GET["petra_min_pressure"] = 'true';
	$_GET["petra_max_pressure"] = 'true';
	}
// if check none is checked, clear all of the options
	if (isset($_GET["check_none"])) {
	$_GET["break_pressure"] = null;
	$_GET["bottom_frac_interval"] = null;
	$_GET["top_frac_interval"] = null;
	$_GET["frac_gradient_initial"] = null;
	$_GET["frac_gradient_final"] = null;
	$_GET["perfs"] = null;
	$_GET["initial_pressure"] = null;
	$_GET["ISIP"] = null;
	$_GET["FSIP"] = null;
	$_GET["final_pressure"] =null;
	$_GET["average_pressure"] = null;
	$_GET["max_pressure"] = null;
	$_GET["normal_gradient"] = 'true';
	$_GET["overburden_gradient"] = null;
	$_GET["drilling_pressure"] = null;
	$_GET["petra_pressure"] = null;
	$_GET["petra_min_pressure"] = null;
	$_GET["petra_max_pressure"] = null;
	}
	?>
	
	<!-- Header Information -->
  <head>
	<meta name="viewport" content="initial-scale=1.0, user-scalable=yes">
    <meta charset="utf-8">
    <title>Pressure vs. TVD</title>
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
	<style type="text/css">
   .labels {
     color: red;
     background-color: white;
     font-family: "Lucida Grande", "Arial", sans-serif;
     font-size: 10px;
     font-weight: bold;
     text-align: center;
     width: 40px;
     border: 2px solid black;
     white-space: nowrap;
   }
	</style>
  
  
    <!--
	_______________________________________________________________________________________________________________________________
	Load the AJAX API for the horizontal Pressure vs. Depth Chart-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript">
    
    // Load the Visualization API and the corechart package.
    google.load("visualization", "1", {packages:["corechart"]});
	
		// Set a callback to run when the Google Visualization API is loaded.
		google.setOnLoadCallback(drawChart);
		
		function drawChart() {
		var data = new google.visualization.DataTable();
		data.addColumn('number', 'TVD'); // Implicit domain label col.
		<?php 
		if (isset($_GET["break_pressure"])) {
		echo "data.addColumn('number', 'break pressure'); // Implicit series 1 data col.
		";
		} if (isset($_GET["bottom_frac_interval"])) {
		echo "data.addColumn('number', 'Bottom Frac Interval'); // interval role col.<
		";
		} if (isset($_GET["top_frac_interval"])) {
		echo "data.addColumn('number', 'Top Frac Interval');  // interval role col.
		";
		} if (isset($_GET["frac_gradient_initial"])) {
		echo "data.addColumn('number', 'Frac Gradient Initial'); // Implicit series 2 data col.
		";
		} if (isset($_GET["frac_gradient_final"])) {
		echo "data.addColumn('number', 'Frac Gradient Final'); // Implicit series 3 data col.
		";
		echo "//data.addColumn('number', 'md_top_shot'); // Implicit series 4 data col.
		";
		} if (isset($_GET["perfs"])) {
		echo "data.addColumn('number', 'tvd_avg_perf_shot'); // Implicit series 5 data col.
		";
		echo "data.addColumn({type:'string', role:'annotationText'}); // annotation role col. for interval_type
		";
		echo "//data.addColumn({type:'number', role:'annotationText'}); // annotation role col. for charge size
		";
		echo "//data.addColumn('number', 'mud_weight'); // Implicit series 6 data col.
		";
		} if (isset($_GET["initial_pressure"])) {
		echo "data.addColumn('number', 'initial_pressure'); // Implicit series 6 data col.
		";
		} if (isset($_GET["ISIP"])) {
		echo "data.addColumn('number', 'ISIP'); // Implicit series 6 data col.
		";
		} if (isset($_GET["FSIP"])) {
		echo "data.addColumn('number', 'FSIP'); // Implicit series 6 data col.
		";
		} if (isset($_GET["final_pressure"])) {
		echo "data.addColumn('number', 'final_pressure'); // Implicit series 6 data col.
		";
		} if (isset($_GET["average_pressure"])) {
		echo "data.addColumn('number', 'average_pressure'); // Implicit series 6 data col.
		";
		} if (isset($_GET["max_pressure"])) {
		echo "data.addColumn('number', 'max_pressure'); // Implicit series 6 data col.
		";
		echo "data.addColumn({type:'number', role:'annotationText'}); // annotation role col. for main body
		";
		} if (isset($_GET["normal_gradient"])) {
		echo "data.addColumn('number', 'Normal Gradient (0.433 psi/ft)'); // Implicit series 6 data col.
		";
		echo "data.addColumn({type:'string', role:'annotation'}); // annotation role col. for zone
		";
		} if (isset($_GET["overburden_gradient"])) {
		echo "data.addColumn('number', 'Overburden Gradient (1.07 psi/ft)'); // Implicit series 6 data col.
		";
		} if (isset($_GET["drilling_pressure"])) {
		echo "data.addColumn('number', 'Drilling-Mud Pressure (.052*MW*TVD)'); // Implicit series 6 data col.
		";
		}if (isset($_GET["petra_pressure"])) {
		echo "data.addColumn('number', 'Petra Model Pressure'); // Implicit series 6 data col.
		";
		}if (isset($_GET["petra_min_pressure"])) {
		echo "data.addColumn('number', 'Petra Model Min Pressure'); // Implicit series 6 data col.
		";
		}if (isset($_GET["petra_max_pressure"])) {
		echo "data.addColumn('number', 'Petra Model Max Pressure'); // Implicit series 6 data col.
		";
		}
		?>
		
		//data.addColumn({type:'string', role:'annotationText'}); // annotationText col.
		//data.addColumn({type:'boolean',role:'certainty'}); // certainty col.
		
		data.addRows([
		
			<?php 
			
			//set $i to 0
			$i =  0;
			
			//Length of the array
			$d1_array_length = count($d1_array);
			
			foreach ($d1_array as $array) {
				
				//set all the values to null
				$tvd = null;
				$break_pressure = null;
				$bottom_interval = null;
				$top_interval = null;
				$frac_gradient_initial = null;
				$frac_gradient_final = null;
				$zone = null;
				$md_top_shot = null;
				$tvd_avg_shot = null;
				$interval_type = null;
				$charge_size = null;
				$mud_weight = null;
				$initial_pressure = null;
				$ISIP = null;
				$FSIP = null;
				$final_pressure = null;
				$average_pressure = null;
				$max_pressure = null;
				$main_body = null;
				$normal_gradient = null;
				$overburden_gradient = null;
				$drilling_pressure = null;
				$petra_pressure = null;
				$petra_min_pressure = null;
				$petra_max_pressure = null;
				
//  $d1_array[] = array('tvd' => interpolate_tvd($average_depth, $surveys_array_filtered), 'break pressure' => $break_pressure, 'Frac Gradient Initial' => $frac_gradient_initial, 'Frac Gradient Final' => $frac_gradient_final, 'zone' => $zone, 'Initial Pressure' => $initial_pressure, 'ISIP' => $initial_shut_in_pressure, 'FSIP' => $final_shut_in_pressure, 'Final Pressure' => $final_pressure, 'Avg Pressure' => $average_pressure, 'Max Pressure' => $Max_Pressure, 'Main_Body' => $Main_Body);
				foreach($array as $x=>$x_value) {
				//echo "Key=" . $x . ", Value=" . $x_value;
				//echo "<br>";
				
					//define the different elements
					if( $x == "tvd"){ 
						$tvd=$x_value;
					}
					if( $x == "break pressure"){ 
						$break_pressure=$x_value;
					}
					if( $x == "Frac Gradient Initial"){ 
						$frac_gradient_initial=$x_value;
					}
					if( $x == "Frac Gradient Final"){ 
						$frac_gradient_final=$x_value;
					}
					if( $x == "zone"){ 
						$zone=$x_value;
					}
					if( $x == "md_top_shot"){ 
						$md_top_shot=$x_value;
					}
					if( $x == "tvd_avg_shot"){ 
						$tvd_avg_shot=$x_value;
					}
					if( $x == "interval_type"){ 
						$interval_type=$x_value;
					}
					if( $x == "charge_size"){ 
						$charge_size=$x_value;
					}
					if( $x == "mud_weight"){ 
						$mud_weight=$x_value;
					}
					if( $x == "Initial Pressure"){ 
						$initial_pressure=$x_value;
					}
					if( $x == "ISIP"){ 
						$ISIP=$x_value;
					}
					if( $x == "FSIP"){ 
						$FSIP=$x_value;
					}
					if( $x == "Final Pressure"){ 
						$final_pressure=$x_value;
					}
					if( $x == "Avg Pressure"){ 
						$average_pressure=$x_value;
					}
					if( $x == "Max Pressure"){ 
						$max_pressure=$x_value;
					}
					if( $x == "Main_Body"){ 
						$main_body=$x_value;
					}
					if( $x == "top_interval"){ 
						$top_interval=$x_value;
					}
					if( $x == "bottom_interval"){ 
						$bottom_interval=$x_value;
					}
					if( $x == "Normal Gradient"){ 
						$normal_gradient=$x_value;
					}
					if( $x == "Overburden Gradient"){ 
						$overburden_gradient=$x_value;
					}
					if( $x == "drilling_pressure"){ 
						$drilling_pressure=$x_value;
					}
					if( $x == "Petra Pressure"){ 
						$petra_pressure=$x_value;
					}
					if( $x == "Petra Min Pressure"){ 
						$petra_min_pressure=$x_value;
					}
					if( $x == "Petra Max Pressure"){ 
						$petra_max_pressure=$x_value;
					}
				
				}
				/*
				if ($i==$d1_array_length-1){
					echo "[".$tvd.",".$break_pressure.", ".$bottom_interval.", ".$top_interval.", ".$frac_gradient_initial.", ".$frac_gradient_final.", '".$zone."', ".$tvd_avg_shot.", '".$interval_type."', ".$initial_pressure.", ".$ISIP.", ".$FSIP.", ".$final_pressure.", ".$average_pressure.", ".$max_pressure.", ".$main_body.", ".$normal_gradient.", ".$overburden_gradient.", ".$mud_pressure.",]
			";
					
				} else {*/
					echo "[".$tvd.",";
					if (isset($_GET["break_pressure"])) {
					echo $break_pressure.", ";
					} if (isset($_GET["bottom_frac_interval"])) {
					echo $bottom_interval.", ";
					} if (isset($_GET["top_frac_interval"])) {
					echo $top_interval.", ";
					} if (isset($_GET["frac_gradient_initial"])) {
					echo $frac_gradient_initial.", ";
					} if (isset($_GET["frac_gradient_final"])) {
					echo $frac_gradient_final.", ";
					} if (isset($_GET["perfs"])) {
					echo $tvd_avg_shot.", '".$interval_type."', ";
					} if (isset($_GET["initial_pressure"])) {
					echo $initial_pressure.", ";
					} if (isset($_GET["ISIP"])) {
					echo $ISIP.", ";
					} if (isset($_GET["FSIP"])) {
					echo $FSIP.", ";
					} if (isset($_GET["final_pressure"])) {
					echo $final_pressure.", ";
					} if (isset($_GET["average_pressure"])) {
					echo $average_pressure.", ";
					} if (isset($_GET["max_pressure"])) {
					echo $max_pressure.", ".$main_body.", ";
					} if (isset($_GET["normal_gradient"])) {
					echo $normal_gradient.", '".$zone."', ";
					} if (isset($_GET["overburden_gradient"])) {
					echo $overburden_gradient.", ";
					} if (isset($_GET["drilling_pressure"])) {
					echo $drilling_pressure.",";
					} if (isset($_GET["petra_pressure"])) {
					echo $petra_pressure.",";
					} if (isset($_GET["petra_min_pressure"])) {
					echo $petra_min_pressure.",";
					} if (isset($_GET["petra_max_pressure"])) {
					echo $petra_max_pressure.",";
					}
					echo "],
					";
				
				$i++;
			}
			?>	
			
		]);

			var options = {
				 title: 'Pressure vs. Depth (horizontal)',
				annotations: {
									slantedText: true,  //not working
									slantedTextAngle: 90,  // not working
									/*boxStyle: {
									  stroke: '#888',           // Color of the box outline.
									  strokeWidth: 1,           // Thickness of the box outline.
									  rx: 15,                   // x-radius of the corner curvature.
									  ry: 15,                   // y-radius of the corner curvature.
									  gradient: {               // Attributes for linear gradient fill.
										color1: '#fbf6a7',      // Start color for gradient.
										color2: '#33b679',      // Finish color for gradient.
										x1: '0%', y1: '0%',     // Where on the boundary to start and end the
										x2: '100%', y2: '100%', // color1/color2 gradient, relative to the
																// upper left corner of the boundary.
										useObjectBoundingBoxUnits: true // If true, the boundary for x1, y1,
																		// x2, and y2 is the box. If false,
																		// it's the entire chart.
									  }
									}*/
								  },
				pointShape: 'circle',
				pointSize: '5',
				<?php
				if (isset($_GET["smoothing"])) {
				echo "curveType: 'function',";
				}
				?>
				//curveType: 'function',
				//orientation: 'vertical',
				legend: { 
					position: 'in',
					textStyle: {
						fontSize: 10,			
									}							
							},
				vAxes: {0: {title: 'Pressure (psi)', logScale: false},
					1: {title: 'Gradient (psi/ft)',
						logScale: false, 
						maxValue: 1,
						slantedText: true,
						slantedTextAngle: 90
						}},
				//hAxis: {
				//    title: "Pressure (psi)", format:  '#,###'
				//},
				hAxes: {0: {title: 'TVD', logScale: false,
								<?php
								if (isset($_GET["drilling_pressure"])) {
								echo "
								viewWindow:{
									min:".$top_limit.",
									max:".$bottom_limit."
								  }";
								  }
								  ?>
								  },
					1: {title: 'Gradient (psi/ft)', 
						logScale: false, 
						maxValue: 1
						}},
				seriesType: "line",
				
				<?php 
				echo "series: {";
				$j = 0;
				if (isset($_GET["break_pressure"])) {
				echo 
					$j.": { //break pressure
						type: 'line',
						interpolateNulls: 'true',
						targetAxisIndex:0,
						pointShape: 'polygon',
						pointSize: 20,
					},";
					$j++;
				}
				if (isset($_GET["bottom_frac_interval"])) {
				echo 
					$j.": { //Bottom Frac Interval
						type: 'line',
						interpolateNulls: 'true',
						targetAxisIndex:0
					},";
						$j++;
				}
				if (isset($_GET["top_frac_interval"])) {
				echo 
					$j.": {  //Top Frac Interval
						type: 'line',
						interpolateNulls: 'true',
						targetAxisIndex:0
					},";
					$j++;
				}
				if (isset($_GET["frac_gradient_initial"])) {
				echo 
					$j.": {//Frac Gradient Initial
						type: 'line',
						//interpolateNulls: 'true',
						lineDashStyle: [2, 3],
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						pointShape: 'square',
						pointSize: 10,
					},";
					$j++;
				}
				if (isset($_GET["frac_gradient_final"])) {
				echo 
					$j.": { //Frac Gradient Final
						type: 'line',
						//interpolateNulls: 'true',
						lineDashStyle: [2, 3],
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						pointShape: 'square',
						pointSize: 10,
					},";
					$j++;
				}
				if (isset($_GET["perfs"])) {
				echo 
					$j.": {  ///tvd_avg_perf_shot
						type: 'line',
						lineWidth: 1,
						lineDashStyle: [1, 3],
						interpolateNulls: 'true',
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90
					},";
					$j++;
				}
				if (isset($_GET["initial_pressure"])) {
				echo 
					$j.": { //initial_pressure
						type: 'line',
						interpolateNulls: 'true',
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						pointShape: 'triangle',
						pointSize: 20,
					},";
					$j++;
				}
				if (isset($_GET["ISIP"])) {
				echo 
					$j.": {  //ISIP
						type: 'line',
						lineWidth: 2,
						interpolateNulls: 'true',
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						pointShape: 'square',
						pointSize: 20,
					},";
					$j++;
				}
				if (isset($_GET["FSIP"])) {
				echo 
					$j.": {  //FSIP
						type: 'line',
						lineWidth: 2,
						interpolateNulls: 'true',
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						pointShape: 'square',
						pointSize: 20,
					},";
					$j++;
				}
				if (isset($_GET["final_pressure"])) {
				echo 
					$j.": {  //final_pressure
						type: 'line',
						lineWidth: 2,
						interpolateNulls: 'true',
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						pointShape: 'triangle',
						pointSize: 20,
					},";
					$j++;
				}
				if (isset($_GET["average_pressure"])) {
				echo 
					$j.": {  //average_pressure
						type: 'line',
						lineWidth: 2,
						interpolateNulls: 'true',
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						pointShape: 'triangle',
						pointSize: 20,
					},";
					$j++;
				}
				if (isset($_GET["max_pressure"])) {
				echo 
					$j.": {  //max_pressure
						type: 'line',
						lineWidth: 2,
						interpolateNulls: 'true',
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						pointShape: 'triangle',
						pointSize: 20,
					},";
					$j++;
				}
				if (isset($_GET["normal_gradient"])) {
				echo 
					$j.": {  //normal_gradient
						type: 'line',
						lineWidth: 4,
						//interpolateNulls: 'true',
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						pointShape: 'triangle',
						pointSize: 20,
						//color: 'orange',
					},";
					$j++;
				}
				if (isset($_GET["overburden_gradient"])) {
				echo 
					$j.": {  //overburden_gradient
						type: 'line',
						lineWidth: 2,
						//interpolateNulls: 'true',
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						pointShape: 'triangle',
						pointSize: 20,
					},";
					$j++;
				}
				if (isset($_GET["drilling_pressure"])) {
				echo 
					$j.": {  //mud_pressure
						type: 'line',
						lineWidth: 2,
						//interpolateNulls: 'true',
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						//pointShape: 'star',
						pointSize: 10,
					},";
					$j++;
				}
				if (isset($_GET["petra_pressure"])) {
				echo 
					$j.": {  //petra_pressure
						type: 'line',
						lineWidth: 2,
						//interpolateNulls: 'true',
						//targetAxisIndex:0,
						//slantedText: true,
						//slantedTextAngle: 90,
						pointShape: 'star',
						pointSize: 25,
						color: 'black',
					},";
					$j++;
				}
				if (isset($_GET["petra_min_pressure"])) {
				echo 
					$j.": {  //petra_min_pressure
						type: 'line',
						lineWidth: 2,
						//interpolateNulls: 'true',
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						//pointShape: 'circle',
						pointSize: 10,
						color: 'red',
					},";
					$j++;
				}
				if (isset($_GET["petra_max_pressure"])) {
				echo 
					$j.": {  //petra_max_pressure
						type: 'line',
						lineWidth: 2,
						//interpolateNulls: 'true',
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						//pointShape: 'circle',
						pointSize: 10,
					},";
					$j++;
				}
				
				?>
				
				}
			};

			var chart = new google.visualization.LineChart(document.getElementById('chart_div'));

			chart.draw(data, options);
		}
    </script>
	<!-- 
	_______________________________________________________________________________________________________________________________
	load the javascript for the vertical Pressure vs. Depth Chart 
	-->
	<script type="text/javascript">
    
    // Load the Visualization API and the corechart package.
    google.load("visualization", "1", {packages:["corechart"]});
	
		// Set a callback to run when the Google Visualization API is loaded.
		google.setOnLoadCallback(drawChart);
		
		function drawChart() {
		var data = new google.visualization.DataTable();
		data.addColumn('number', 'TVD'); // Implicit domain label col.
		<?php 
		if (isset($_GET["break_pressure"])) {
		echo "data.addColumn('number', 'break pressure'); // Implicit series 1 data col.
		";
		} if (isset($_GET["bottom_frac_interval"])) {
		echo "data.addColumn('number', 'Bottom Frac Interval'); // interval role col.<
		";
		} if (isset($_GET["top_frac_interval"])) {
		echo "data.addColumn('number', 'Top Frac Interval');  // interval role col.
		";
		} if (isset($_GET["frac_gradient_initial"])) {
		echo "data.addColumn('number', 'Frac Gradient Initial'); // Implicit series 2 data col.
		";
		} if (isset($_GET["frac_gradient_final"])) {
		echo "data.addColumn('number', 'Frac Gradient Final'); // Implicit series 3 data col.
		";
		echo "//data.addColumn('number', 'md_top_shot'); // Implicit series 4 data col.
		";
		} if (isset($_GET["perfs"])) {
		echo "data.addColumn('number', 'tvd_avg_perf_shot'); // Implicit series 5 data col.
		";
		echo "data.addColumn({type:'string', role:'annotationText'}); // annotation role col. for interval_type
		";
		echo "//data.addColumn({type:'number', role:'annotationText'}); // annotation role col. for charge size
		";
		echo "//data.addColumn('number', 'mud_weight'); // Implicit series 6 data col.
		";
		} if (isset($_GET["initial_pressure"])) {
		echo "data.addColumn('number', 'initial_pressure (wellhead)'); // Implicit series 6 data col.
		";
		} if (isset($_GET["ISIP"])) {
		echo "data.addColumn('number', 'ISIP (adjusted for normal gradient)'); // Implicit series 6 data col.
		";
		} if (isset($_GET["FSIP"])) {
		echo "data.addColumn('number', 'FSIP (adjusted for normal gradient)'); // Implicit series 6 data col.
		";
		} if (isset($_GET["final_pressure"])) {
		echo "data.addColumn('number', 'final_pressure (wellhead)'); // Implicit series 6 data col.
		";
		} if (isset($_GET["average_pressure"])) {
		echo "data.addColumn('number', 'average_pressure (wellhead)'); // Implicit series 6 data col.
		";
		} if (isset($_GET["max_pressure"])) {
		echo "data.addColumn('number', 'max_pressure (wellhead)'); // Implicit series 6 data col.
		";
		echo "data.addColumn({type:'number', role:'annotationText'}); // annotation role col. for main body
		";
		} if (isset($_GET["normal_gradient"])) {
		echo "data.addColumn('number', 'Normal Gradient (0.433 psi/ft)'); // Implicit series 6 data col.
		";
		echo "data.addColumn({type:'string', role:'annotation'}); // annotation role col. for zone
		";
		} if (isset($_GET["overburden_gradient"])) {
		echo "data.addColumn('number', 'Overburden Gradient (1.07 psi/ft)'); // Implicit series 6 data col.
		";
		} if (isset($_GET["drilling_pressure"])) {
		echo "data.addColumn('number', 'Drilling-Mud Pressure (.052*MW*TVD)'); // Implicit series 6 data col.
		";
		}if (isset($_GET["petra_pressure"])) {
		echo "data.addColumn('number', 'Petra Model Pressure'); // Implicit series 6 data col.
		";
		}if (isset($_GET["petra_min_pressure"])) {
		echo "data.addColumn('number', 'Petra Model Min Pressure'); // Implicit series 6 data col.
		";
		}if (isset($_GET["petra_max_pressure"])) {
		echo "data.addColumn('number', 'Petra Model Max Pressure'); // Implicit series 6 data col.
		";
		}
		?>
		
		//data.addColumn({type:'string', role:'annotationText'}); // annotationText col.
		//data.addColumn({type:'boolean',role:'certainty'}); // certainty col.
		
		data.addRows([
		
			<?php 
			
			//set $i to 0
			$i =  0;
			
			//Length of the array
			$d1_array_length = count($d1_array);
			
			foreach ($d1_array as $array) {
				
				//set all the values to null
				$tvd = null;
				$break_pressure = null;
				$bottom_interval = null;
				$top_interval = null;
				$frac_gradient_initial = null;
				$frac_gradient_final = null;
				$zone = null;
				$md_top_shot = null;
				$tvd_avg_shot = null;
				$interval_type = null;
				$charge_size = null;
				$mud_weight = null;
				$initial_pressure = null;
				$ISIP = null;
				$FSIP = null;
				$final_pressure = null;
				$average_pressure = null;
				$max_pressure = null;
				$main_body = null;
				$normal_gradient = null;
				$overburden_gradient = null;
				$drilling_pressure = null;
				$petra_pressure = null;
				$petra_min_pressure = null;
				$petra_max_pressure = null;
				
//  $d1_array[] = array('tvd' => interpolate_tvd($average_depth, $surveys_array_filtered), 'break pressure' => $break_pressure, 'Frac Gradient Initial' => $frac_gradient_initial, 'Frac Gradient Final' => $frac_gradient_final, 'zone' => $zone, 'Initial Pressure' => $initial_pressure, 'ISIP' => $initial_shut_in_pressure, 'FSIP' => $final_shut_in_pressure, 'Final Pressure' => $final_pressure, 'Avg Pressure' => $average_pressure, 'Max Pressure' => $Max_Pressure, 'Main_Body' => $Main_Body);
				foreach($array as $x=>$x_value) {
				//echo "Key=" . $x . ", Value=" . $x_value;
				//echo "<br>";
				
					//define the different elements
					if( $x == "tvd"){ 
						$tvd=$x_value;
					}
					if( $x == "break pressure"){ 
						$break_pressure=$x_value;
					}
					if( $x == "Frac Gradient Initial"){ 
						$frac_gradient_initial=$x_value;
					}
					if( $x == "Frac Gradient Final"){ 
						$frac_gradient_final=$x_value;
					}
					if( $x == "zone"){ 
						$zone=$x_value;
					}
					if( $x == "md_top_shot"){ 
						$md_top_shot=$x_value;
					}
					if( $x == "tvd_avg_shot"){ 
						$tvd_avg_shot=$x_value;
					}
					if( $x == "interval_type"){ 
						$interval_type=$x_value;
					}
					if( $x == "charge_size"){ 
						$charge_size=$x_value;
					}
					if( $x == "mud_weight"){ 
						$mud_weight=$x_value;
					}
					if( $x == "Initial Pressure"){ 
						$initial_pressure=$x_value;
					}
					if( $x == "ISIP"){ 
						$ISIP=$x_value;
					}
					if( $x == "FSIP"){ 
						$FSIP=$x_value;
					}
					if( $x == "Final Pressure"){ 
						$final_pressure=$x_value;
					}
					if( $x == "Avg Pressure"){ 
						$average_pressure=$x_value;
					}
					if( $x == "Max Pressure"){ 
						$max_pressure=$x_value;
					}
					if( $x == "Main_Body"){ 
						$main_body=$x_value;
					}
					if( $x == "top_interval"){ 
						$top_interval=$x_value;
					}
					if( $x == "bottom_interval"){ 
						$bottom_interval=$x_value;
					}
					if( $x == "Normal Gradient"){ 
						$normal_gradient=$x_value;
					}
					if( $x == "Overburden Gradient"){ 
						$overburden_gradient=$x_value;
					}
					if( $x == "drilling_pressure"){ 
						$drilling_pressure=$x_value;
					}
					if( $x == "Petra Pressure"){ 
						$petra_pressure=$x_value;
					}
					if( $x == "Petra Min Pressure"){ 
						$petra_min_pressure=$x_value;
					}
					if( $x == "Petra Max Pressure"){ 
						$petra_max_pressure=$x_value;
					}
				
				}
				/*
				if ($i==$d1_array_length-1){
					echo "[".$tvd.",".$break_pressure.", ".$bottom_interval.", ".$top_interval.", ".$frac_gradient_initial.", ".$frac_gradient_final.", '".$zone."', ".$tvd_avg_shot.", '".$interval_type."', ".$initial_pressure.", ".$ISIP.", ".$FSIP.", ".$final_pressure.", ".$average_pressure.", ".$max_pressure.", ".$main_body.", ".$normal_gradient.", ".$overburden_gradient.", ".$mud_pressure.",]
			";
					
				} else {*/
					echo "[".$tvd.",";
					if (isset($_GET["break_pressure"])) {
					echo $break_pressure.", ";
					} if (isset($_GET["bottom_frac_interval"])) {
					echo $bottom_interval.", ";
					} if (isset($_GET["top_frac_interval"])) {
					echo $top_interval.", ";
					} if (isset($_GET["frac_gradient_initial"])) {
					echo $frac_gradient_initial.", ";
					} if (isset($_GET["frac_gradient_final"])) {
					echo $frac_gradient_final.", ";
					} if (isset($_GET["perfs"])) {
					echo $tvd_avg_shot.", '".$interval_type."', ";
					} if (isset($_GET["initial_pressure"])) {
					echo $initial_pressure.", ";
					} if (isset($_GET["ISIP"])) {
					echo $ISIP.", ";
					} if (isset($_GET["FSIP"])) {
					echo $FSIP.", ";
					} if (isset($_GET["final_pressure"])) {
					echo $final_pressure.", ";
					} if (isset($_GET["average_pressure"])) {
					echo $average_pressure.", ";
					} if (isset($_GET["max_pressure"])) {
					echo $max_pressure.", ".$main_body.", ";
					} if (isset($_GET["normal_gradient"])) {
					echo $normal_gradient.", '".$zone."', ";
					} if (isset($_GET["overburden_gradient"])) {
					echo $overburden_gradient.", ";
					} if (isset($_GET["drilling_pressure"])) {
					echo $drilling_pressure.",";
					} if (isset($_GET["petra_pressure"])) {
					echo $petra_pressure.",";
					} if (isset($_GET["petra_min_pressure"])) {
					echo $petra_min_pressure.",";
					} if (isset($_GET["petra_max_pressure"])) {
					echo $petra_max_pressure.",";
					}
					echo "],
					";
				
				$i++;
			}
			?>	
			
		]);

			var options = {
				 title: 'Pressure vs. Depth (vertical)',
				annotations: {
									slantedText: true,  //not working
									slantedTextAngle: 90,  // not working
									/*boxStyle: {
									  stroke: '#888',           // Color of the box outline.
									  strokeWidth: 1,           // Thickness of the box outline.
									  rx: 15,                   // x-radius of the corner curvature.
									  ry: 15,                   // y-radius of the corner curvature.
									  gradient: {               // Attributes for linear gradient fill.
										color1: '#fbf6a7',      // Start color for gradient.
										color2: '#33b679',      // Finish color for gradient.
										x1: '0%', y1: '0%',     // Where on the boundary to start and end the
										x2: '100%', y2: '100%', // color1/color2 gradient, relative to the
																// upper left corner of the boundary.
										useObjectBoundingBoxUnits: true // If true, the boundary for x1, y1,
																		// x2, and y2 is the box. If false,
																		// it's the entire chart.
									  }
									}*/
								  },
				pointShape: 'circle',
				pointSize: '5',
				<?php
				if (isset($_GET["smoothing"])) {
				echo "curveType: 'function',";
				}
				?>
				//curveType: 'function',
				orientation: 'vertical',
				legend: { 
					//position: 'in',
					textStyle: {
						fontSize: 14,			
									}							
							},
				vAxes: {0: {title: 'TVD', logScale: false, direction: -1, 
								<?php
								if (isset($_GET["drilling_pressure"])) {
								echo "
								viewWindow:{
									min:".$top_limit.",
									max:".$bottom_limit."
								  }";
								  }
								  ?>
							  },
							1: {title: 'Gradient (psi/ft)',
								logScale: false, 
								maxValue: 1,
								slantedText: true,
								slantedTextAngle: 90
								}},
				//hAxis: {
				//    title: "Pressure (psi)", format:  '#,###'
				//},
				hAxes: {0: {title: 'Pressure (psi)', logScale: false},
					1: {title: 'Gradient (psi/ft)', 
						logScale: false, 
						maxValue: 1
						}},
				seriesType: "line",
				
				<?php 
				echo "series: {";
				$j = 0;
				if (isset($_GET["break_pressure"])) {
				echo 
					$j.": { //break pressure
						type: 'line',
						interpolateNulls: 'true',
						targetAxisIndex:0,
						pointShape: 'polygon',
						pointSize: 20,
					},";
					$j++;
				}
				if (isset($_GET["bottom_frac_interval"])) {
				echo 
					$j.": { //Bottom Frac Interval
						type: 'line',
						interpolateNulls: 'true',
						targetAxisIndex:0
					},";
						$j++;
				}
				if (isset($_GET["top_frac_interval"])) {
				echo 
					$j.": {  //Top Frac Interval
						type: 'line',
						interpolateNulls: 'true',
						targetAxisIndex:0
					},";
					$j++;
				}
				if (isset($_GET["frac_gradient_initial"])) {
				echo 
					$j.": {//Frac Gradient Initial
						type: 'line',
						//interpolateNulls: 'true',
						lineDashStyle: [2, 3],
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						pointShape: 'square',
						pointSize: 10,
					},";
					$j++;
				}
				if (isset($_GET["frac_gradient_final"])) {
				echo 
					$j.": { //Frac Gradient Final
						type: 'line',
						//interpolateNulls: 'true',
						lineDashStyle: [2, 3],
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						pointShape: 'square',
						pointSize: 10,
					},";
					$j++;
				}
				if (isset($_GET["perfs"])) {
				echo 
					$j.": {  ///tvd_avg_perf_shot
						type: 'line',
						lineWidth: 1,
						lineDashStyle: [1, 3],
						interpolateNulls: 'true',
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90
					},";
					$j++;
				}
				if (isset($_GET["initial_pressure"])) {
				echo 
					$j.": { //initial_pressure
						type: 'line',
						interpolateNulls: 'true',
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						pointShape: 'triangle',
						pointSize: 20,
					},";
					$j++;
				}
				if (isset($_GET["ISIP"])) {
				echo 
					$j.": {  //ISIP
						type: 'line',
						lineWidth: 3,
						interpolateNulls: 'true',
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						pointShape: 'square',
						pointSize: 20,
					},";
					$j++;
				}
				if (isset($_GET["FSIP"])) {
				echo 
					$j.": {  //FSIP
						type: 'line',
						lineWidth: 3,
						interpolateNulls: 'true',
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						pointShape: 'square',
						pointSize: 20,
					},";
					$j++;
				}
				if (isset($_GET["final_pressure"])) {
				echo 
					$j.": {  //final_pressure
						type: 'line',
						lineWidth: 3,
						interpolateNulls: 'true',
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						pointShape: 'triangle',
						pointSize: 20,
					},";
					$j++;
				}
				if (isset($_GET["average_pressure"])) {
				echo 
					$j.": {  //average_pressure
						type: 'line',
						lineWidth: 3,
						interpolateNulls: 'true',
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						pointShape: 'triangle',
						pointSize: 20,
					},";
					$j++;
				}
				if (isset($_GET["max_pressure"])) {
				echo 
					$j.": {  //max_pressure
						type: 'line',
						lineWidth: 3,
						interpolateNulls: 'true',
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						pointShape: 'triangle',
						pointSize: 20,
					},";
					$j++;
				}
				if (isset($_GET["normal_gradient"])) {
				echo 
					$j.": {  //normal_gradient
						type: 'line',
						lineWidth: 3,
						//interpolateNulls: 'true',
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						pointShape: 'triangle',
						pointSize: 20,
						//color: 'orange',
					},";
					$j++;
				}
				if (isset($_GET["overburden_gradient"])) {
				echo 
					$j.": {  //overburden_gradient
						type: 'line',
						lineWidth: 2,
						//interpolateNulls: 'true',
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						pointShape: 'triangle',
						pointSize: 20,
					},";
					$j++;
				}
				if (isset($_GET["drilling_pressure"])) {
				echo 
					$j.": {  //mud_pressure
						type: 'line',
						lineWidth: 2,
						//interpolateNulls: 'true',
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						//pointShape: 'star',
						pointSize: 10,
					},";
					$j++;
				}
				if (isset($_GET["petra_pressure"])) {
				echo 
					$j.": {  //petra_pressure
						type: 'line',
						lineWidth: 2,
						//interpolateNulls: 'true',
						//targetAxisIndex:0,
						//slantedText: true,
						//slantedTextAngle: 90,
						pointShape: 'star',
						pointSize: 25,
						color: 'black',
					},";
					$j++;
				}
				if (isset($_GET["petra_min_pressure"])) {
				echo 
					$j.": {  //petra_min_pressure
						type: 'line',
						lineWidth: 2,
						//interpolateNulls: 'true',
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						//pointShape: 'circle',
						pointSize: 10,
						color: 'red',
					},";
					$j++;
				}
				if (isset($_GET["petra_max_pressure"])) {
				echo 
					$j.": {  //petra_max_pressure
						type: 'line',
						lineWidth: 2,
						//interpolateNulls: 'true',
						targetAxisIndex:0,
						slantedText: true,
						slantedTextAngle: 90,
						//pointShape: 'circle',
						pointSize: 10,
					},";
					$j++;
				}
				
				?>
				
				}
			};

			var chart = new google.visualization.LineChart(document.getElementById('chart_div3'));

			chart.draw(data, options);
		}
    </script>
	
<!--  
_______________________________________________________________________________________________________________________________
javascript to create a production curve 
-->
<script type="text/javascript">
    
    // Load the Visualization API and the corechart package.
    google.load("visualization", "1", {packages:["corechart"]});
	
		// Set a callback to run when the Google Visualization API is loaded.
		google.setOnLoadCallback(drawChart);
		
		function drawChart() {
		var data = new google.visualization.DataTable();
		data.addColumn('number', 'Date'); // Implicit domain label col.
		data.addColumn('number', 'Gas Production Volume'); // Implicit series 1 data col.
		data.addColumn('number', 'Oil Production Volume'); // Implicit series 1 data col.
		
		data.addRows([
		
			<?php 
			
			//set $i to 0
			$i =  0;
			
			//Length of the array
			$d1_array_length = count($Production_array);
			//echo $d1_array_length;
			$initial_date = null;
			$L = 0;
			
			foreach ($Production_array as $array) {
				
				//set all the values to null
				$date = null;
				$gas_production_volume = null;
				$oil_production_volume= null;
				
//  $d1_array[] = array('tvd' => interpolate_tvd($average_depth, $surveys_array_filtered), 'break pressure' => $break_pressure, 'Frac Gradient Initial' => $frac_gradient_initial, 'Frac Gradient Final' => $frac_gradient_final, 'zone' => $zone, 'Initial Pressure' => $initial_pressure, 'ISIP' => $initial_shut_in_pressure, 'FSIP' => $final_shut_in_pressure, 'Final Pressure' => $final_pressure, 'Avg Pressure' => $average_pressure, 'Max Pressure' => $Max_Pressure, 'Main_Body' => $Main_Body);
				foreach($array as $x=>$x_value) {
				//echo "Key=" . $x . ", Value=" . $x_value;
				//echo "<br>";
				
					//define the different elements
					if( $x == "date"){ 
						$date=$x_value;
						//$timestamp = strtotime($date);
						$year = substr($date,0,4);
						$month = substr( $date, 4,2);
						$day = substr( $date,-2);
						//echo $year."-".$month."-".$day;
						//echo strtotime('$year."-".$month."-".$day')."<br>";
						$your_date = strtotime($year."-".$month."-".$day);
						//echo $your_date."<br>";
						//echo $date."<br>";
						$date = floor($your_date/(60*60*24));
						//echo $date."<br>";
						
						
						//$date = 365*$year+30*$month+$day;
						//$date = date("s", mktime(0,0,0,$month, $day, $year));
					}
					if( $x == "gas_production_volume"){ 
						$gas_production_volume=$x_value;
					}
					if( $x == "oil_production_volume"){ 
						$oil_production_volume=$x_value;
					}
					
				}
				//echo $your_date."<br>";
				//echo $date."<br>";
				if( $L== 0) {
						$initial_date = $date;
						}
						$date = ($date- $initial_date);
				//echo $initial_date."<br>";
				$L++;
				/*
				if ($i==$d1_array_length-1){
					echo "[".$tvd.",".$break_pressure.", ".$bottom_interval.", ".$top_interval.", ".$frac_gradient_initial.", ".$frac_gradient_final.", '".$zone."', ".$tvd_avg_shot.", '".$interval_type."', ".$initial_pressure.", ".$ISIP.", ".$FSIP.", ".$final_pressure.", ".$average_pressure.", ".$max_pressure.", ".$main_body.", ".$normal_gradient.", ".$overburden_gradient.", ".$mud_pressure.",]
			";
					
				} else {*/
					echo "[".$date.",";
					if (isset($_GET["well_name"])) {
					echo $gas_production_volume.", ";
					} if (isset($_GET["well_name"])) {
					echo $oil_production_volume.", ";
					}
					echo "],
					";
				
				$i++;
			}
			?>	
			
		]);

			var options = {
				 title: 'Production',
				annotations: {
									slantedText: true,  //not working
									slantedTextAngle: 90,  // not working
									/*boxStyle: {
									  stroke: '#888',           // Color of the box outline.
									  strokeWidth: 1,           // Thickness of the box outline.
									  rx: 15,                   // x-radius of the corner curvature.
									  ry: 15,                   // y-radius of the corner curvature.
									  gradient: {               // Attributes for linear gradient fill.
										color1: '#fbf6a7',      // Start color for gradient.
										color2: '#33b679',      // Finish color for gradient.
										x1: '0%', y1: '0%',     // Where on the boundary to start and end the
										x2: '100%', y2: '100%', // color1/color2 gradient, relative to the
																// upper left corner of the boundary.
										useObjectBoundingBoxUnits: true // If true, the boundary for x1, y1,
																		// x2, and y2 is the box. If false,
																		// it's the entire chart.
									  }
									}*/
								  },
				pointShape: 'circle',
				pointSize: '5',
				//curveType: 'function',
				//orientation: 'vertical',
				//legend: { 
				//	position: 'in',
				//	textStyle: {
				//		fontSize: 10,			
				//					}							
				//			},
				vAxis: {
					title: 'Volume', 
					},
				hAxis: {
				    title: "Days", 
					//logScale: 'true',
					//format:  "####",
				},
				//hAxes: {0: {title: 'Date', format: 'YYYYMMdd'},
				//	1: {title: 'Gradient (psi/ft)', 
				//		logScale: false, 
				//		maxValue: 1
				//		}},
				seriesType: "line",
				<?php
					if (isset($_GET["trendlines"])) {
						echo "
						trendlines: {
						  0: {
							type: 'exponential',
							//degree: 2,
							visibleInLegend: true,
							showR2: true,
						  },
						  1: {
							type: 'exponential',
							//degree: 2,
							visibleInLegend: true,
							showR2: true,
						  },
						}"; 
					}
				?>
				
			}
		

			var chart = new google.visualization.ScatterChart(document.getElementById('chart_div2'));

			chart.draw(data, options);
		};	
    </script>	
	
	
	<!--  
	_______________________________________________________________________________________________________________________________
	create a google maps tool
	-->
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false">
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
	<script src="/MDtoTVD/arcgislink_compressed.js" type="text/javascript"></script>

    <script>
		// Creates Markers for wellhead and bottomhole position and loads a arcgis map whenever the map is moved

		function initialize() {
		  var mapOptions = {
			zoom: 16,
			center: new google.maps.LatLng(<?php echo $Surface_lat.", ".$Surface_lon;?>),
			//center: new google.maps.LatLng(39.44, -108.06)
			mapTypeId: google.maps.MapTypeId.SATELLITE
		  }
		  
		  var map= new google.maps.Map(document.getElementById('map-canvas'),
										mapOptions);

		  setMarkers(map, beaches);
		
		// Load Chris Wold's ESRI ArcGIS Mapserver for well details
		//var ctaLayer = new google.maps.KmlLayer('http://wpxdnvdsde01:6080/arcgis/services/PetraWells/MapServer/KmlServer?Composite=false&LayerIDs=2');
		//ctaLayer.setMap(map);
		var url = 'http://wpxdnvdsde01:6080/arcgis/rest/services/PetraWells/MapServer';
	    var dynamap = new gmaps.ags.MapOverlay(url);//, { opacity: 0.5 });
	    dynamap.setMap(map)
		
		map.panTo(<?php echo $Surface_lat-"1"." ".$Surface_lon;?>)
		}
		
		//google.maps.event.addDomListener(window, 'load', initialize);
		//map.panTo(<?php echo $Surface_lat-"1".", ".$Surface_lon;?>)

		/**
		 * Data for the markers consisting of a name, a LatLng and a zIndex for
		 * the order in which these markers should display on top of each
		 * other.
		 */
		var beaches = [
		  ['<?php echo $WELL_NAME." Bottom Hole";?>', <?php echo $Bottom_lat.", ".$Bottom_lon;?>, 1],
		  ['<?php echo $WELL_NAME." Wellhead";?>', <?php echo $Surface_lat.", ".$Surface_lon;?>, 1],

		];

		function setMarkers(map, locations) {
		  // Add markers to the map

		  // Marker sizes are expressed as a Size of X,Y
		  // where the origin of the image (0,0) is located
		  // in the top left of the image.

		  // Origins, anchor positions and coordinates of the marker
		  // increase in the X direction to the right and in
		  // the Y direction down.
		  var image = {
			url: 'images/oil.png',
			// This marker is 20 pixels wide by 32 pixels tall.
			size: new google.maps.Size(32, 37),
			// The origin for this image is 0,0.
			origin: new google.maps.Point(0,0),
			// The anchor for this image is the base of the flagpole at 0,16.
			anchor: new google.maps.Point(0, 16)
		  };
		  // Shapes define the clickable region of the icon.
		  // The type defines an HTML &lt;area&gt; element 'poly' which
		  // traces out a polygon as a series of X,Y points. The final
		  // coordinate closes the poly by connecting to the first
		  // coordinate.
		  var shape = {
			  coords: [1, 1, 1, 20, 18, 20, 18 , 1],
			  type: 'poly'
		  };
		  for (var i = 0; i < locations.length; i++) {
			var beach = locations[i];
			var myLatLng = new google.maps.LatLng(beach[1], beach[2]);
			var marker = new google.maps.Marker({
				position: myLatLng,
				map: map,
				icon: image,
				animation: google.maps.Animation.DROP,
				shape: shape,
				title: beach[0],
				zIndex: beach[3]
				//labelContent: beach[0],
				//labelAnchor: new google.maps.Point(32, 0),
				//labelClass: "labels", // the CSS class for the label
			});
		  }
		  
		  google.maps.event.addListener(marker, 'click', toggleBounce);
		}
		
		function toggleBounce() {

		  if (marker.getAnimation() != null) {
			marker.setAnimation(null);
		  } else {
			marker.setAnimation(google.maps.Animation.BOUNCE);
			var latLng = new google.maps.LatLng(<?php echo $Surface_lat-"1".", ".$Surface_lon;?>); //Makes a latlng
			map.panTo(latLng); //Make map global
		  }
		}

		google.maps.event.addDomListener(window, 'load', initialize);

    </script>
	
  </head>

  <body>
  <!--
  <script>
  //analytics code
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-56518760-1', 'auto');
  ga('send', 'pageview');

</script>
-->

    <!--Div that will hold the chart-->
	<table style="width:100%">
		<tr>
			<td width="50%"> 
			<form action="PressureVsTVD.php" method="get">
	Well Name: <input type="text" name="well_name" value="<?php echo $_GET["well_name"] ?>"><br>
	<?php
				if (isset($_GET["debug"])) {
				echo "<input type='checkbox' name='debug' value='true' checked>debug mode, ";
				} else {
				echo  "<input type='checkbox' name='debug' value='true'>debug mode, ";
				}
				if (isset($_GET["smoothing"])) {
				echo "<input type='checkbox' name='smoothing' value='true' checked>Smooth Curves,";
				} else {
				echo  "<input type='checkbox' name='smoothing' value='true'>Smooth Curves,";
				}
				if (isset($_GET["trendlines"])) {
				echo "<input type='checkbox' name='trendlines' value='true' checked>Production Trendlines, <br>";
				} else {
				echo  "<input type='checkbox' name='trendlines' value='true'>Production Trendlines, <br>";
				}
				/*
				if (isset($_GET["check_all"])) {
				echo "<input type='checkbox' name='check_all' value='true' checked>Check All  (will overide other selections), ";
				} else {
				echo  "<input type='checkbox' name='check_all' value='true'>Check All  (will overide other selections), ";
				}
				if (isset($_GET["check_none"])) {
				echo "<input type='checkbox' name='check_all' value='true' checked>Check None, (will overide other selections)<br>";
				} else {
				echo  "<input type='checkbox' name='check_all' value='true'>Check None, (will overide other selections)<br>";
				}
				*/
				if (isset($_GET["break_pressure"])) {
				echo "<input type='checkbox' name='break_pressure' value='true' checked>Break Pressure, <br>";
				} else {
				echo  "<input type='checkbox' name='break_pressure' value='true'>Break Pressure, <br>";
				}
				if (isset($_GET["bottom_frac_interval"])) {
				echo "<input type='checkbox' name='bottom_frac_interval' value='true' checked>Bottom Frac Interval, <br>";
				} else {
				echo  "<input type='checkbox' name='bottom_frac_interval' value='true'>Bottom Frac Interval, <br>";
				}
				if (isset($_GET["top_frac_interval"])) {
				echo "<input type='checkbox' name='top_frac_interval' value='true' checked>Top Frac Interval,<br>";
				} else {
				echo  "<input type='checkbox' name='top_frac_interval' value='true'>Top Frac Interval, <br>";
				}
				if (isset($_GET["frac_gradient_initial"])) {
				echo "<input type='checkbox' name='frac_gradient_initial' value='true' checked>Frac Gradient Intial, <br>";
				} else {
				echo  "<input type='checkbox' name='frac_gradient_initial' value='true'>Frac Gradient Initial, <br>";
				}
				if (isset($_GET["frac_gradient_final"])) {
				echo "<input type='checkbox' name='frac_gradient_final' value='true' checked>Frac Gradient Final, <br>";
				} else {
				echo  "<input type='checkbox' name='frac_gradient_final' value='true'>Frac Gradient Final, <br>";
				}
				if (isset($_GET["perfs"])) {
				echo "<input type='checkbox' name='perfs' value='true' checked>Perfs, <br>";
				} else {
				echo  "<input type='checkbox' name='perfs' value='true'>Perfs,<br>";
				}
				if (isset($_GET["ISIP"])) {
				echo "<input type='checkbox' name='ISIP' value='true' checked>ISIP,<br>";
				} else {
				echo  "<input type='checkbox' name='ISIP' value='true'>ISIP,<br>";
				}
				if (isset($_GET["FSIP"])) {
				echo "<input type='checkbox' name='FSIP' value='true' checked>FSIP,<br>";
				} else {
				echo  "<input type='checkbox' name='FSIP' value='true'>FSIP, <br>";
				}
				if (isset($_GET["initial_pressure"])) {
				echo "<input type='checkbox' name='initial_pressure' value='true' checked>Initial Pressure (wellhead), <br>";
				} else {
				echo  "<input type='checkbox' name='initial_pressure' value='true'>Initial Pressure (wellhead), <br>";
				}
				if (isset($_GET["final_pressure"])) {
				echo "<input type='checkbox' name='final_pressure' value='true' checked>Final Pressure (wellhead), <br>";
				} else {
				echo  "<input type='checkbox' name='final_pressure' value='true'>Final Pressure (wellhead),<br>";
				}
				if (isset($_GET["average_pressure"])) {
				echo "<input type='checkbox' name='average_pressure' value='true' checked>Average Pressure (wellhead),<br>";
				} else {
				echo  "<input type='checkbox' name='average_pressure' value='true'>Average Pressure (wellhead), <br>";
				}
				if (isset($_GET["max_pressure"])) {
				echo "<input type='checkbox' name='max_pressure' value='true' checked>Max Pressure (wellhead), <br>";
				} else {
				echo  "<input type='checkbox' name='max_pressure' value='true'>Max Pressure (wellhead),<br>";
				}
				if (isset($_GET["normal_gradient"])) {
				echo "<input type='checkbox' name='normal_gradient' value='true' checked>Normal Gradient, <br>";
				} else {
				echo  "<input type='checkbox' name='normal_gradient' value='true'>Normal Gradient, <br>";
				}
				if (isset($_GET["overburden_gradient"])) {
				echo "<input type='checkbox' name='overburden_gradient' value='true' checked>Overburden Gradient, <br>";
				} else {
				echo  "<input type='checkbox' name='overburden_gradient' value='true'>Overburden Gradient, <br>";
				}
				if (isset($_GET["drilling_pressure"])) {
				echo "<input type='checkbox' name='drilling_pressure' value='true' checked>Drilling (Mud) Pressure, <br>";
				} else {
				echo  "<input type='checkbox' name='drilling_pressure' value='true'>Drilling (Mud) Pressure, <br>";
				}
				if (isset($_GET["petra_pressure"])) {
				echo "<input type='checkbox' name='petra_pressure' value='true' checked>Petra Pressure Model,<br>";
				} else {
				echo  "<input type='checkbox' name='petra_pressure' value='true'>Petra Pressure Model, <br>";
				}
				/*
				if (isset($_GET["petra_min_pressure"])) {
				echo "<input type='checkbox' name='petra_min_pressure' value='true' checked>Petra Min Pressure, <br>";
				} else {
				echo  "<input type='checkbox' name='petra_min_pressure' value='true'>Petra Min Pressure, <br>";
				}
				if (isset($_GET["petra_max_pressure"])) {
				echo "<input type='checkbox' name='petra_max_pressure' value='true' checked>Petra Max Pressure, <br>";
				} else {
				echo  "<input type='checkbox' name='petra_max_pressure' value='true'>Petra Max Pressure, <br>";
				}
				*/
				?>
	<!--<input type="checkbox" name="debug" value="true">debug mode, <input type="checkbox" name="smoothing" value="true">Smooth Curves, <br><input type="checkbox" name="break_pressure" value="true" checked>break pressure, <input type="checkbox" name="bottom_frac_interval" value="true"checked>Bottom Frac Interval, <input type="checkbox" name="top_frac_interval" value="true"checked>Top Frac Interval, <input type="checkbox" name="frac_gradient_initial" value="true"checked>Frac Gradient Initial, <input type="checkbox" name="frac_gradient_final" value="true"checked>Frac Gradient Final, <input type="checkbox" name="perfs" value="true"checked>perfs, <input type="checkbox" name="initial_pressure" value="true"checked>initial pressure, <input type="checkbox" name="ISIP" value="true"checked>ISIP, <input type="checkbox" name="FSIP" value="true"checked>FSIP, <input type="checkbox" name="final_pressure" value="true"checked>Final Pressure, <input type="checkbox" name="average_pressure" value="true"checked>Average Pressure, <input type="checkbox" name="max_pressure" value="true"checked>Max Pressure, <input type="checkbox" name="normal_gradient" value="true"checked>Normal Gradient, <input type="checkbox" name="overburden_gradient" value="true"checked>Overburden Gradient, <input type="checkbox" name="drilling_pressure" value="true"checked>Drilling Pressure, <br>-->
	<!--MD value to interpolate to TVD: <input type="text" name="interpolate_value"><br>-->
	<br><input type="submit">
	</form>
			
			</td>
			<td width="50%"><div align="center" style="height:400px;width:100%" id="map-canvas"></div><br><center><font font-family="open+sans"  align="center" color="black">Move the map to display surrounding well locations!</font></center></td>
		</tr>
		<tr>
			<td width="50%"><div align="left" style="height:900px;width:100%" id="chart_div"></div></td>
			<td width="50%"><div align="right" style="height:900px;width:100%" id="chart_div3"></div></td>
		</tr>
		<tr>
			<td colspan="2" style="width:100%"><div align="center" style="height:900px;width:100%" id="chart_div2"></div></td>
		</tr>
	</table>
	
	<?php
// Save User info usage statistics into mysql database
$servername = "localhost";
$username = "admin";
$password = "password";
$dbname = "appusers";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user = getenv("HOMEPATH");
//echo $user."<br>";
// escape variables for security
$user = str_replace("\Users\\","", $user);
//echo $user."<br>";
$date = date('Y:m:d H:i:s', time() - 28800);

$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

//echo "INSERT INTO users (user, application, url, timestamp)
//VALUES ('$user', 'Pressure Vs. TVD', '$url', '$date')";
$sql = "INSERT INTO users (user, application, url, timestamp)
VALUES ('$user', 'Pressure Vs. TVD', '$url', '$date')";

if (mysqli_query($conn, $sql)) {
//    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>
	
  </body>
</html>

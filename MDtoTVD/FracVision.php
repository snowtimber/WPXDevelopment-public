<html>
	<?php
	//declare arrays for storing gathered data
	$d2_array = array();
	$d1_array = array();
	
	// Load PHP files and build array of data
	require('interpolate_tvd_function.php');
	require('dim_well_request.php');
	require('ow_surveys_query.php');
	//require('interpolate_value.php');
	require('frac_gradient_query.php');
	require('ow_perfs_query.php');
	require('mw_query.php');
	$md_interpolate = $_GET["interpolate_value"];
	
	
	echo "<br><br>".$md_interpolate."<br>";
	
	if (isset($md_interpolate)) {
		echo "<br><br>Interpolated TVD is: ".interpolate_tvd($md_interpolate,$surveys_array_filtered);
	}

//compile d2_array
	$d2_array['d1_array'] = $d1_array;

	$fp = fopen('results.json', 'w');
	fwrite($fp, json_encode($d2_array));
	fclose($fp);
	
	?>
	
  <head>
    <!--Load the AJAX API-->
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
		data.addColumn('number', 'break pressure'); // Implicit series 1 data col.
		data.addColumn('number', 'top_interval');  // interval role col.
		data.addColumn('number', 'bottom_interval');  // interval role col.
		data.addColumn('number', 'Frac Gradient Initial (psi/ft *TVD)'); // Implicit series 2 data col.
		data.addColumn('number', 'Frac Gradient Final (psi/ft *TVD)'); // Implicit series 3 data col.
		data.addColumn({type:'string', role:'annotation'}); // annotation role col. for zone
		//data.addColumn('number', 'md_top_shot'); // Implicit series 4 data col.
		data.addColumn('number', 'tvd_avg_shot'); // Implicit series 5 data col.
		data.addColumn({type:'string', role:'annotationText'}); // annotation role col. for interval_type
		data.addColumn({type:'number', role:'annotationText'}); // annotation role col. for charge size
		data.addColumn('number', 'mud_weight'); // Implicit series 6 data col.
		data.addColumn('number', 'initial_pressure'); // Implicit series 6 data col.
		data.addColumn('number', 'ISIP'); // Implicit series 6 data col.
		data.addColumn('number', 'FSIP'); // Implicit series 6 data col.
		data.addColumn('number', 'final_pressure'); // Implicit series 6 data col.
		data.addColumn('number', 'average_pressure'); // Implicit series 6 data col.
		data.addColumn('number', 'max_pressure'); // Implicit series 6 data col.
		data.addColumn({type:'number', role:'annotationText'}); // annotation role col. for main body
		
		
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
				
				}
				if ($i==$d1_array_length-1){
					echo "[".$tvd.", ".$break_pressure.", ".$bottom_interval.", ".$top_interval.", ".$frac_gradient_initial.", ".$frac_gradient_final.", '".$zone."', ".$tvd_avg_shot.", '".$interval_type."', ".$charge_size.", ".$mud_weight.", ".$initial_pressure.", ".$ISIP.", ".$FSIP.", ".$final_pressure.", ".$average_pressure.", ".$max_pressure.", ".$main_body.",]
			";
					
				} else {
					echo "[".$tvd.", ".$break_pressure.", ".$bottom_interval.", ".$top_interval.", ".$frac_gradient_initial.", ".$frac_gradient_final.", '".$zone."', ".$tvd_avg_shot.", '".$interval_type."', ".$charge_size.", ".$mud_weight.", ".$initial_pressure.", ".$ISIP.", ".$FSIP.", ".$final_pressure.", ".$average_pressure.", ".$max_pressure.", ".$main_body.",],
			";
				}
				$i++;
			}
			?>		
		]);

			var options = {
				title: 'Pressure vs. Depth',
				pointShape: 'circle',
				pointSize: '10',
				curveType: 'function',
				//orientation: 'vertical',
				vAxis: { title: "pressure (psi)", format:  '#,###', direction: -1},
				hAxes:[
				{title:'TVD',textStyle:{color: 'black'}} // Nothing specified for axis 0
				//{title:'Percentage',textStyle:{color: 'red'}, maxValue: 1, minValue: 0} // Axis 1
				]
				//series: [
				//0:{ type: "line", targetAxisIndex: 0 },
				//1: { type: "line", targetAxisIndex: 1}
				//]
				//series: {2: {type: "line", hAxis.maxValue: 1, hAxis.minValue: 0}}
			};

			var chart = new google.visualization.LineChart(document.getElementById('chart_div'));

			chart.draw(data, options);
		}
    </script>
  </head>

  <body>
  
	<form action="well_lookup_to_surveys.php" method="get">
	Well Name: <input type="text" name="well_name"><br>
	MD value to interpolate to TVD: <input type="text" name="interpolate_value"><br>
	<input type="submit">
	</form>

    <!--Div that will hold the chart-->
    <div style="height:800px" id="chart_div"></div>
  </body>
</html>

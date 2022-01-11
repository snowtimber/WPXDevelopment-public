<script type="text/javascript">
    
    // Load the Visualization API and the corechart package.
    google.load("visualization", "1", {packages:["corechart"]});
	
		// Set a callback to run when the Google Visualization API is loaded.
		google.setOnLoadCallback(drawChart);
		
		function drawChart() {
		var data = new google.visualization.DataTable();
		data.addColumn('number', 'Date'); // Implicit domain label col.
		//data.addColumn('number', 'Gas Production Volume'); // Implicit series 1 data col.
		//data.addColumn('number', 'Oil Production Volume'); // Implicit series 1 data col.
		
		//need to make for each element of array
		<?php
		foreach ($wellname_array as $wellname_element) {
		echo "data.addColumn('number', 'Gas - ".$wellname_element."'); // implicit series 1 data col
		";
		echo "data.addColumn('number', 'Oil - ".$wellname_element."'); // Implicit series 1 data col";
		echo "
		";
		}
		?>
		
		//data.addColumn('number', 'Gas - $wellname'); // Implicit series 1 data col.
		//data.addColumn('number', 'Oil - $wellname'); // Implicit series 1 data col.
		
		//need to make summary
		//data.addColumn('number', 'Gas - average'); // Implicit series 1 data col.
		//data.addColumn('number', 'Oil - average'); // Implicit series 1 data col.
		
		data.addRows([
		
			<?php 
			
			//set $i to 0
			$i =  0;
			
			$L = 0;
			
			foreach ($Normalized_Production_array as $array) {
				
				//set all the values to null
				$date = null;
				$gas_production_volume = null;
				$oil_production_volume= null;
				$well_name= null;
				
				foreach($array as $x=>$x_value) {
				//echo "Key=" . $x . ", Value=" . $x_value;
				//echo "<br>";
				
					//define the different elements
					if( $x == "date"){ 
						$date=round($x_value*30.4375,0);
					}
					if( $x == "wellname"){ 
						$well_name=$x_value;
					}
					if( $x == "gas_production_volume"){ 
						$gas_production_volume=$x_value;
					}
					if( $x == "oil_production_volume"){ 
						$oil_production_volume=$x_value;
					}
					
				}

				//specify different columns depending on source well
				// foreach array of wellname as wells
				$m = 0;
				foreach ($wellname_array as $wellname_element) {
					$m++;
					if ($wellname_element == $well_name) {
						
						echo "[".$date.str_repeat(", ",$m*2-1);
						echo $gas_production_volume.", ";
						echo $oil_production_volume;
						echo str_repeat(", ",2*count($wellname_array)-$m*2+1);
						echo "],
						";
					}
				}
				/*
				echo "[".$date.",";
				echo $gas_production_volume.", ";
				echo $oil_production_volume.", ";
				echo "],
				";
				*/
				
				$i++;
			}
			?>	
			
		]);

			var options = {
				 title: 'Cumulative Production',
				annotations: {
									//slantedText: true,  //not working
									//slantedTextAngle: 90,  // not working
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
				pointSize: '1',
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
				//interpolateNulls: "true",
				<?php
					//if (isset($_GET["trendlines"])) {
						/*
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
					//}
					*/
				?>
				
			}
		

			var chart = new google.visualization.LineChart(document.getElementById('chart_div2'));

			chart.draw(data, options);
		};	
    </script>

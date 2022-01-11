<script type="text/javascript">
    
    // Load the Visualization API and the corechart package.
    google.load("visualization", "1", {packages:["corechart"]});
	
		// Set a callback to run when the Google Visualization API is loaded.
		google.setOnLoadCallback(drawChart);
		
		function drawChart() {
		var data = new google.visualization.DataTable();
		data.addColumn('number', 'Actual Cost'); // Implicit domain label col.
		data.addColumn('number', 'Production Curve Percentage'); // Implicit series 1 data col.
		data.addColumn({type:'string', role:'annotation'}); // annotation role col. for wellname
		data.addColumn({type:'string', role:'annotationText'}); // annotation role col. for wellname
		//data.addColumn('number', 'Oil Production Volume'); // Implicit series 1 data col.
		
		//data.addColumn('number', 'Gas - $wellname'); // Implicit series 1 data col.
		//data.addColumn('number', 'Oil - $wellname'); // Implicit series 1 data col.
		
		//need to make summary
		//data.addColumn('number', 'Gas - average'); // Implicit series 1 data col.
		//data.addColumn('number', 'Oil - average'); // Implicit series 1 data col.
		
		data.addRows([
		
			<?php 
			
			for ($row = 0; $row < count($wellname_array)-1; $row++) {
			
				if ( $cost_array[$row] > 0 && $PCP_array[$row] >0) {
				echo "[".$cost_array[$row].", ";
				echo $PCP_array[$row].",";
				echo "'".$wellname_array[$row]."', '".$wellname_array[$row]."'";
				echo "],
				";
				/*
				echo "[".$date.",";
				echo $gas_production_volume.", ";
				echo $oil_production_volume.", ";
				echo "],
				";
				*/
				}
			}
			?>	
			
		]);

			var options = {
				title: 'Actual Cost vs. Relative Well Performance',
				annotations: {
					textStyle: {
					  fontName: 'open sans',
					  fontSize: 14,
					  //bold: true,
					  //italic: true,
					  //color: '#871b47',     // The color of the text.
					  //auraColor: '#d799ae', // The color of the text outline.
					  opacity: 0.8          // The transparency of the text.
					}
				  },
				pointShape: 'circle',
				pointSize: '4',
				//curveType: 'function',
				//orientation: 'vertical',
				//legend: { 
				//	position: 'in',
				//	textStyle: {
				//		fontSize: 10,			
				//					}							
				//			},
				vAxis: {
					title: 'Relative Well Performance', 
					},
				hAxis: {
				    title: "Cost", 
					//logScale: 'true',
					//format:  "####",
				},
				//hAxes: {0: {title: 'Date', format: 'YYYYMMdd'},
				//	1: {title: 'Gradient (psi/ft)', 
				//		logScale: false, 
				//		maxValue: 1
				//		}},
				seriesType: "line",
				lineWidth: 0,
				trendlines: {
				  0: {
					type: 'linear',
					visibleInLegend: true,
					visibleInLegend: true,
					showR2: true,
				  }
				}
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
		

			var chart = new google.visualization.LineChart(document.getElementById('chart_CostvsPCP'));

			chart.draw(data, options);
		};	
    </script>

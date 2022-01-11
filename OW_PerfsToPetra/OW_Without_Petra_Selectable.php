<!doctype html>

<?php
//get list of OW_not _Petra wells and put the uniques into an array to be used in a selectable list
$file = 'OW_not_Petra.txt';
//get lines from the file
$lines = file($file);

$lineNumber = 1;
foreach($lines as $line) {
	$values_array = explode(',',$line);
	//put the well names in an array
	if(strpos($line,',') !== false){
		$well_names[] = $values_array[1];
		//echo $values_array[1]."<br>";
		$wsn_array[] = $values_array[0];
		//echo $values_array[0]."<br><br>";
	}
	$lineNumber++;
}
$unique_wells = array_unique($well_names);
$unique_wsns = array_unique($wsn_array);
/*
print_r ($unique_wells);
echo "<br>";
print_r ($unique_wsns);
echo "<br><br>";
*/

//create array with wsn keys like 1,2,3,4 ect.
foreach($unique_wsns as $wsn) {
	$wsn_unique_incremental[] = $wsn;
}



?>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Perfs in OW not transferred to Petra</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
 
  <style>
  #feedback { font-size: 1.4em; }
  #selectable .ui-selecting { background: #FECA40; }
  #selectable .ui-selected { background: #F39814; color: white; }
  #selectable { list-style-type: none; margin: 0; padding: 0; width: 20%; }
  #selectable li { margin: 3px; padding: 0.4em; font-size: 1.1em; height: 12px; }
  </style>
  <script>
  //this will display the index of selected items
  $(function() {
    $( "#selectable" ).selectable({
      stop: function() {
        var result = $( "#select-result" ).empty();
		var names = $( "#select-result" ).empty();
        $( ".ui-selected", this ).each(function() {
          var index = $( "#selectable li" ).index( this );
          //result.append( " #" + ( index + 1 ) );
		  var name = $( this ).attr('name');
          names.append( " " + ( name + ", " ) );
        });
      }
    });
  });
  </script>
  <script>
$(document).ready(function(){
    $( "#selectable" ).selectable({
		selected: function() {
			var wsn = $( ".ui-selected", this ).attr('wsn')
			var src = 'conflicting_select_with_formatting_OW_only.php?wsn='+wsn;
			var iframe = '<iframe id="selected-results" width=100% height=100% frameborder="0" src="'+src+'" allowfullscreen></iframe>';
			$("#div1").html(iframe);
			return false;
		}
		/*selected: function() {
			$.ajax({url: "conflicting_select_with_formatting.php?wsn=" +$( ".ui-selected", this ).attr('wsn'), success: function(result){
				$("#div1").html(result);
			}});
		}*/
    });
});
</script>
</head>
<body>
<table style="width: 100%">
	<tr>
		<td>
			<form action="http://10.33.10.146/WPXDevelopment/OW_PerfsToPetra/OW_Without_Petra_Selectable.php">
				<input type="submit" value="2. Perfs in OW not yet transferred to PETRA">
			</form>
		</td>
		<td>
			<form action="http://10.33.10.146/WPXDevelopment/OW_PerfsToPetra/conflicting_wells.php">
				<input type="submit" value="3. Conflicting Perfs in OW & PETRA">
			</form>
		</td>
		<td>
			<form action="http://10.33.10.146/WPXDevelopment/OW_PerfsToPetra/DVcompare_depths_WPX_only.php">
				<input type="submit" value="1. Run Full Perf Analysis (takes about 2 hours)">
			</form>
		</td>
	</tr>
</table>
<br>
 
 <div style="float:left;display:inline;width:100%;">
<p id="feedback">
<span>The below wells have Perfs in OW, but not in Petra:</span><br>
<span>(as of last report generation)</span><br><br>
<span>You've selected well:</span> <span id="select-result">none</span>.
</p>
 
<ol id="selectable">
<?php
$k=0;
//foreach unique well create a selectable item
foreach($unique_wells as $well) {
	//<li class="ui-widget-content">Item 1</li>
	?>
	<li class="ui-widget-content" name="<?php echo $well;?>" wsn="<?php echo $wsn_unique_incremental[$k];?>"><?php echo $well.", wsn:".$wsn_unique_incremental[$k];?></li>
	<?php
	$k++;
}
?>
</ol>
</div>

<div style="position:absolute;left:400px;top: 100px;width:100%;height:3000px;" id="div1"><h2>Select a well to Populate Perfs.</h2></div>
 
</body>
</html>

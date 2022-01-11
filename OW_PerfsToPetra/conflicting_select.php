<!doctype html>
<?php
//take name parameter sent from well selector and display OW perfs

$wsn = $_GET["wsn"];
//$well_name = $_GET["name"];
//ECHO $well_name."<br><br>";

//OPEN ODBC CONNECTION TO DATAVISION
$conn=odbc_connect('DataVision','datavision_query','pw');
if (!$conn) {
  exit("Connection Failed: " . $conn);
}

//$sql="SELECT * FROM OneLine.pic_Dim_Well ORDER BY wsn DESC";
$sql="SELECT * FROM OneLine.pic_Dim_Well WHERE wsn='".$wsn."' ORDER BY wsn ASC";
//echo "<br>".$sql;
$rs=odbc_exec($conn,$sql);
if (!$rs) {
  exit("Error in SQL");
}

while (odbc_fetch_row($rs)) {
  $var1=odbc_result($rs,"field_name");
  $var2=odbc_result($rs,"Pad_Name");
  $var3=odbc_result($rs,"well_name");
  $var4=odbc_result($rs,"well_spud");
  $var5=odbc_result($rs,"log_date");
  $var7=odbc_result($rs,"aries_first_dlvr_date");
  $var8=odbc_result($rs,"dim_well_id");
  $var9=odbc_result($rs,"wsn");
  $ow_well_id=odbc_result($rs,"ow_well_id");
  
  echo "<br><br>well name: ".$var3;
  echo "<br>wsn: ".$var9;
  }
?>
<html lang="en">
<head>
  <meta charset="utf-8" />
<title>jQuery UI Sortable with Selectable</title>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<style>
*,
*:before,
*:after {
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
}

#album {
	list-style: none;
	float: left;
	width: 25%;
	border: 1px solid blue;
}
#album2 {
	list-style: none;
	float: left;
	width: 25%;
	border: 1px solid blue;
}
#anotheralbum {
	list-style: none;
	float: left;
	width: 25%;
	border: 1px solid blue;
}
#album li  {
	float: left;
	margin: 0px;
}

#album2 li  {
	float: left;
	margin: 0px;
}
#anotheralbum li  {
	float: left;
	margin: 0px;
}

#album div {
	width: 340px;
	height: 20px;
	border: 1px solid #CCC;

	background: #F6F6F6;	
}
#album2 div {
	width: 340px;
	height: 20px;
	border: 1px solid #CCC;

	background: #F6F6F6;	
}
#anotheralbum div {
	width: 340px;
	height: 20px;
	border: 1px solid #CCC;

	background: #F6F6F6;	
}
#album .ui-sortable-placeholder {
	border: 1px dashed #CCC;
	width: 340px;
	height: 20px;
	background: none;
	visibility: visible !important;
}
#album2 .ui-sortable-placeholder {
	border: 1px dashed #CCC;
	width: 340px;
	height: 20px;
	background: none;
	visibility: visible !important;
}
#anotheralbum .ui-sortable-placeholder {
	border: 1px dashed #CCC;
	width: 340px;
	height: 20px;
	background: none;
	visibility: visible !important;
}

#album .ui-selecting div, 
#album .ui-selected div {
	background-color: #3C6;
}

#album2 .ui-selecting div, 
#album2 .ui-selected div {
	background-color: #3C6;
}
#anotheralbum .ui-selecting div, 
#anotheralbum .ui-selected div {
	background-color: #3C6;
}

</style>
<script>
$(function() {

	$('body').selectable({
		filter: 'li'
		//filter: '#album2 > li'
	});

	$('.connectedSortable').sortable({
		connectWith: ".connectedSortable",
		delay: 50,
		start: function(e, ui) {
			var topleft = 0;
			
			// if the current sorting LI is not selected, select
			$(ui.item).addClass('ui-selected');
			
			$('.ui-selected div').each(function() {

				// save reference to original parent
				var originalParent = $(this).parent()[0];
				$(this).data('origin', originalParent);
				
				// position each DIV in cascade
				$(this).css('position', 'absolute');
				$(this).css('top', topleft);
				$(this).css('left', topleft);
				topleft += 20;

			}).appendTo(ui.item); // glue them all inside current sorting LI

		},
		stop: function(e, ui) {
			$(ui.item).children().each(function() {
				
				// restore all the DIVs in the sorting LI to their original parents
				var originalParent = $(this).data('origin');
				$(this).appendTo(originalParent);

				// remove the cascade positioning
				$(this).css('position', '');
				$(this).css('top', '');
				$(this).css('left', '');
			});
			
			// put the selected LIs after the just-dropped sorting LI
			$('#album .ui-selected').insertAfter(ui.item);
			
			// put the selected LIs after the just-dropped sorting LI
			$('#album2 .ui-selected').insertAfter(ui.item);
		}
	});
});
</script>
</head>
<body>

<table style="width:75%">
<td><center><h2>OW data via DV</h2></center></td><td><center><h2>Petra Data</h2></center></td><td><center><h2>CSV Upload to Petra</h2></center></td>
</table>
<ul id="album" class="connectedSortable">
<?php
//request perf data from OW via DV
$sql2="SELECT * FROM dbo.dim_Perforation_Interval WHERE dim_well_id='".$var8."' ORDER BY md_top_shot ";
//echo "<br>".$sql;
$rs2=odbc_exec($conn,$sql2);

if (!$rs2) {
  exit("Error in DV SQL");
}

$k=1;
while (odbc_fetch_row($rs2)) {
  $var1=odbc_result($rs2,"perf_id");
  $var2=odbc_result($rs2,"perf_interval_id");
  $var3=odbc_result($rs2,"carrier_size");
  $var4=odbc_result($rs2,"charge_phasing");
  $var5=odbc_result($rs2,"charge_size");
  $var6=odbc_result($rs2,"csg_collar_top_shot");
  $var7=odbc_result($rs2,"interval_type");
  $var8=round(odbc_result($rs2,"md_bottom_shot"),0);
  $var9=round(odbc_result($rs2,"md_top_shot"),0);
  $var10=substr(odbc_result($rs2,"date_interval_shot"),0, 10);
  
  //get the absolute top shot
  if($k==1){
  $OW_absolute_top_shot = $var9;
  }
  
  echo '<li id="li'.$k.'"><div>'.$var9.' - '.$var8.' - '.$var7.' - '.$var10.' -OW</div></li>';
  $k++;
  }
?>
</ul>
    
<ul id="album2" class="connectedSortable">
<?php
$conn=odbc_connect("DRIVER={DBISAM 3 ODBC Driver};ConnectionType=Local;CatalogName=Z:/;","","");
if (!$conn) {
  exit("Connection Failed: " . $conn);
}

  //$sql="SELECT ZONEDEF.*, ZONEDEF.CHGDATE FROM ZONEDEF WHERE (((ZONEDEF.CHGDATE)>5/1/2011))";
    $sql="SELECT *
    FROM PERFS
    WHERE WSN=".$wsn;
$rs=odbc_exec($conn,$sql);
if (!$rs) {
  exit("Error in SQL");
}

$j=1;
while (odbc_fetch_row($rs)) {
  $var1=odbc_result($rs,"WSN");
  $var2=odbc_result($rs,"DATE");
  $var3=odbc_result($rs,"TOP");
  $var4=odbc_result($rs,"BASE");
  $var5=odbc_result($rs,"PERFTYPE");
  $var6=odbc_result($rs,"ENDDATE");
  
  //get the absolute top shot
  if($k==1){
  $PETRA_absolute_top_shot = $var3;
  }

  echo '<li id="li'.$j.'"><div>'.$var3.' - '.$var4.' - '.$var5.' - '.$var2.' - '.$var6.' -PETRA</div></li>';
  $j++;
}
echo "</table>";

  
odbc_close($conn);
?>
</ul>
<div id="anotheralbum" class="connectedSortable">
Drag here to include in CSV Upload
</div>
    
<br style="clear:both">
<br>
<br>

 
 
</body>
</html>
<?php
//take name parameter sent from well selector and display OW perfs

$wsn = $_GET["wsn"];
//$well_name = $_GET["name"];
//ECHO $well_name."<br><br>";

//OPEN ODBC CONNECTION TO DATAVISION
$conn=odbc_connect('DataVision','datavision_query','pw');
if (!$conn) {
  exit("Connection Failed: " . $conn);
}

$con2=odbc_connect('EDM_Win_Auth','EDMDB_OW_PIC_P','pw');
if (!$con2) {
  exit("Connection Failed: " . $con2);
}
/*
$sql="SELECT * FROM OneLine.pic_Dim_Well WHERE well_name=".$well_name."";
//echo "<br>".$sql;
$rs=odbc_exec($conn,$sql);
if (!$rs) {
  exit("Error in SQL");
}
*/

//$sql="SELECT * FROM OneLine.pic_Dim_Well ORDER BY wsn DESC";
$sql="SELECT * FROM OneLine.pic_Dim_Well WHERE wsn='".$wsn."' ORDER BY wsn ASC";
//echo "<br>".$sql;
$rs=odbc_exec($conn,$sql);
if (!$rs) {
  exit("Error in SQL");
}

while (odbc_fetch_row($rs)) {
  $var1=odbc_result($rs,"field_name");
  $var2=odbc_result($rs,"Pad_Name");
  $var3=odbc_result($rs,"well_name");
  $var4=odbc_result($rs,"well_spud");
  $var5=odbc_result($rs,"log_date");
  $var7=odbc_result($rs,"aries_first_dlvr_date");
  $var8=odbc_result($rs,"dim_well_id");
  $var9=odbc_result($rs,"wsn");
  $ow_well_id=odbc_result($rs,"ow_well_id");
  
  echo "<br><br>well name: ".$var3;
  echo "<br>wsn: ".$var9;
  }
  
  
//request perf data from OW via DV
$sql2="SELECT * FROM dbo.dim_Perforation_Interval WHERE dim_well_id='".$var8."' ORDER BY md_top_shot ";
//echo "<br>".$sql;
$rs2=odbc_exec($conn,$sql2);

if (!$rs2) {
  exit("Error in DV SQL");
}
echo "<br>OW Data stored in DV:";
echo "<table><tr>";
echo "<th>perf_id</th>";
echo "<th>perf_interval_id</th>";
echo "<th>carrier_size</th>";
echo "<th>charge_phasing</th>";
echo "<th>charge_size</th>";
echo "<th>casing_collar_top_shot</th>";
echo "<th>interval_type</th>";
echo "<th>md_bottom_shot</th>";
echo "<th>md_top_shot</th>";

while (odbc_fetch_row($rs2)) {
  $var1=odbc_result($rs2,"perf_id");
  $var2=odbc_result($rs2,"perf_interval_id");
  $var3=odbc_result($rs2,"carrier_size");
  $var4=odbc_result($rs2,"charge_phasing");
  $var5=odbc_result($rs2,"charge_size");
  $var6=odbc_result($rs2,"csg_collar_top_shot");
  $var7=odbc_result($rs2,"interval_type");
  $var8=odbc_result($rs2,"md_bottom_shot");
  $var9=odbc_result($rs2,"md_top_shot");
  
  echo "<tr><td>".$var1."</td>";
  echo "<td>$var2</td>";
  echo "<td>$var3</td>";
  echo "<td>$var4</td>";
  echo "<td>$var5</td>";
  echo "<td>$var6</td>";
  echo "<td>$var7</td>";
  echo "<td>$var8</td>";
  echo "<td>$var9</td></tr>";
  }
		
echo "</table><br><br>";

echo "<br>OW Data directly from OW:";
$sql="SELECT * FROM EDMDB_OW_PIC_P.dbo.CD_Perf_interval WHERE well_id='".$ow_well_id."' ORDER BY md_top_shot ASC";
$rs3=odbc_exec($con2,$sql);
if (!$rs3) {
  exit("Error in SQL");
}

while (odbc_fetch_row($rs3)) {
    echo odbc_result_all($rs3);
}


//close odbc connection ->IMPORTANT!!!!
odbc_close($conn);
odbc_close($con2); 
?>
 </html>

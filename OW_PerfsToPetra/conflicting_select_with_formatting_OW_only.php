<!doctype html>
<?php
//take name parameter sent from well selector and display OW perfs

$wsn = $_GET["wsn"];

//calendar function to convert date from days since 1-1-1900 to gregorian calendar
function date_convert($format, $xl_date) 
{ 
    $greg_start = gregoriantojd(12, 30, 1899); 
    return date($format, jdtounix($greg_start + $xl_date)); 
} 

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

$con3=odbc_connect('EDM_Win_Auth','EDMDB_OW_PIC_P','pw');
if (!$con3) {
  exit("Connection Failed: " . $con3);
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
  $uwi =odbc_result($rs,"uwi");
  
  echo "<br><br>well name: ".$var3;
  echo "<br>wsn: ".$var9;
  }
  
  //request perf data from OW via DV
$sql2="SELECT * FROM dbo.dim_Perforation_Interval WHERE dim_well_id='".$var8."' ORDER BY md_top_shot ASC";
//echo "<br>".$sql;
$rs2=odbc_exec($conn,$sql2);

if (!$rs2) {
  exit("Error in DV SQL");
}

$k=0;
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
  if($k==0){
  $OW_absolute_top_shot = $var9;
  }
  
  $string = $var9.' - '.$var8.' - '.$var7.' - '.$var10.' - OW';
  $OW_array[$k] = $string;

  //echo '<li id="li'.$k.'"><div>'.$var9.' - '.$var8.' - '.$var7.' - '.$var10.' -OW</div></li>';
  $k++;
  $OW_absolute_bottom_shot = $var8;
  }
  
  //Petra Data Request
$con2=odbc_connect("DRIVER={DBISAM 3 ODBC Driver};ConnectionType=Local;CatalogName=Z:/;","","");
if (!$con2) {
  exit("Connection Failed: " . $con2);
}

  //$sql="SELECT ZONEDEF.*, ZONEDEF.CHGDATE FROM ZONEDEF WHERE (((ZONEDEF.CHGDATE)>5/1/2011))";
    $sql="SELECT *
    FROM PERFS
    WHERE WSN=".$wsn."ORDER BY TOP ASC";
$rs3=odbc_exec($con2,$sql);
if (!$rs3) {
  exit("Error in SQL");
}

$j=0;
while (odbc_fetch_row($rs3)) {
  $var1=odbc_result($rs3,"WSN");
  $var2=odbc_result($rs3,"DATE");
  $var3=odbc_result($rs3,"TOP");
  $var4=odbc_result($rs3,"BASE");
  $var5=odbc_result($rs3,"PERFTYPE");
  $var6=odbc_result($rs3,"ENDDATE");
  
  //get the absolute top shot for OW, since no petra entry
  if($j==0){
  $PETRA_absolute_top_shot = $var9;
  }
	
  $string = $var3.' - '.$var4.' - '.$var5.' - '.$var2.' - '.$var6.' - PETRA';
  $PETRA_array[$j] = $string;
  //echo '<li id="li'.$j.'"><div>'.$var3.' - '.$var4.' - '.$var5.' - '.$var2.' - '.$var6.' -PETRA</div></li>';
  $j++;
  
}


$absolute_top_shot = $OW_absolute_top_shot;

$absolute_bottom_shot = $OW_absolute_bottom_shot;


//initialize values
$final_petra_array = array();
$final_ow_array = array();
$y = 0;
$final_ow_array = $OW_array;


?>
<html lang="en">
<head>
  <meta charset="utf-8" />
<title>Perfs in OW but not yet in Petra</title>
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
	width: 380px;
	height: 20px;
	border: 1px solid #CCC;

	background: #F6F6F6;	
}
#album2 div {
	width: 400px;
	height: 20px;
	border: 1px solid #CCC;

	background: #F6F6F6;	
}
#anotheralbum div {
	width: 400px;
	height: 20px;
	border: 1px solid #CCC;

	background: #F6F6F6;	
}
#album .ui-sortable-placeholder {
	border: 1px dashed #CCC;
	width: 380px;
	height: 20px;
	background: none;
	visibility: visible !important;
}
#album2 .ui-sortable-placeholder {
	border: 1px dashed #CCC;
	width: 400px;
	height: 20px;
	background: none;
	visibility: visible !important;
}
#anotheralbum .ui-sortable-placeholder {
	border: 1px dashed #CCC;
	width: 400px;
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
(function ($) {
    // http://stackoverflow.com/questions/19999388/jquery-check-if-user-is-using-ie
    // run it once
    var isIE = (function (ua) {
        var msie = ua.indexOf('MSIE '),
            trident = ua.indexOf('Trident/'),
            edge = ua.indexOf('Edge/');

        if (msie > 0) {
            // IE 10 or older => return version number
            return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
        }

        if (trident > 0) {
            // IE 11 => return version number
            var rv = ua.indexOf('rv:');
            return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
        }

        if (edge > 0) {
            // IE 12 => return version number
            return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
        }

        // other browser
        return false;
    }(navigator.userAgent || navigator.vendor || window.opera));

    // Dynamically loads swfobject + downloadify (setup)
    function getDownloadify(cb) {
        $.getScript('https://cdnjs.cloudflare.com/ajax/libs/swfobject/2.2/swfobject.min.js').done(function (script, textStatus) {
            console.log('swfobject: ' + textStatus);
        }).fail(function (jqxhr, settings, exception) {
            console.warn('swfobject: unable to get script: ' + exception);
        });

        $.getScript('http://cdn.uriit.ru/jsPDF/libs/Downloadify/js/downloadify.min.js').done(function (script, textStatus) {
            console.log('downloadify: ' + textStatus);
            if (typeof cb === "function") {
                cb();
            }
        }).fail(function (jqxhr, settings, exception) {
            console.warn('downloadify: unable to get script: ' + exception);
        });
    }

    // Setup the downloadify flash fallback and attach the csv data
    function setupDownloadify(data, exportLink) {
        exportLink.hide();
        Downloadify.create('downloadify', {
            filename: 'export_' + $.datepicker.formatDate("yymmdd", new Date()) + '.csv',
            data: data,
            onComplete: function () {
                alert('Your file has been saved.');
            },
            onCancel: function () {
                //alert('You have cancelled the saving of this file.');
            },
            onError: function () {
                alert('An error has occured. Please try again later');
            },
            swf: 'http://cdn.uriit.ru/jsPDF/libs/Downloadify/media/downloadify.swf',
            downloadImage: 'http://cdn.uriit.ru/jsPDF/libs/Downloadify/images/download.png',
            width: 68,
            height: 55,
            transparent: true,
            append: false
        });
    }

    $(function () {
        function addDataUri(exportLink) {
            var csvData = convertJQueryToCsv(),
                dataUri = 'data:text/csv;charset=utf-8,' + escape(csvData);

            if (exportLink.length && !isIE) {
                exportLink.prop({
                    href: dataUri,
                    target: '_blank',
                    download: 'export_' + +(new Date()) + '.csv'
                });
            }
        }

        function convertJQueryToCsv() {
            var arr = [];
            $.each($("#anotheralbum li div"), function () {
				//var perfs = value.replace("-", ",");
				//$(this).text() = value.replace("-", ",");
                arr.push("<?php echo '\"'.$uwi.'\"' ?>,"+$(this).text());
            });
			var csvtext = "UWI,TOP,BASE,PERFTYPE,DATE,ENDDATE" + String.fromCharCode(13)+ arr.join(String.fromCharCode(13));
			var csvtext = csvtext.replace(/ - /g, ",");
			var csvtext = csvtext.replace(/PETRA/g, "");
			var csvtext = csvtext.replace(/OW/g, "");
			var csvtext = csvtext.replace(/PERFORATED/g, "ACTIVE");
            return csvtext;
        }

        $(document.body).selectable({
            filter: 'li' //filter: '#album2 > li'
        });

        // change to "dropped" event, not sure what that is atm...
        //$('.exportLink').click(function(ev){
        //    addDataUri($(this));
        //});

        $('.connectedSortable').sortable({
            connectWith: ".connectedSortable",
            delay: 100,
            start: function (e, ui) {
                var padding = 0;

                // if the current sorting LI is not selected, select
                $(ui.item).addClass('ui-selected');

                $('.ui-selected div').each(function () {
                    // save reference to original parent
                    var originalParent = $(this).parent()[0];

                    $(this).data('origin', originalParent);

                    // position each DIV in cascade
                    $(this).css({
                        position: 'absolute',
                        top: padding,
                        left: padding
                    });

                    padding += 20;
                }).appendTo(ui.item); // glue them all inside current sorting LI
            },
            stop: function (e, ui) {
                $(ui.item).children().each(function () {

                    // restore all the DIVs in the sorting LI to their original parents
                    var originalParent = $(this).data('origin');
                    $(this).appendTo(originalParent);

                    // remove the cascade positioning
                    $(this).css({
                        position: '',
                        top: '',
                        left: ''
                    });
                });

                // put the selected LIs after the just-dropped sorting LI
                $('#album .ui-selected').insertAfter(ui.item);

                // put the selected LIs after the just-dropped sorting LI
                $('#album2 .ui-selected').insertAfter(ui.item);

                // put the selected LIs after the just-dropped sorting LI
                $('#anotheralbum .ui-selected').insertAfter(ui.item);

                addDataUri($('.exportLink'));
            }
        });
    });

    $(window).on('load', function () {
        if (isIE) {
            getDownloadify(function () {
                setupDownloadify(csvData, exportLink);
            });
        }
    });

}(window.jQuery));
</script>
</head>
<body>

<table style="width:75%">
<td><center><h2>OW data via DV</h2></center></td><td><center><h2>Petra Data</h2></center></td><td><center><h2>CSV Upload to Petra</h2></center></td>
</table>
<ul id="album" class="connectedSortable">
<?php
$k=1;
foreach($final_ow_array as $perf){
echo '<li id="li'.$k.'"><div>'.$perf.'</div></li>';
$k++;
}
?>
</ul>
    
<ul id="album2" class="connectedSortable">
<?php
$j=1;
foreach($final_petra_array as $perf){
echo '<li id="li'.$j.'"><div>'.$perf.'</div></li>';
$j++;
}
?>
</ul>
<div id="anotheralbum" class="connectedSortable">
Drag here to include in CSV Upload
</div>
    
<br style="clear:both">
<br>
<br>
<a class="exportLink">Right Click and "Save Link As" to Export to CSV</a>
 <div id="downloadify"></div>
 
</body>
</html>
<?php
//display all the rest of the data for the sake of checking

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
$rs3=odbc_exec($con3,$sql);
if (!$rs3) {
  exit("Error in SQL");
}

while (odbc_fetch_row($rs3)) {
    echo odbc_result_all($rs3);
}

  //$sql="SELECT ZONEDEF.*, ZONEDEF.CHGDATE FROM ZONEDEF WHERE (((ZONEDEF.CHGDATE)>5/1/2011))";
    $sql="SELECT *
    FROM PERFS
    WHERE WSN=".$wsn."ORDER BY TOP ASC";
$rs4=odbc_exec($con2,$sql);
if (!$rs) {
  exit("Error in SQL");
}
echo "<br><br>Petra Data directly from Petra:";
while (odbc_fetch_row($rs4)) {
    echo odbc_result_all($rs4);
}

//close odbc connection ->IMPORTANT!!!!
odbc_close($conn);
odbc_close($con2); 
odbc_close($con3); 
?>
 </html>

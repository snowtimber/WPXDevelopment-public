<html>
<body>


<?php
if (isset($_GET["startdate"])) {
	$startdate = $_GET["startdate"];
	$enddate = $_GET["enddate"];
	}

$timestamp = date('Y-m-d-H-i');

//    1/1/2014
$DrillScheduleThisYearHtml = file_get_contents('http://Lmeyer:pw!@wmstutwlcp1.wpx.wpxenergy.com:8080/webcore-williams/reports/general/quick-view-schedule/quick-view-schedule.wbx?date_from=1/1/' . date('Y',strtotime(date("Y-m-d", mktime()) . " + 0 day")) . '&regReports=general/quick-view-schedule/quick-view-schedule.wbx&bu_x=55967746&bu_x=55967747&units=us&date_to=1/1/' . date('Y',strtotime(date("Y-m-d", mktime()) . " + 365 day")));
$DrillScheduleNextYearHtml = file_get_contents('http://Lmeyer:pw!@wmstutwlcp1.wpx.wpxenergy.com:8080/webcore-williams/reports/general/quick-view-schedule/quick-view-schedule.wbx?date_from=1/1/' . date('Y',strtotime(date("Y-m-d", mktime()) . " + 365 day")) . '&regReports=general/quick-view-schedule/quick-view-schedule.wbx&bu_x=55967746&bu_x=55967747&units=us&date_to=1/1/' . date('Y',strtotime(date("Y-m-d", mktime()) . " + 730 day")));
//$DrillScheduleYearFromTodayHtml = file_get_contents('http://Lmeyer:pw!@wmstutwlcp1.wpx.wpxenergy.com:8080/webcore-williams/reports/general/quick-view-schedule/quick-view-schedule.wbx?date_from=' . date("m/d/Y") . '&regReports=general/quick-view-schedule/quick-view-schedule.wbx&bu_x=55967746&bu_x=55967747&units=us&date_to=' . date('m/d/Y',strtotime(date("Y-m-d", mktime()) . " + 365 day")));
if (isset($_GET["startdate"])) {
$DrillScheduleCustom = file_get_contents('http://Lmeyer:pw!@wmstutwlcp1.wpx.wpxenergy.com:8080/webcore-williams/reports/general/quick-view-schedule/quick-view-schedule.wbx?date_from=' . $startdate . '&regReports=general/quick-view-schedule/quick-view-schedule.wbx&bu_x=55967746&bu_x=55967747&units=us&date_to=' . $enddate);
}

echo $DrillScheduleThisYearHtml;
echo $DrillScheduleNextYearHtml;
//echo $DrillScheduleYearFromTodayHtml;
if (isset($_GET["startdate"])) {
echo $DrillScheduleCustom;
}

$file1 = fopen("DrillScheduleThisYearHtml.txt","w");
$file2 = fopen("DrillScheduleNextYearHtml.txt","w");
//$file3 = fopen("DrillScheduleYearFromTodayHtml.txt","w");
if (isset($_GET["startdate"])) {
$file4 =  fopen("DrillScheduleCustom.txt","w");
}

$wrote1 = fwrite($file1, $DrillScheduleThisYearHtml);
$wrote1 = fwrite($file2, $DrillScheduleNextYearHtml);
//$wrote1 = fwrite($file3, $DrillScheduleYearFromTodayHtml);
if (isset($_GET["startdate"])) {
$wrote1 = fwrite($file4, $DrillScheduleCustom);
}

fclose($file1);
fclose($file2);
//fclose($file3);
if (isset($_GET["startdate"])) {
fclose($file4);
}
?>

</body>
</html>

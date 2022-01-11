<?php
$oneYearOn = date('Y',strtotime(date("Y-m-d", mktime()) . " + 365 day"));
echo $oneYearOn;

echo "<br><br>";

$test =  date('m/d/Y',strtotime(date("m/d/Y", mktime()) . " + 365 day"));
echo $test;
?>

<?php
//create a average normalized production curve
//2nd method
//$last_average = 0;
foreach($Normalized_Production_array as $pair) $tmp[$pair['date']][] = $pair['gas_production_volume'];
foreach($tmp as $key => $value){

/*
$average_value = array_sum($value) / count($value);
$current_date = $key
$diff_date = $Current_date-$last_date
$interpolated_value = (($average_value - $last_average)/($diff_date))
*/

//$average_production_array[] = array('gas_production_volume' => array_sum($value) / count($value), 'date' => $key,'wellname' => "average", 'oil_production_volume' => 0);
$Normalized_Production_array[] = array('gas_production_volume' => array_sum($value) / count($value), 'date' => $key,'wellname' => "average", 'oil_production_volume' => 0);
$average_production_array[] = array('gas_production_volume' => array_sum($value) / count($value), 'date' => $key,'wellname' => "average", 'oil_production_volume' => 0);

/*	
$last_average = $average_value
$last_date = $current_date
*/
}
$wellname_array[] = "average";
?>

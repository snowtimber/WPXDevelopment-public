<?php
//collect dim well ids from get request attached to url
$IDs = $_GET["ID"];
$dim_well_ids = array();
$no_of_wells = substr_count($IDs,'-');
if ($no_of_wells> 1){
$dim_well_ids = explode('-',$IDs);
} else {
$dim_well_ids[] = $IDs;
}

// initialize wellname array
  $wellname_array = array();
  ?>

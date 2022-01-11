function xl_date_to_gregorian($format, $xl_date) 
{ 
    $greg_start = gregoriantojd(12, 31, 1899); 
    return date($format, jdtounix($greg_start + $xl_date)); 
} 

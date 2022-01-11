<?php
$conn=odbc_connect('DataVision','datavision_query','pw');
if (!$conn) {
  exit("Connection Failed: " . $conn);
}

   $result = odbc_tables($conn);

   $tables = array();
   while (odbc_fetch_row($result)){
     if(odbc_result($result,"TABLE_TYPE")=="TABLE")
       echo"<br>".odbc_result($result,"TABLE_NAME");

   }
  
odbc_close($conn);
?>

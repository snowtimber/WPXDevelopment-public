<?php
require_once ('PHPExcel/Classes/PHPExcel.php');
file_put_contents('tmp.html','<table border="1"><tr><td>123</td></tr></table>');
$objReader = new PHPExcel_Reader_HTML;
$objPHPExcel = $objReader->load('tmp.html');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("myExcelFile.xlsx");

echo "Excel File Saved!";
?>

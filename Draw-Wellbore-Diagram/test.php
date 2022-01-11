<?php
header('Content-type: image/png');
$png_image = imagecreate(150, 150);

imagecolorallocate($png_image, 15, 142, 210);
imagesetthickness($png_image, 5);
$black = imagecolorallocate($png_image, 0, 0, 0);

$x = 0;
$y = 0;
$w = imagesx($png_image) - 1;
$z = imagesy($png_image) - 1;

//border
imageline($png_image, $x, $y, $x, $y+$z, $black);
imageline($png_image, $x, $y, $x+$w, $y, $black);
imageline($png_image, $x+$w, $y, $x+$w, $y+$z, $black);
imageline($png_image, $x, $y+$z, $x+$w ,$y+$z, $black);

//diagonal
imageline($png_image, $x, $y, $x+$w, $y+$z, $black);

imagepng($png_image);
imagedestroy($png_image);
?>

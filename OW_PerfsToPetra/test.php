<?php
$a1=array("a"=>"red","b"=>"green","c"=>"blue","d"=>"yellow");
$a2=array("a"=>"red","b"=>"green","c"=>"blue","d"=>"yellow");

$result=array_diff_assoc($a1,$a2);
print_r($result);
IF($result==null){
echo "True";
}
IF($result<>null){
echo "<br><br>false";
} else {
echo "<br><br>True<br><br>";
}

echo 'Hello ' . $_GET["name"] . '!';
?>

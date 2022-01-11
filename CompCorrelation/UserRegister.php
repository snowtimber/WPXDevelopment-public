<?php
// Save User info usage statistics into mysql database
$servername = "localhost";
$username = "admin";
$password = "password";
$dbname = "appusers";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user = getenv("HOMEPATH");
//echo $user."<br>";
// escape variables for security
$user = str_replace("\Users\\","", $user);
//echo $user."<br>";
$date = date('Y:m:d H:i:s', time() - 28800);

$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

//echo "INSERT INTO users (user, application, url, timestamp)
//VALUES ('$user', 'CompCorrelation', '$url', '$date')";
$sql = "INSERT INTO users (user, application, url, timestamp)
VALUES ('$user', 'CompCorrelation', '$url', '$date')";

if (mysqli_query($conn, $sql)) {
//    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>

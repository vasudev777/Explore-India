<?php
$servername = "sql307.infinityfree.com";
$username = "if0_42189423";
$password = "iChFdT0gdr";
$dbname = "if0_42189423_exploreindia";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    // echo 'done';
}
?>

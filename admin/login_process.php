<?php

include('db.php');
session_start();

//  $link = mysqli_connect("localhost","root","","exploreindia");

$a_uname = $_POST['uname'];
$a_password = $_POST['password'];

//echo $a_uname;
//echo $a_password;

$sql1 = "SELECT * FROM admin_login where a_uname='$a_uname' and a_password='$a_password'";
$result = mysqli_query($conn, $sql1);
if (mysqli_num_rows($result) == 1) {
    while ($row = mysqli_fetch_array($result)) {
        $_SESSION['uname'] = $row['a_uname'];
        $_SESSION['password'] = $row['a_password'];
        //				echo "<script>alert('LOGIN SUCCESSFULLY')</script>";
        echo "<script>window.location='dashboard.php?add=1'</script>";
    }
} else {
    echo "<script>alert('LOGIN UNSUCCESSFULLY')</script>";
    echo "<script>window.location='login.php'</script>";
}

?>
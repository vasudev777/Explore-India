<?php
include('db.php');
session_start();
if (!isset($_SESSION['uemail'])) { header('Location: login.php'); exit; }

$cust_id  = intval($_POST['cust_id'] ?? $_SESSION['ucust_id']);
$fname    = mysqli_real_escape_string($conn, trim($_POST['fname']    ?? ''));
$lname    = mysqli_real_escape_string($conn, trim($_POST['lname']    ?? ''));
$mobile   = mysqli_real_escape_string($conn, trim($_POST['mobile']   ?? ''));
$gender   = mysqli_real_escape_string($conn, trim($_POST['gender']   ?? ''));
$state    = intval($_POST['state'] ?? 0);
$city     = intval($_POST['city'] ?? 0);
$address  = mysqli_real_escape_string($conn, trim($_POST['address']  ?? ''));
$birthdate= mysqli_real_escape_string($conn, trim($_POST['birthdate'] ?? ''));
$new_pass = trim($_POST['new_password'] ?? '');

$sql = "UPDATE customer_details SET
    cust_fname    = '$fname',
    cust_lname    = '$lname',
    cust_mobile   = '$mobile',
    cust_gender   = '$gender',
    cust_state    = '$state',
    cust_city     = '$city',
    cust_address  = '$address',
    cust_birthdate= '$birthdate'";

// Password update only if provided
if (!empty($new_pass)) {
    $hashed = password_hash($new_pass, PASSWORD_BCRYPT);
    $hashed = mysqli_real_escape_string($conn, $hashed);
    $sql .= ", cust_password = '$hashed'";
}

$sql .= " WHERE cust_id = $cust_id";

if (mysqli_query($conn, $sql)) {
    // Update session name
    $_SESSION['uname'] = $fname . ' ' . $lname;
    header('Location: profile.php?success=1');
} else {
    header('Location: profile.php?error=1');
}
exit;
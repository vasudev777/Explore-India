<?php
        error_reporting(E_ALL);
ini_set('display_errors', 1);
include('db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email = trim(mysqli_real_escape_string($conn, $_POST['email'] ?? ''));
$fname = trim(mysqli_real_escape_string($conn, $_POST['fname'] ?? ''));
$lname = trim(mysqli_real_escape_string($conn, $_POST['lname'] ?? ''));

if (!$email) {
    header('Location: login.php?error=google_failed');
    exit;
}

// Check if customer exists
$res  = mysqli_query($conn, "SELECT * FROM customer_details WHERE cust_email='$email'");

if (mysqli_num_rows($res) > 0) {
    $cust = mysqli_fetch_assoc($res);

    // Blocked check
   if ($cust['is_blocked'] == 1 ) {
        header('Location: login.php?blocked=1');
        exit;
    }

    // Login
    $_SESSION['uemail']   = $cust['cust_email'];
    $_SESSION['uname']    = $cust['cust_fname'] . ' ' . $cust['cust_lname'];
    $_SESSION['ucust_id'] = $cust['cust_id'];

} else {
    // New user — auto register via Google
    $randPass = password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO customer_details
        (cust_fname, cust_lname, cust_email, cust_password, is_verified, is_blocked)
        VALUES (?, ?, ?, ?, 1, 0)");
    $stmt->bind_param('ssss', $fname, $lname, $email, $randPass);
    $stmt->execute();
    $newId = $conn->insert_id;

    $_SESSION['uemail']   = $email;
    $_SESSION['uname']    = $fname . ' ' . $lname;
    $_SESSION['ucust_id'] = $newId;
}

header('Location: index.php');
exit;
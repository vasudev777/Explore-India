<?php
include('db.php');
session_start();

$message = '';
$status = 'error';

if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, trim($_GET['token']));
    
    // Check if token exists
    $check_token = mysqli_query($conn, "SELECT localg_id, localg_name FROM local_guide WHERE activation_token='$token'");
    if (mysqli_num_rows($check_token) > 0) {
        $row = mysqli_fetch_assoc($check_token);
        $guide_id = $row['localg_id'];
        $name = $row['localg_name'];
        
        // Update database: verify email (localg_emailverify = 1) and remove token
        $update = mysqli_query($conn, "UPDATE local_guide SET localg_emailverify=1, activation_token=NULL WHERE localg_id='$guide_id'");
        if ($update) {
            $status = 'success';
            $message = "Thank you, <strong>" . htmlspecialchars($name) . "</strong>! Your email address has been successfully verified.";
        } else {
            $message = "Database error. Could not verify email at this moment.";
        }
    } else {
        $message = "Invalid or expired verification token! Please make sure you clicked the correct link.";
    }
} else {
    $message = "No verification token provided.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Email Verification – Explore India</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css'/>
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Montserrat:400,500,600,700,800,900" rel="stylesheet">
    <style>
        body {
            background: #F8FAFC;
            font-family: 'Open Sans', sans-serif;
            color: #334155;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .status-card {
            background: #ffffff;
            border: 1px solid #E2E8F0;
            border-radius: 24px;
            padding: 50px 40px;
            width: 100%;
            max-width: 480px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        }
        .status-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin: 0 auto 30px;
        }
        .status-icon.success {
            background: #ECFDF5;
            color: #10B981;
            border: 2px solid #A7F3D0;
            box-shadow: 0 0 25px rgba(16, 185, 129, 0.1);
        }
        .status-icon.error {
            background: #FEF2F2;
            color: #EF4444;
            border: 2px solid #FEE2E2;
            box-shadow: 0 0 25px rgba(239, 68, 68, 0.1);
        }
        h2 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 22px;
            color: #0F172A;
            margin-bottom: 16px;
        }
        p {
            font-size: 14.5px;
            color: #64748B;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .btn-action {
            background: linear-gradient(135deg, #FF782C, #F39C12);
            color: #fff;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            padding: 12px 30px;
            font-size: 15px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
            box-shadow: 0 4px 15px rgba(255, 120, 44, 0.2);
        }
        .btn-action:hover {
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(255, 120, 44, 0.3);
        }
        .btn-action.secondary {
            background: #F1F5F9;
            color: #475569;
            border: 1.5px solid #E2E8F0;
            box-shadow: none;
        }
        .btn-action.secondary:hover {
            background: #E2E8F0;
            color: #334155;
            box-shadow: none;
        }
    </style>
</head>
<body>

<div class="status-card">
    <?php if ($status === 'success'): ?>
        <div class="status-icon success">
            <i class="fa fa-check"></i>
        </div>
        <h2>Email Verified!</h2>
        <p><?= $message ?></p>
        <div style="background: rgba(255,120,44,0.05); border: 1px solid rgba(255,120,44,0.15); border-radius: 12px; padding: 15px; margin-bottom: 25px; font-size: 13px; color: #D97706; line-height:1.5; text-align:left;">
            💡 <strong>Next Step:</strong> Administrator team will review and approve your application. You will receive an approval email once your account is ready.
        </div>
        <a href="guide_login.php" class="btn-action">Go to Login</a>
    <?php else: ?>
        <div class="status-icon error">
            <i class="fa fa-times"></i>
        </div>
        <h2>Verification Failed</h2>
        <p><?= $message ?></p>
        <a href="guide_register.php" class="btn-action secondary">Back to Register</a>
    <?php endif; ?>
</div>

</body>
</html>

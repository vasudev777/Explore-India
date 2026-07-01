<?php
include('db.php');
session_start();

$error = '';
$success = '';
$token_valid = false;
$guide_id = 0;
$name = '';

if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, trim($_GET['token']));
    
    // Check if token exists in local_guide table
    $query = "SELECT localg_id, localg_name FROM local_guide WHERE activation_token='$token'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $guide_id = $row['localg_id'];
        $name = $row['localg_name'];
        $token_valid = true;
        
        // Handle password reset submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
            $password = trim($_POST['password']);
            $confirm_password = trim($_POST['confirm_password']);
            
            if (empty($password)) {
                $error = "Password cannot be empty!";
            } elseif ($password !== $confirm_password) {
                $error = "Passwords do not match! Please verify and try again.";
            } else {
                $password_esc = mysqli_real_escape_string($conn, $password);
                
                // Update password (plain-text in their schema) and clear reset token
                $update = mysqli_query($conn, "UPDATE local_guide SET localg_password='$password_esc', activation_token=NULL WHERE localg_id='$guide_id'");
                if ($update) {
                    $success = "Your password has been successfully reset! You can now log in using your new credentials.";
                    $token_valid = false; // Disable form showing
                } else {
                    $error = "Database error. Could not reset password at this moment.";
                }
            }
        }
    } else {
        $error = "Invalid or expired password reset link! Please request a new reset link.";
    }
} else {
    $error = "No password reset token provided. Please use the link sent to your email.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password – Explore India Guide Portal</title>
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
        .form-card {
            background: #ffffff;
            border: 1px solid #E2E8F0;
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        }
        .logo-title {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-title h2 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 26px;
            color: #0F172A;
        }
        .logo-title h2 span {
            color: #FF782C;
        }
        .form-group-custom {
            margin-bottom: 20px;
        }
        .form-group-custom label {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            color: #64748B;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 6px;
        }
        .form-input-custom {
            width: 100%;
            border: 1.5px solid #E2E8F0;
            border-radius: 12px;
            padding: 11px 16px;
            font-size: 14px;
            color: #0F172A;
            background: #FFFFFF;
            outline: none;
            transition: all 0.2s;
        }
        .form-input-custom:focus {
            border-color: #FF782C;
            box-shadow: 0 0 0 3px rgba(255, 120, 44, 0.12);
        }
        .btn-submit {
            background: linear-gradient(135deg, #FF782C, #F39C12);
            color: #fff;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-size: 15px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            transition: all 0.2s;
            box-shadow: 0 4px 15px rgba(255, 120, 44, 0.2);
        }
        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(255, 120, 44, 0.3);
        }
        .alert {
            border-radius: 12px;
            font-size: 13.5px;
            margin-bottom: 20px;
            padding: 12px 16px;
            line-height: 1.5;
        }
        .alert-danger {
            background: #FEF2F2;
            border: 1px solid #FEE2E2;
            color: #991B1B;
        }
        .alert-success {
            background: #ECFDF5;
            border: 1px solid #D1FAE5;
            color: #065F46;
        }
        .footer-link {
            text-align: center;
            margin-top: 24px;
            font-size: 13px;
            color: #64748B;
        }
        .footer-link a {
            color: #FF782C;
            text-decoration: none;
            font-weight: 600;
        }
        .footer-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="form-card">
    <div class="logo-title">
        <h2><span class="fa fa-globe"></span> Explore <span>India</span></h2>
        <p style="color:#64748B; font-size:14px; margin-top:5px;">Reset Account Password</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
        <div class="footer-link">
            <a href="guide_login.php">Proceed to Log In</a>
        </div>
    <?php endif; ?>

    <?php if ($token_valid): ?>
        <p style="font-size:13.5px; color:#64748B; margin-bottom:20px; text-align:center;">
            Hello <strong><?= htmlspecialchars($name) ?></strong>, please enter your new password below.
        </p>
        <form method="POST" action="">
            <div class="form-group-custom">
                <label>New Password</label>
                <input type="password" name="password" class="form-input-custom" placeholder="••••••••" required>
            </div>

            <div class="form-group-custom">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-input-custom" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-submit">Update Password</button>
        </form>
    <?php elseif (!$success): ?>
        <div class="footer-link">
            <a href="guide_forgot.php">Request a new reset link</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>

<?php
include('db.php');
session_start();

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    
    // Check if email exists in local_guide table
    $query = "SELECT localg_id, localg_name FROM local_guide WHERE localg_email='$email'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $guide_id = $row['localg_id'];
        $name = $row['localg_name'];
        
        // Generate reset token
        $token = bin2hex(random_bytes(16));
        
        // Save token to database activation_token field
        $update = mysqli_query($conn, "UPDATE local_guide SET activation_token='$token' WHERE localg_id='$guide_id'");
        
        if ($update) {
            // Generate link
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'];
            $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
            $reset_link = "$protocol://$host$uri/guide_reset.php?token=$token";
            
            // Send Reset Link via PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'shram0610@gmail.com';
                $mail->Password   = 'uhnrjrocoecdeizv'; // Gmail App password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('shram0610@gmail.com', 'Explore India');
                $mail->addAddress($email, $name);
                $mail->isHTML(true);
                $mail->Subject = 'Reset Your Password - Explore India Guide Portal';
                
                $mail->Body    = '
                <div style="font-family:Arial,sans-serif;max-width:500px;margin:0 auto;background:#f8fafc;border-radius:16px;overflow:hidden;border:1px solid #e2e8f0;">
                    <div style="background:linear-gradient(135deg,#FF782C,#F39C12);padding:32px;text-align:center;">
                        <h2 style="color:#fff;margin:0;font-size:24px;">🇮🇳 Explore India</h2>
                        <p style="color:rgba(255,255,255,0.9);margin:5px 0 0;font-size:13px;font-weight:600;">GUIDE PORTAL PASSWORD RESET</p>
                    </div>
                    <div style="padding:32px;background:#fff;">
                        <p style="color:#0f172a;font-size:16px;margin-bottom:12px;font-weight:700;">Hello '.$name.',</p>
                        <p style="color:#475569;font-size:14.5px;line-height:1.6;">We received a request to reset your password for your Local Guide account on Explore India. Click the button below to set a new password. This link is valid for a single use.</p>
                        <div style="text-align:center;margin:30px 0;">
                            <a href="'.$reset_link.'" style="background:linear-gradient(135deg,#FF782C,#F39C12);color:#fff;text-decoration:none;padding:12px 30px;border-radius:8px;font-weight:700;display:inline-block;box-shadow:0 4px 15px rgba(255,120,44,0.3);">Reset Password</a>
                        </div>
                        <p style="color:#64748b;font-size:12px;line-height:1.5;">If you did not request this password reset, you can safely ignore this email. Your password will remain unchanged.</p>
                        <p style="color:#64748b;font-size:12px;line-height:1.5;margin-top:10px;">If the button above does not work, copy and paste this link into your browser:<br><a href="'.$reset_link.'">'.$reset_link.'</a></p>
                        <hr style="border:none;border-top:1px solid #e2e8f0;margin:24px 0;">
                        <p style="color:#94a3b8;font-size:11px;text-align:center;">© 2026 Explore India. All Rights Reserved.</p>
                    </div>
                </div>';
                
                $mail->send();
                $success = "A password reset link has been successfully sent to <strong>$email</strong>. Please check your inbox and follow the instructions.";
            } catch (Exception $e) {
                $error = "Failed to send reset link email. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $error = "Database error. Could not request password reset at this moment.";
        }
    } else {
        $error = "No active local guide account found with this email address!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password – Explore India Guide Portal</title>
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
        <p style="color:#64748B; font-size:14px; margin-top:5px;">Guide Password Recovery</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
        <div class="footer-link">
            <a href="guide_login.php">Back to Log In</a>
        </div>
    <?php else: ?>
        <p style="font-size:13.5px; color:#64748B; margin-bottom:20px; line-height:1.6; text-align:center;">
            Enter your registered guide email address below, and we will email you a secure link to reset your password.
        </p>
        <form method="POST" action="">
            <div class="form-group-custom">
                <label>Email Address</label>
                <input type="email" name="email" class="form-input-custom" placeholder="name@example.com" required>
            </div>

            <button type="submit" class="btn-submit">Send Reset Link</button>
        </form>
        <div class="footer-link">
            Remembered your password? <a href="guide_login.php">Log In here</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>

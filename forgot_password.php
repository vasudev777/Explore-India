<?php
    error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include('db.php');
session_start();
         use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
$step    = $_GET['step'] ?? 'email';
$message = '';
$error   = '';

// Step 1: Email submit → Send OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $r = mysqli_query($conn, "SELECT * FROM customer_details WHERE cust_email='$email'");
    if ($r && mysqli_num_rows($r) > 0) {
        $otp     = rand(100000, 999999);
        $expiry  = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        mysqli_query($conn, "UPDATE customer_details SET otp_code='$otp', otp_expiry='$expiry' WHERE cust_email='$email'");
        $_SESSION['fp_email'] = $email;

        // Send OTP via PHPMailer
        try {


require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'shram0610@gmail.com';
            $mail->Password   = 'uhnrjrocoecdeizv';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;
            $mail->setFrom('shram0610@gmail.com', 'Explore India');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = ' Password Reset OTP – Explore India';
            $mail->Body = '
            <div style="font-family:Arial,sans-serif;max-width:480px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;border:1px solid #e9ecef;">
                <div style="background:linear-gradient(135deg,#0a0a0a,#1a1a2e);padding:28px;text-align:center;">
                    <div style="font-size:32px;margin-bottom:8px;">🔐</div>
                    <h2 style="color:#fff;margin:0;font-size:20px;">Reset Your Password</h2>
                </div>
                <div style="padding:28px;">
                    <p style="color:#666;font-size:14px;margin-bottom:20px;">Use the OTP below to reset your password. Valid for 10 minutes.</p>
                    <div style="background:#f8f9fa;border:1.5px solid #e9ecef;border-radius:12px;padding:20px;text-align:center;margin-bottom:20px;">
                        <div style="font-size:11px;color:#aaa;letter-spacing:2px;text-transform:uppercase;margin-bottom:8px;">Your OTP</div>
                        <div style="font-size:36px;font-weight:800;color:#f5a623;letter-spacing:8px;">' . $otp . '</div>
                    </div>
                    <p style="color:#aaa;font-size:12px;">If you did not request this, please ignore this email.</p>
                </div>
            </div>';
            $mail->send();
            header('Location: forgot_password.php?step=otp');
            exit;
        } catch (Exception $e) {
            $error = 'Could not send OTP. Please try again.';
        }
    } else {
        $error = 'No account found with this email address.';
    }
}

// Step 2: OTP verify
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp'])) {
    $otp   = mysqli_real_escape_string($conn, trim($_POST['otp']));
    $email = mysqli_real_escape_string($conn, $_SESSION['fp_email'] ?? '');
    $r = mysqli_query($conn, "SELECT * FROM customer_details WHERE cust_email='$email' AND otp_code='$otp' AND otp_expiry > NOW()");
    if ($r && mysqli_num_rows($r) > 0) {
        $_SESSION['fp_verified'] = true;
        header('Location: forgot_password.php?step=reset');
        exit;
    } else {
        $error = 'Invalid or expired OTP. Please try again.';
        $step  = 'otp';
    }
}

// Step 3: New password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_pass'])) {
    if (!($_SESSION['fp_verified'] ?? false)) { header('Location: forgot_password.php'); exit; }
    $new_pass  = trim($_POST['new_pass']);
    $conf_pass = trim($_POST['conf_pass']);
    $email     = mysqli_real_escape_string($conn, $_SESSION['fp_email'] ?? '');

    if ($new_pass !== $conf_pass) {
        $error = 'Passwords do not match.';
        $step  = 'reset';
    } elseif (strlen($new_pass) < 6) {
        $error = 'Password must be at least 6 characters.';
        $step  = 'reset';
    } else {
        $hashed = password_hash($new_pass, PASSWORD_BCRYPT);
        $hashed = mysqli_real_escape_string($conn, $hashed);
        mysqli_query($conn, "UPDATE customer_details SET cust_password='$hashed', otp_code=NULL, otp_expiry=NULL WHERE cust_email='$email'");
        unset($_SESSION['fp_email'], $_SESSION['fp_verified']);
        header('Location: login.php?reset=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password – Explore India</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css'/>
    <link href="css/style.css" rel='stylesheet' type='text/css'/>
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Montserrat:400,600,700,800" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f5f6fa; font-family: 'Open Sans', sans-serif; color: #1a1a1a; min-height: 100vh; display: flex; flex-direction: column; }

        /* DARK HERO */
        .page-hero { background: linear-gradient(160deg, #0a0a0a 0%, #12082a 50%, #0a0a0a 100%); padding: 100px 20px 60px; text-align: center; }
        .hero-icon { font-size: 48px; margin-bottom: 14px; }
        .page-hero h1 { font-family: 'Montserrat', sans-serif; font-size: clamp(24px, 5vw, 40px); font-weight: 900; color: #fff; margin-bottom: 8px; text-transform: none !important; }
        .page-hero p { font-size: 14px; color: rgba(255,255,255,0.4); }

        /* Steps indicator */
        .steps { display: flex; justify-content: center; gap: 0; margin-top: 24px; }
        .step { display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: rgba(255,255,255,0.3); }
        .step.active { color: #f5a623; }
        .step.done   { color: #2ecc9a; }
        .step-num { width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); }
        .step.active .step-num { background: rgba(245,166,35,0.2); border-color: #f5a623; color: #f5a623; }
        .step.done .step-num   { background: rgba(46,204,154,0.2); border-color: #2ecc9a; color: #2ecc9a; }
        .step-line { width: 32px; height: 1px; background: rgba(255,255,255,0.1); margin: 0 6px; }

        /* WHITE FORM */
        .form-section { flex: 1; padding: 40px 20px 80px; display: flex; align-items: flex-start; justify-content: center; }
        .form-card { background: #fff; border: 1.5px solid #e9ecef; border-radius: 20px; overflow: hidden; width: 100%; max-width: 440px; box-shadow: 0 8px 40px rgba(0,0,0,0.08); }
        .form-header { padding: 20px 24px 16px; border-bottom: 1px solid #e9ecef; background: #fff; }
        .form-header h3 { font-family: 'Montserrat', sans-serif; font-size: 16px; font-weight: 700; color: #1a1a1a; margin-bottom: 4px; text-transform: none !important; }
        .form-header p { font-size: 12px; color: #888; margin: 0; }
        .form-body { padding: 22px 24px 24px; }

        .field-group { margin-bottom: 16px; }
        .field-label { font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #aaa; display: block; margin-bottom: 7px; }
        .field-input { width: 100%; background: #f8f9ff; border: 1.5px solid #e9ecef; border-radius: 10px; color: #1a1a1a; font-size: 14px; padding: 12px 16px; outline: none; transition: border-color 0.2s; font-family: 'Open Sans', sans-serif; }
        .field-input:focus { border-color: #f5a623; background: #fffdf5; }

        /* OTP boxes */
        .otp-inputs { display: flex; gap: 8px; justify-content: center; margin-bottom: 20px; }
        .otp-box { width: 48px; height: 56px; border: 1.5px solid #e9ecef; border-radius: 10px; text-align: center; font-size: 22px; font-weight: 800; font-family: 'Montserrat', sans-serif; color: #1a1a1a; background: #f8f9fa; outline: none; transition: border-color 0.2s; }
        .otp-box:focus { border-color: #f5a623; background: #fffdf5; }

        .btn-submit { width: 100%; background: linear-gradient(135deg, #f5a623, #d48a1a); border: none; border-radius: 12px; color: #fff; font-size: 15px; font-weight: 700; font-family: 'Montserrat', sans-serif; padding: 14px; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; text-transform: none !important; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(245,166,35,0.35); }

        .back-link { display: block; text-align: center; margin-top: 14px; font-size: 13px; color: #888; text-decoration: none; }
        .back-link:hover { color: #f5a623; text-decoration: none; }

        .alert-msg { padding: 10px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .alert-error { background: #fff0f0; border: 1px solid #ff5050; color: #ff5050; }

        /* DARK STATS */
        .stats-section { background: #0a0a0a; padding: 40px 20px; }
        .stats-row { display: flex; justify-content: center; gap: 40px; }
        .s-stat { text-align: center; }
        .s-num { font-family: 'Montserrat', sans-serif; font-size: 24px; font-weight: 800; }
        .s-label { font-size: 11px; color: rgba(255,255,255,0.3); margin-top: 3px; }
    </style>
</head>
<body oncontextmenu="return false;">
<?php include('header.php'); ?>

<!-- DARK HERO -->
<div class="page-hero">
    <div class="hero-icon">🔐</div>
    <h1>Forgot Password</h1>
    <p>Reset your password in 3 simple steps</p>

    <div class="steps">
        <div class="step <?= $step==='email' ? 'active' : 'done' ?>">
            <div class="step-num"><?= $step==='email' ? '1' : '✓' ?></div>
            Email
        </div>
        <div class="step-line"></div>
        <div class="step <?= $step==='otp' ? 'active' : ($step==='reset' ? 'done' : '') ?>">
            <div class="step-num"><?= $step==='reset' ? '✓' : '2' ?></div>
            OTP
        </div>
        <div class="step-line"></div>
        <div class="step <?= $step==='reset' ? 'active' : '' ?>">
            <div class="step-num">3</div>
            Reset
        </div>
    </div>
</div>

<!-- WHITE FORM -->
<div class="form-section">
    <div class="form-card">

        <?php if ($step === 'email'): ?>
        <div class="form-header">
            <h3>📧 Enter Your Email</h3>
            <p>We'll send a 6-digit OTP to your registered email</p>
        </div>
        <div class="form-body">
            <?php if ($error): ?><div class="alert-msg alert-error"><span class="fa fa-times-circle"></span> <?= $error ?></div><?php endif; ?>
            <form method="POST" action="forgot_password.php">
                <div class="field-group">
                    <label class="field-label">Email Address</label>
                    <input type="email" name="email" class="field-input" placeholder="your@email.com" required autofocus>
                </div>
                <button type="submit" class="btn-submit"><span class="fa fa-paper-plane"></span> &nbsp; Send OTP</button>
            </form>
            <a href="login.php" class="back-link"><span class="fa fa-arrow-left"></span> Back to Login</a>
        </div>

        <?php elseif ($step === 'otp'): ?>
        <div class="form-header">
            <h3>🔢 Enter OTP</h3>
            <p>6-digit OTP sent to <?= htmlspecialchars($_SESSION['fp_email'] ?? '') ?></p>
        </div>
        <div class="form-body">
            <?php if ($error): ?><div class="alert-msg alert-error"><span class="fa fa-times-circle"></span> <?= $error ?></div><?php endif; ?>
            <form method="POST" action="forgot_password.php?step=otp" id="otpForm">
                <input type="hidden" name="otp" id="otpHidden">
                <div class="otp-inputs">
                    <?php for($i=1;$i<=6;$i++): ?>
                    <input type="text" class="otp-box" maxlength="1" id="otp<?= $i ?>" inputmode="numeric" pattern="[0-9]">
                    <?php endfor; ?>
                </div>
                <button type="submit" class="btn-submit"><span class="fa fa-check-circle"></span> &nbsp; Verify OTP</button>
            </form>
            <a href="forgot_password.php" class="back-link"><span class="fa fa-arrow-left"></span> Change Email</a>
        </div>

        <?php elseif ($step === 'reset'): ?>
        <div class="form-header">
            <h3>🔑 Set New Password</h3>
            <p>Choose a strong password for your account</p>
        </div>
        <div class="form-body">
            <?php if ($error): ?><div class="alert-msg alert-error"><span class="fa fa-times-circle"></span> <?= $error ?></div><?php endif; ?>
            <form method="POST" action="forgot_password.php?step=reset">
                <div class="field-group">
                    <label class="field-label">New Password</label>
                    <input type="password" name="new_pass" class="field-input" placeholder="Min 6 characters" required>
                </div>
                <div class="field-group">
                    <label class="field-label">Confirm Password</label>
                    <input type="password" name="conf_pass" class="field-input" placeholder="Repeat password" required>
                </div>
                <button type="submit" class="btn-submit"><span class="fa fa-lock"></span> &nbsp; Reset Password</button>
            </form>
        </div>
        <?php endif; ?>

    </div>
</div>

<!-- DARK STATS -->
<div class="stats-section">
    <div class="stats-row">
        <div class="s-stat"><div class="s-num" style="color:#5ea0ff;">🔐</div><div class="s-label">Secure Reset</div></div>
        <div class="s-stat"><div class="s-num" style="color:#f5a623;">10 min</div><div class="s-label">OTP Valid</div></div>
        <div class="s-stat"><div class="s-num" style="color:#5ecfa8;">✓</div><div class="s-label">Encrypted</div></div>
    </div>
</div>

<?php include('footer.php'); ?>

<script>
// OTP box auto-focus & combine
var boxes = document.querySelectorAll('.otp-box');
if (boxes.length) {
    boxes.forEach(function(box, i) {
        box.addEventListener('input', function() {
            if (this.value.length === 1 && i < boxes.length - 1) boxes[i+1].focus();
        });
        box.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && i > 0) boxes[i-1].focus();
        });
    });

    document.getElementById('otpForm').addEventListener('submit', function() {
        var otp = '';
        boxes.forEach(function(b) { otp += b.value; });
        document.getElementById('otpHidden').value = otp;
    });
}
</script>
</body>
</html>
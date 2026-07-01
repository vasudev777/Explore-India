<?php
include('db.php');
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['guide_id'])) {
    header('Location: guide_dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);
    
    $query = "SELECT * FROM local_guide WHERE localg_email='$email'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // Compare password (plain-text in their schema)
        if ($row['localg_password'] === $password) {
            
            // Check email verification status
            if (intval($row['localg_emailverify']) !== 1) {
                $error = "Please verify your email address first! Check your inbox for the activation link.";
            }
            // Check administrator approval status
            elseif (intval($row['localg_approve']) !== 1) {
                $error = "Your account is pending administrator review and approval. You will receive an email once approved.";
            }
            // Check account block status (status = 1 is blocked)
            elseif (intval($row['status']) === 1) {
                $error = "Your account has been blocked by the administrator. Please contact support.";
            }
            // Success
            else {
                $_SESSION['guide_id'] = $row['localg_id'];
                $_SESSION['guide_name'] = $row['localg_name'];
                $_SESSION['guide_email'] = $row['localg_email'];
                header('Location: guide_dashboard.php');
                exit;
            }
        } else {
            $error = "Invalid password! Please try again.";
        }
    } else {
        $error = "No account found with this email address!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Local Guide Log In – Explore India</title>
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
        }
        .alert-danger {
            background: #FEF2F2;
            border: 1px solid #FEE2E2;
            color: #991B1B;
            line-height: 1.5;
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
        <p style="color:#64748B; font-size:14px; margin-top:5px;">Local Guide Portal Log In</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group-custom">
            <label>Email Address</label>
            <input type="email" name="email" class="form-input-custom" placeholder="name@example.com" required>
        </div>

        <div class="form-group-custom">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                <label style="margin:0;">Password</label>
                <a href="guide_forgot.php" style="font-size:11.5px; color:#FF782C; text-decoration:none; font-weight:600;">Forgot Password?</a>
            </div>
            <input type="password" name="password" class="form-input-custom" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn-submit">Log In</button>
    </form>

    <div class="footer-link">
        Don't have a guide account? <a href="guide_register.php">Register here</a>
    </div>
</div>

</body>
</html>

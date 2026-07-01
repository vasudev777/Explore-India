<?php
    
    error_reporting(E_ALL);
ini_set('display_errors', 1);
include('db.php');
session_start();


// Agar already logged in hai toh index pe bhejo
if (isset($_SESSION['uemail'])) {
    header('Location: index.php');
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname    = trim(mysqli_real_escape_string($conn, $_POST['fname']));
    $lname    = trim(mysqli_real_escape_string($conn, $_POST['lname']));
    $email    = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];
    $mobile   = trim(mysqli_real_escape_string($conn, $_POST['mobile']));
    $address  = trim(mysqli_real_escape_string($conn, $_POST['address']));
    $gender   = mysqli_real_escape_string($conn, $_POST['gender']);
    $bdate    = mysqli_real_escape_string($conn, $_POST['birthdate']);
    $state    = mysqli_real_escape_string($conn, $_POST['state']);
    $city     = mysqli_real_escape_string($conn, $_POST['city']);

    // Validations
    if ($password !== $confirm) {
        $error = 'Passwords do not match!';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters!';
    } else {
        // Check duplicate email
        $chk = mysqli_query($conn, "SELECT cust_id FROM customer_details WHERE cust_email='$email'");
        if (mysqli_num_rows($chk) > 0) {
            $error = 'This email is already registered! <a href="login.php">Login here</a>';
        } else {
            // Hash password
            $hashed = password_hash($password, PASSWORD_BCRYPT);

            // Generate 6-digit OTP
            $otp    = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            // Store in session temporarily (insert after OTP verify)
            $_SESSION['reg_data'] = [
                'fname'    => $fname,
                'lname'    => $lname,
                'email'    => $email,
                'password' => $hashed,
                'mobile'   => $mobile,
                'address'  => $address,
                'gender'   => $gender,
                'bdate'    => $bdate,
                'state'    => $state,
                'city'     => $city,
                'otp'      => $otp,
                'expiry'   => $expiry,
            ];

            // Send OTP
            require_once 'send_otp.php';
            $sent = sendOTP($email, $fname . ' ' . $lname, $otp);

            if ($sent) {
                header('Location: otp_verify.php?type=register');
                exit;
            } else {
                $error = 'Failed to send OTP. Please check your email and try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register – Explore India</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css'/>
    <link href="css/style.css" rel='stylesheet' type='text/css'/>
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Montserrat:400,500,600,700,800" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="js/disable.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #0a0a0a; font-family: 'Open Sans', sans-serif; color: #fff; min-height: 100vh; }

        .reg-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 100px 16px 60px;
            background: linear-gradient(160deg, #0a0a0a 0%, #12082a 50%, #0a0a0a 100%);
        }
        .reg-card {
            background: #141414;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            padding: 40px 36px;
            width: 100%;
            max-width: 560px;
        }
        .reg-card h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 26px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 6px;
        }
        .reg-card .sub {
            font-size: 13px;
            color: rgba(255,255,255,0.4);
            margin-bottom: 28px;
        }
        .reg-card .sub a { color: #f5a623; text-decoration: none; }

        .form-row { display: flex; gap: 12px; }
        .form-row .form-group { flex: 1; }

        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            font-size: 11.5px;
            font-weight: 600;
            letter-spacing: 0.5px;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 10px;
            padding: 11px 14px;
            font-size: 14px;
            color: #fff;
            outline: none;
            transition: border-color 0.2s;
            font-family: 'Open Sans', sans-serif;
        }
        .form-group input::placeholder { color: rgba(255,255,255,0.25); }
        .form-group input:focus,
        .form-group select:focus { border-color: rgba(245,166,35,0.5); }
        .form-group select option { background: #1a1a1a; color: #fff; }

        /* Gender radio */
        .gender-group {
            display: flex;
            gap: 20px;
            margin-top: 4px;
        }
        .gender-option {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            color: rgba(255,255,255,0.7);
            cursor: pointer;
        }
        .gender-option input[type="radio"] { accent-color: #f5a623; width: 16px; height: 16px; }

        /* Password strength */
        .pw-strength {
            height: 4px;
            border-radius: 4px;
            margin-top: 6px;
            background: rgba(255,255,255,0.08);
            overflow: hidden;
        }
        .pw-strength-bar {
            height: 100%;
            width: 0%;
            border-radius: 4px;
            transition: width 0.3s, background 0.3s;
        }

        .alert-box {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
        }
        .alert-error   { background: rgba(255,80,80,0.12);  border: 1px solid rgba(255,80,80,0.25);  color: #ff6b6b; }
        .alert-success { background: rgba(78,203,141,0.12); border: 1px solid rgba(78,203,141,0.25); color: #4ecb8d; }

        .btn-reg {
            width: 100%;
            background: #f5a623;
            color: #0a0a0a;
            border: none;
            border-radius: 30px;
            padding: 13px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
            margin-top: 8px;
            font-family: 'Montserrat', sans-serif;
        }
        .btn-reg:hover { background: #f7b84e; transform: scale(1.02); }
        .btn-reg:disabled { background: rgba(245,166,35,0.4); cursor: not-allowed; transform: none; }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
            color: rgba(255,255,255,0.2);
            font-size: 12px;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.08);
        }

        @media (max-width: 500px) {
            .reg-card { padding: 28px 20px; }
            .form-row { flex-direction: column; gap: 0; }
        }
    </style>
</head>
<body oncontextmenu="return false;">

    
    <header>
    <div class="top-head container">
        <div class="ml-auto text-right right-p">
            <ul>
                <li class="mr-3">
                    <span class="fa fa-phone">&nbsp;&nbsp;</span>
                    <a href="tel:1800405025">1800-405025</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="container">
        <!-- nav -->
        <nav class="py-3 d-lg-flex">
            <div id="logo">
                <h1><a href="index.php"><span class="fa fa-free-code-camp"></span>Explore India</a></h1>
            </div>

            <label for="drop" class="toggle"><span class="fa fa-bars"></span></label>
            <input type="checkbox" id="drop"/>

<ul class="menu ml-auto mt-1">
    <li class="active"><a href="index.php">Home</a></li>

    <?php if (isset($_SESSION['uemail'])) { ?>

        <?php
        $fname = '';
        $id = $_SESSION['uid'];

        $sql = "SELECT cust_fname FROM customer_details WHERE cust_id=" . intval($id);
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $fname = $row['cust_fname'];
        }
        ?>

        <li><a href="packages.php">Service</a></li>

        <li class="dropdown">
            <button class="dropbtn">
                Hello <?php echo htmlspecialchars($fname); ?>
            </button>

            <div class="dropdown-content">
                <a href="Profile.php">My Profile</a>
                <a href="cust_history.php">History</a>
                <a href="feedback.php">Feedback</a>
                <a href="logout.php">Log Out</a>
            </div>
        </li>

    <?php } else { ?>

        <li><a href="localguide/nice-html/ltr/login.php">Join Us</a></li>
        <li><a href="login.php">Login</a></li>
        <li><a href="registration.php">Registration</a></li>

    <?php } ?>

</ul>
        </nav>
        <!-- //nav -->
    </div>
   
    
   <style>
.dropdown {
    position: relative;
    display: inline-block;
}
.dropbtn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: inherit;
    color: inherit;
    padding: 0;
}
.dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    background-color: #fff;
    min-width: 160px;
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    z-index: 9999;  /* ✅ Sabse upar rahega */
    border-radius: 5px;
}
.dropdown-content a {
    color: #333;
    padding: 10px 16px;
    display: block;
    text-decoration: none;
}
.dropdown-content a:hover {
    background-color: #f1f1f1;
}
.dropdown:hover .dropdown-content {
    display: block;  /* ✅ Hover pe dikhega */
}
</style>
</header>
    
    

<div class="reg-wrapper">
    <div class="reg-card">
        <h2>Create Account</h2>
        <p class="sub">Already have an account? <a href="login.php">Sign In</a></p>

        <?php if ($error): ?>
        <div class="alert-box alert-error"><span class="fa fa-exclamation-circle"></span> <?= $error ?></div>
        <?php endif; ?>

        <form action="registration.php" method="POST" id="regForm">

            <div class="form-row">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="fname" placeholder="Rahul" required value="<?= htmlspecialchars($_POST['fname'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="lname" placeholder="Sharma" required value="<?= htmlspecialchars($_POST['lname'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="rahul@email.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" id="password" placeholder="Min 6 characters" required>
                    <div class="pw-strength"><div class="pw-strength-bar" id="pwBar"></div></div>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Re-enter password" required>
                </div>
            </div>

            <div class="form-group">
                <label>Mobile</label>
                <input type="tel" name="mobile" placeholder="9876543210" pattern="[0-9]{10}" required value="<?= htmlspecialchars($_POST['mobile'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="birthdate" required value="<?= htmlspecialchars($_POST['birthdate'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Gender</label>
                <div class="gender-group">
                    <label class="gender-option">
                        <input type="radio" name="gender" value="Male" <?= (($_POST['gender'] ?? '') === 'Male') ? 'checked' : '' ?>> Male
                    </label>
                    <label class="gender-option">
                        <input type="radio" name="gender" value="Female" <?= (($_POST['gender'] ?? '') === 'Female') ? 'checked' : '' ?>> Female
                    </label>
                    <label class="gender-option">
                        <input type="radio" name="gender" value="Other" <?= (($_POST['gender'] ?? '') === 'Other') ? 'checked' : '' ?>> Other
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" placeholder="Street, Area" required value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>State</label>
                    <select name="state" id="stateSelect" onchange="getCities(this.value)" required>
                        <option value="">Select State</option>
                        <?php
                        $states = mysqli_query($conn, "SELECT * FROM state ORDER BY s_name");
                        foreach ($states as $s) {
                            $sel = (($_POST['state'] ?? '') == $s['s_id']) ? 'selected' : '';
                            echo "<option value='{$s['s_id']}' $sel>{$s['s_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>City</label>
                    <select name="city" id="citySelect" required>
                        <option value="">Select City</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn-reg" id="regBtn">
                <span class="fa fa-envelope"></span> Send OTP & Register
            </button>
        </form>
    </div>
</div>

<?php include('footer.php'); ?>

<script>
// Password strength
document.getElementById('password').addEventListener('input', function() {
    var val = this.value;
    var bar = document.getElementById('pwBar');
    var strength = 0;
    if (val.length >= 6) strength++;
    if (val.length >= 10) strength++;
    if (/[A-Z]/.test(val)) strength++;
    if (/[0-9]/.test(val)) strength++;
    if (/[^A-Za-z0-9]/.test(val)) strength++;
    var colors = ['#ff4444','#ff8800','#f5a623','#88cc00','#4ecb8d'];
    var widths  = ['20%','40%','60%','80%','100%'];
    bar.style.width      = widths[strength-1] || '0%';
    bar.style.background = colors[strength-1] || 'transparent';
});

// City AJAX
function getCities(stateId) {
    if (!stateId) return;
    $.ajax({
        type: 'POST',
        url:  'city.php',
        data: 's_id=' + stateId,
        success: function(html) {
            $('#citySelect').html(html);
        }
    });
}

// Form submit loading state
document.getElementById('regForm').addEventListener('submit', function() {
    var btn = document.getElementById('regBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="fa fa-spinner fa-spin"></span> Sending OTP...';
});
</script>
</body>
</html>
<?php
    error_reporting(E_ALL);
ini_set('display_errors', 1);
include('db.php');
session_start();

if (isset($_SESSION['uemail'])) {
    header('Location: index.php');
    exit;
}

$error = '';

// ── Password Login ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_type'])) {

    if ($_POST['login_type'] === 'password') {
        $email    = trim(mysqli_real_escape_string($conn, $_POST['email']));
        $password = $_POST['password'];

        $res = mysqli_query($conn, "SELECT * FROM customer_details WHERE cust_email='$email'");
        if (mysqli_num_rows($res) === 0) {
            $error = 'No account found with this email. <a href="registration.php">Register here</a>';
        } else {
            $cust = mysqli_fetch_assoc($res);
            if ($cust['is_blocked'] == 1 ) {
                $error = 'Your account has been blocked. Please contact support.';
            } elseif (!password_verify($password, $cust['cust_password'])) {
                $error = 'Incorrect password. <a href="forgetpassword.php">Forgot password?</a>';
            } else {
                $_SESSION['uemail']   = $cust['cust_email'];
                $_SESSION['uname']    = $cust['cust_fname'] . ' ' . $cust['cust_lname'];
                $_SESSION['ucust_id'] = $cust['cust_id'];
                header('Location: index.php');
                exit;
            }
        }

    } elseif ($_POST['login_type'] === 'email_otp') {
        $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
        $res   = mysqli_query($conn, "SELECT * FROM customer_details WHERE cust_email='$email'");
        if (mysqli_num_rows($res) === 0) {
            $error = 'No account found with this email.';
        } else {
            $cust = mysqli_fetch_assoc($res);
             if ($cust['is_blocked'] == 1 ) {
                $error = 'Your account has been blocked. Please contact support.';
            } else {
                $otp    = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                $_SESSION['login_otp'] = [
                    'email'  => $email,
                    'otp'    => $otp,
                    'expiry' => $expiry,
                ];
                require_once 'send_otp.php';
                $sent = sendOTP($email, $cust['cust_fname'], $otp);
                if ($sent) {
                    header('Location: otp_verify.php?type=login');
                    exit;
                } else {
                    $error = 'Failed to send OTP. Try again.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login – Explore India</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css'/>
    <link href="css/style.css" rel='stylesheet' type='text/css'/>
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Montserrat:400,600,700,800" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="js/disable.js"></script>

    <!-- Firebase -->
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-auth-compat.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #0a0a0a; font-family: 'Open Sans', sans-serif; color: #fff; min-height: 100vh; }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 100px 16px 60px;
            background: linear-gradient(160deg, #0a0a0a 0%, #12082a 50%, #0a0a0a 100%);
        }
        .login-card {
            background: #141414;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            padding: 40px 36px;
            width: 100%;
            max-width: 420px;
        }
        .login-card h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 26px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 6px;
        }
        .login-card .sub {
            font-size: 13px;
            color: rgba(255,255,255,0.4);
            margin-bottom: 28px;
        }
        .login-card .sub a { color: #f5a623; text-decoration: none; }

        /* Tab switcher */
        .tab-switcher {
            display: flex;
            background: rgba(255,255,255,0.05);
            border-radius: 12px;
            padding: 4px;
            margin-bottom: 24px;
            gap: 4px;
        }
        .tab-btn {
            flex: 1;
            padding: 9px 6px;
            border: none;
            border-radius: 8px;
            background: transparent;
            color: rgba(255,255,255,0.4);
            font-size: 11.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Open Sans', sans-serif;
        }
        .tab-btn.active {
            background: #f5a623;
            color: #0a0a0a;
        }

        /* Forms */
        .login-form { display: none; }
        .login-form.active { display: block; }

        .form-group { margin-bottom: 16px; }
        .form-group label {
            display: block;
            font-size: 11.5px;
            font-weight: 600;
            letter-spacing: 0.5px;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .form-group input {
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
        .form-group input:focus { border-color: rgba(245,166,35,0.5); }

        .alert-box {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
        }
        .alert-error { background: rgba(255,80,80,0.12); border: 1px solid rgba(255,80,80,0.25); color: #ff6b6b; }

        .btn-login {
            width: 100%;
            background: #f5a623;
            color: #0a0a0a;
            border: none;
            border-radius: 30px;
            padding: 13px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
            font-family: 'Montserrat', sans-serif;
            margin-top: 4px;
        }
        .btn-login:hover { background: #f7b84e; }

        .forgot-link {
            display: block;
            text-align: right;
            font-size: 12px;
            color: rgba(255,255,255,0.35);
            text-decoration: none;
            margin-top: 8px;
        }
        .forgot-link:hover { color: #f5a623; }

        /* Google button */
        .btn-google {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: #fff;
            color: #1a1a1a;
            border: none;
            border-radius: 30px;
            padding: 13px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
            font-family: 'Open Sans', sans-serif;
        }
        .btn-google:hover { background: #f0f0f0; transform: scale(1.02); }
        .btn-google img { width: 20px; height: 20px; }

        .google-note {
            font-size: 12px;
            color: rgba(255,255,255,0.35);
            text-align: center;
            margin-top: 12px;
            line-height: 1.5;
        }

        /* Spinner overlay */
        .spinner-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 16px;
        }
        .spinner-overlay.show { display: flex; }
        .spinner { width: 44px; height: 44px; border: 3px solid rgba(245,166,35,0.2); border-top-color: #f5a623; border-radius: 50%; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner-overlay p { color: #fff; font-size: 14px; }

        @media (max-width: 480px) {
            .login-card { padding: 28px 18px; }
            .tab-btn { font-size: 10.5px; }
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
    
    

<!-- Spinner -->
<div class="spinner-overlay" id="spinnerOverlay">
    <div class="spinner"></div>
    <p>Signing you in...</p>
</div>

<div class="login-wrapper">
    <div class="login-card">
        <h2>Welcome Back 👋</h2>
        <p class="sub">Don't have an account? <a href="registration.php">Sign Up</a></p>

        <?php if ($error): ?>
        <div class="alert-box alert-error"><span class="fa fa-exclamation-circle"></span> <?= $error ?></div>
        <?php endif; ?>

        <!-- Tab Switcher -->
        <div class="tab-switcher">
            <button class="tab-btn active" onclick="switchTab('password')">
                <span class="fa fa-lock"></span> Password
            </button>
            <button class="tab-btn" onclick="switchTab('otp')">
                <span class="fa fa-envelope"></span> Email OTP
            </button>
            <button class="tab-btn" onclick="switchTab('google')">
                <span class="fa fa-google"></span> Google
            </button>
        </div>

        <!-- ① Password Login -->
        <form action="login.php" method="POST" class="login-form active" id="form-password">
            <input type="hidden" name="login_type" value="password">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="rahul@email.com" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
           <a href="forgot_password.php" style="font-size:13px; color:#f5a623;">Forgot Password?</a>
            <button type="submit" class="btn-login" style="margin-top:16px;">
                <span class="fa fa-sign-in"></span> Login
            </button>
        </form>

        <!-- ② Email OTP Login -->
        <form action="login.php" method="POST" class="login-form" id="form-otp">
            <input type="hidden" name="login_type" value="email_otp">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="rahul@email.com" required>
            </div>
            <button type="submit" class="btn-login">
                <span class="fa fa-paper-plane"></span> Send OTP
            </button>
        </form>

        <!-- ③ Google Login -->
        <div class="login-form" id="form-google">
            <button class="btn-google" onclick="signInWithGoogle()">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google">
                Continue with Google
            </button>
            <p class="google-note">We'll create your account automatically if you're new.</p>
        </div>

    </div>
</div>

<?php include('footer.php'); ?>

<script>
// Tab switcher
function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(function(b, i) {
        b.classList.remove('active');
        if (['password','otp','google'][i] === tab) b.classList.add('active');
    });
    document.querySelectorAll('.login-form').forEach(function(f) { f.classList.remove('active'); });
    document.getElementById('form-' + tab).classList.add('active');
}

// Firebase init
const firebaseConfig = {
    apiKey:            "AIzaSyDUwKtcw8tp9_ZPMEdxrh7RCgWVh0ZFb9A",
    authDomain:        "exploreindia-16717.firebaseapp.com",
    projectId:         "exploreindia-16717",
    storageBucket:     "exploreindia-16717.firebasestorage.app",
    messagingSenderId: "545411280305",
    appId:             "1:545411280305:web:7a0feed5597bacf1c92190"
};
firebase.initializeApp(firebaseConfig);

function signInWithGoogle() {
    if (window.AndroidInterface) {
        // Use native Android Google Sign-In
        window.AndroidInterface.launchGoogleSignIn();
    } else {
        // Fallback for laptop browser: use Firebase popup
        var provider = new firebase.auth.GoogleAuthProvider();
        document.getElementById('spinnerOverlay').classList.add('show');

        firebase.auth().signInWithPopup(provider)
            .then(function(result) {
                var user  = result.user;
                var email = user.email;
                var name  = user.displayName || '';
                var parts = name.split(' ');
                var fname = parts[0] || '';
                var lname = parts.slice(1).join(' ') || '';

                // Send to PHP handler
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = 'google_auth.php';
                [
                    { name: 'email', value: email },
                    { name: 'fname', value: fname },
                    { name: 'lname', value: lname },
                    { name: 'uid',   value: user.uid }
                ].forEach(function(field) {
                    var inp = document.createElement('input');
                    inp.type  = 'hidden';
                    inp.name  = field.name;
                    inp.value = field.value;
                    form.appendChild(inp);
                });
                document.body.appendChild(form);
                form.submit();
            })
            .catch(function(err) {
                document.getElementById('spinnerOverlay').classList.remove('show');
                alert('Google Sign-in failed: ' + err.message);
            });
    }
}

// This function is called by the Android App natively after user picks their Google account
function handleGoogleUserNative(email, displayName, uid) {
    var parts = displayName.split(' ');
    var fname = parts[0] || '';
    var lname = parts.slice(1).join(' ') || '';

    // Send to PHP handler
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = 'google_auth.php';
    [
        { name: 'email', value: email },
        { name: 'fname', value: fname },
        { name: 'lname', value: lname },
        { name: 'uid',   value: uid }
    ].forEach(function(field) {
        var inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = field.name;
        inp.value = field.value;
        form.appendChild(inp);
    });
    document.body.appendChild(form);
    form.submit();
}
</script>
</body>
</html>
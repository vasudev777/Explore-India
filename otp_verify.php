<?php
include('db.php');
session_start();

$type  = $_GET['type'] ?? 'register'; // register | login
$error = '';

// ── Handle OTP submit ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered = trim($_POST['otp'] ?? '');

    if ($type === 'register') {
        $data = $_SESSION['reg_data'] ?? null;
        if (!$data) { header('Location: registration.php'); exit; }

        if ($entered !== $data['otp']) {
            $error = 'Invalid OTP. Please try again.';
        } elseif (strtotime($data['expiry']) < time()) {
            $error = 'OTP expired. <a href="registration.php">Register again</a>';
        } else {
            // Insert customer
            $stmt = $conn->prepare("INSERT INTO customer_details
                (cust_fname, cust_lname, cust_gender, cust_email, cust_password,
                 cust_mobile, cust_address, cust_birthdate, cust_state, cust_city,
                 is_verified, is_blocked)
                VALUES (?,?,?,?,?,?,?,?,?,?,1,0)");
            $stmt->bind_param('ssssssssss',
                $data['fname'], $data['lname'], $data['gender'],
                $data['email'], $data['password'], $data['mobile'],
                $data['address'], $data['bdate'],
                $data['state'], $data['city']
            );
            $stmt->execute();
            $newId = $conn->insert_id;

            // Auto login
            $_SESSION['uemail']   = $data['email'];
            $_SESSION['uname']    = $data['fname'] . ' ' . $data['lname'];
            $_SESSION['ucust_id'] = $newId;
            unset($_SESSION['reg_data']);

            header('Location: index.php');
            exit;
        }

    } elseif ($type === 'login') {
        $loginData = $_SESSION['login_otp'] ?? null;
        if (!$loginData) { header('Location: login.php'); exit; }

        if ($entered !== $loginData['otp']) {
            $error = 'Invalid OTP. Please try again.';
        } elseif (strtotime($loginData['expiry']) < time()) {
            $error = 'OTP expired. <a href="login.php">Try again</a>';
        } else {
            // Check blocked
            $email = $loginData['email'];
            $res   = mysqli_query($conn, "SELECT * FROM customer_details WHERE cust_email='$email' AND is_blocked='0'");
            if (mysqli_num_rows($res) === 0) {
                $error = 'Your account has been blocked. Contact support.';
            } else {
                $cust = mysqli_fetch_assoc($res);
                $_SESSION['uemail']   = $cust['cust_email'];
                $_SESSION['uname']    = $cust['cust_fname'] . ' ' . $cust['cust_lname'];
                $_SESSION['ucust_id'] = $cust['cust_id'];
                unset($_SESSION['login_otp']);
                header('Location: index.php');
                exit;
            }
        }
    }
}

// ── Resend OTP ──
if (isset($_GET['resend'])) {
    if ($type === 'register' && isset($_SESSION['reg_data'])) {
        $otp    = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        $_SESSION['reg_data']['otp']    = $otp;
        $_SESSION['reg_data']['expiry'] = $expiry;
        $email = $_SESSION['reg_data']['email'];
        $name  = $_SESSION['reg_data']['fname'];
        require_once 'send_otp.php';
        sendOTP($email, $name, $otp);
    } elseif ($type === 'login' && isset($_SESSION['login_otp'])) {
        $otp    = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        $_SESSION['login_otp']['otp']    = $otp;
        $_SESSION['login_otp']['expiry'] = $expiry;
        $email = $_SESSION['login_otp']['email'];
        require_once 'send_otp.php';
        sendOTP($email, 'User', $otp);
    }
    header("Location: otp_verify.php?type=$type&resent=1");
    exit;
}

$maskedEmail = '';
if ($type === 'register' && isset($_SESSION['reg_data']['email'])) {
    $e = $_SESSION['reg_data']['email'];
    $maskedEmail = substr($e, 0, 3) . '****' . strstr($e, '@');
} elseif ($type === 'login' && isset($_SESSION['login_otp']['email'])) {
    $e = $_SESSION['login_otp']['email'];
    $maskedEmail = substr($e, 0, 3) . '****' . strstr($e, '@');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Verify OTP – Explore India</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css'/>
    <link href="css/style.css" rel='stylesheet' type='text/css'/>
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Montserrat:400,600,700,800" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #0a0a0a; font-family: 'Open Sans', sans-serif; color: #fff; min-height: 100vh; }

        .otp-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 100px 16px 60px;
            background: linear-gradient(160deg, #0a0a0a 0%, #12082a 50%, #0a0a0a 100%);
        }
        .otp-card {
            background: #141414;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            padding: 44px 36px;
            width: 100%;
            max-width: 420px;
            text-align: center;
        }
        .otp-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: rgba(245,166,35,0.12);
            border: 2px solid rgba(245,166,35,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 28px;
        }
        .otp-card h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 22px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 8px;
        }
        .otp-card p {
            font-size: 13px;
            color: rgba(255,255,255,0.45);
            margin-bottom: 28px;
            line-height: 1.6;
        }
        .otp-card p span { color: #f5a623; font-weight: 600; }

        /* OTP boxes */
        .otp-inputs {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 24px;
        }
        .otp-inputs input {
            width: 50px;
            height: 56px;
            background: rgba(255,255,255,0.06);
            border: 1.5px solid rgba(255,255,255,0.12);
            border-radius: 12px;
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            color: #fff;
            outline: none;
            transition: border-color 0.2s;
            font-family: 'Montserrat', sans-serif;
        }
        .otp-inputs input:focus { border-color: #f5a623; }

        .alert-box {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            text-align: left;
        }
        .alert-error   { background: rgba(255,80,80,0.12);  border: 1px solid rgba(255,80,80,0.25);  color: #ff6b6b; }
        .alert-success { background: rgba(78,203,141,0.12); border: 1px solid rgba(78,203,141,0.25); color: #4ecb8d; }

        .btn-verify {
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
        }
        .btn-verify:hover { background: #f7b84e; }

        .timer { font-size: 13px; color: rgba(255,255,255,0.35); margin-top: 16px; }
        .timer span { color: #f5a623; font-weight: 600; }
        .resend-link { color: #f5a623; text-decoration: none; font-size: 13px; }
        .resend-link:hover { text-decoration: underline; }
    </style>
</head>
<body oncontextmenu="return false;">
<?php include('header.php'); ?>

<div class="otp-wrapper">
    <div class="otp-card">
        <div class="otp-icon">✉️</div>
        <h2>Verify Your Email</h2>
        <p>We've sent a 6-digit OTP to<br><span><?= $maskedEmail ?></span><br>Valid for 10 minutes.</p>

        <?php if ($error): ?>
        <div class="alert-box alert-error"><span class="fa fa-exclamation-circle"></span> <?= $error ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['resent'])): ?>
        <div class="alert-box alert-success"><span class="fa fa-check-circle"></span> New OTP sent!</div>
        <?php endif; ?>

        <form action="otp_verify.php?type=<?= $type ?>" method="POST" id="otpForm">
            <div class="otp-inputs">
                <input type="text" maxlength="1" class="otp-box" id="o1" autofocus>
                <input type="text" maxlength="1" class="otp-box" id="o2">
                <input type="text" maxlength="1" class="otp-box" id="o3">
                <input type="text" maxlength="1" class="otp-box" id="o4">
                <input type="text" maxlength="1" class="otp-box" id="o5">
                <input type="text" maxlength="1" class="otp-box" id="o6">
            </div>
            <input type="hidden" name="otp" id="otpHidden">
            <button type="submit" class="btn-verify" id="verifyBtn">
                <span class="fa fa-check-circle"></span> Verify OTP
            </button>
        </form>

        <p class="timer" id="timerTxt">Resend OTP in <span id="countdown">60</span>s</p>
        <a href="otp_verify.php?type=<?= $type ?>&resend=1" class="resend-link" id="resendLink" style="display:none;">
            <span class="fa fa-refresh"></span> Resend OTP
        </a>
    </div>
</div>

<?php include('footer.php'); ?>

<script>
// OTP box auto-focus
var boxes = document.querySelectorAll('.otp-box');
boxes.forEach(function(box, i) {
    box.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g,'');
        if (this.value && i < 5) boxes[i+1].focus();
        updateHidden();
    });
    box.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace' && !this.value && i > 0) boxes[i-1].focus();
    });
    // Paste support
    box.addEventListener('paste', function(e) {
        e.preventDefault();
        var text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'');
        for (var j = 0; j < 6 && j < text.length; j++) {
            boxes[j].value = text[j];
        }
        updateHidden();
        if (text.length >= 6) document.getElementById('verifyBtn').focus();
    });
});

function updateHidden() {
    var val = '';
    boxes.forEach(function(b){ val += b.value; });
    document.getElementById('otpHidden').value = val;
}

// Countdown timer
var seconds = 60;
var timer = setInterval(function() {
    seconds--;
    document.getElementById('countdown').textContent = seconds;
    if (seconds <= 0) {
        clearInterval(timer);
        document.getElementById('timerTxt').style.display  = 'none';
        document.getElementById('resendLink').style.display = 'block';
    }
}, 1000);

// Submit loading
document.getElementById('otpForm').addEventListener('submit', function() {
    updateHidden();
    var btn = document.getElementById('verifyBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="fa fa-spinner fa-spin"></span> Verifying...';
});
</script>
</body>
</html>
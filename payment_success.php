<?php
    error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include('db.php');
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
if (!isset($_SESSION['uemail'])) { header('Location: login.php'); exit; }

$cust_id   = intval($_SESSION['ucust_id']);
$pay_id    = htmlspecialchars($_POST['razorpay_payment_id'] ?? '');
$pay_type  = htmlspecialchars($_POST['pay_type'] ?? $_SESSION['pay_type'] ?? '');
$total     = floatval($_POST['total'] ?? $_SESSION['pay_total'] ?? 0);
$date      = htmlspecialchars($_POST['date'] ?? $_SESSION['pay_date'] ?? date('Y-m-d'));
$lg        = intval($_POST['lg'] ?? $_SESSION['pay_lg'] ?? 0);

// Get customer
$r = mysqli_query($conn, "SELECT * FROM customer_details WHERE cust_id=$cust_id");
$cust = mysqli_fetch_assoc($r);
$cust_name  = $cust['cust_fname'] . ' ' . $cust['cust_lname'];
$cust_email = $cust['cust_email'];

// Generate booking ref
$booking_ref = strtoupper(substr($pay_type, 0, 3)) . date('ymd') . rand(100, 999);

// Build details & save to DB based on type
if ($pay_type === 'predefine') {
    $packid   = intval($_POST['packid'] ?? $_SESSION['pay_packid'] ?? 0);
    $packname = htmlspecialchars($_POST['packname'] ?? $_SESSION['pay_packname'] ?? '');
    $details  = "Package: $packname | Date: $date | Payment ID: $pay_id";
    $title    = $packname;

    // Save to booking table
    mysqli_query($conn, "INSERT INTO predefine_booking (cust_id, pa_id, date, payment_id, amount, status, booking_ref)
        VALUES ($cust_id, $packid, '$date', '$pay_id', $total, 'Confirmed', '$booking_ref')
        ON DUPLICATE KEY UPDATE status='Confirmed'");

    // Save to local_guide_request for Guide Dashboard
    $pack_res = mysqli_query($conn, "SELECT s_id, h_id FROM package WHERE pa_id=$packid");
    $pack_data = mysqli_fetch_assoc($pack_res);
    $s_id = $pack_data ? intval($pack_data['s_id']) : 0;
    $h_id = $pack_data ? $pack_data['h_id'] : '';
    if ($lg > 0 && $s_id > 0) {
        $h_id_esc = mysqli_real_escape_string($conn, $h_id);
        mysqli_query($conn, "INSERT INTO local_guide_request (localg_id, cust_id, s_id, h_id, date)
            VALUES ($lg, $cust_id, $s_id, '$h_id_esc', '$date')");
    }

} elseif ($pay_type === 'customize') {
    $state  = htmlspecialchars($_POST['state']  ?? $_SESSION['pay_state']  ?? '');
    $day    = htmlspecialchars($_POST['day']    ?? $_SESSION['pay_day']    ?? '');
    $h_name = htmlspecialchars($_POST['h_name'] ?? $_SESSION['pay_h_name'] ?? '');
    $hid    = htmlspecialchars($_POST['hid']    ?? $_SESSION['pay_hid']    ?? '');
    $details = "State: $state | Days: $day | Hotel: $h_name | Date: $date | Payment ID: $pay_id";
    $title   = "Custom Package - $state";

    $hid_esc = mysqli_real_escape_string($conn, $hid);
    mysqli_query($conn, "INSERT INTO customize_booking (cust_id, h_id, localg_id, date, day, amount, payment_id, status, booking_ref)
        VALUES ($cust_id, '$hid_esc', $lg, '$date', '$day', $total, '$pay_id', 'Confirmed', '$booking_ref')");

    // Save to local_guide_request for Guide Dashboard
    $state_esc = mysqli_real_escape_string($conn, $state);
    $st_res = mysqli_query($conn, "SELECT s_id FROM state WHERE s_name='$state_esc' LIMIT 1");
    $st_row = mysqli_fetch_assoc($st_res);
    $s_id = $st_row ? intval($st_row['s_id']) : 0;
    if ($lg > 0 && $s_id > 0) {
        mysqli_query($conn, "INSERT INTO local_guide_request (localg_id, cust_id, s_id, h_id, date)
            VALUES ($lg, $cust_id, $s_id, '$hid_esc', '$date')");
    }

} else { // transport
    $from     = htmlspecialchars($_POST['from'] ?? '');
    $to       = htmlspecialchars($_POST['to']   ?? '');
    $details  = "Type: $pay_type | $from → $to | Date: $date | Payment ID: $pay_id";
    $title    = ucfirst($pay_type) . " - $from to $to";

    $from_esc = mysqli_real_escape_string($conn, $from);
    $to_esc   = mysqli_real_escape_string($conn, $to);
    $det_esc  = mysqli_real_escape_string($conn, json_encode($_POST));
    $pay_id_esc  = mysqli_real_escape_string($conn, $pay_id);
$booking_esc = mysqli_real_escape_string($conn, $booking_ref);
mysqli_query($conn, "INSERT INTO transport_bookings (cust_id, type, from_city, to_city, travel_date, details, fare, payment_id, booking_ref, status)
    VALUES ($cust_id, '$pay_type', '$from_esc', '$to_esc', '$date', '$det_esc', $total, '$pay_id_esc', '$booking_esc', 'Confirmed')");
}

// Send Email Receipt with PHPMailer
$mail_sent = false;
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
    $mail->addAddress($cust_email, $cust_name);
    $mail->isHTML(true);
    $mail->Subject = ' Booking Confirmed : ' . $title . ' | Explore India';

    // Email HTML
    $mail->Body = '
    <!DOCTYPE html>
    <html>
    <head><meta charset="utf-8"><style>
        body { font-family: Arial, sans-serif; background: #f5f6fa; margin: 0; padding: 20px; }
        .container { max-width: 580px; margin: 0 auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #0a0a0a, #1a1a2e); padding: 32px 28px; text-align: center; }
        .header h1 { font-size: 24px; color: #fff; margin: 0 0 6px; }
        .header p { color: rgba(255,255,255,0.5); font-size: 13px; margin: 0; }
        .header .logo { font-size: 28px; margin-bottom: 12px; }
        .success-banner { background: #f0fff8; border-left: 4px solid #2ecc9a; padding: 14px 20px; margin: 20px; border-radius: 0 8px 8px 0; }
        .success-banner h3 { color: #1a1a1a; font-size: 15px; margin: 0 0 4px; }
        .success-banner p { color: #666; font-size: 13px; margin: 0; }
        .ref-box { text-align: center; padding: 16px; background: #f8f9fa; margin: 0 20px 20px; border-radius: 10px; }
        .ref-box .ref-label { font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 1px; }
        .ref-box .ref-num { font-size: 22px; font-weight: 800; color: #f5a623; letter-spacing: 2px; margin-top: 4px; }
        .details { margin: 0 20px 20px; }
        .details h4 { font-size: 13px; font-weight: 700; text-transform: uppercase; color: #888; letter-spacing: 1px; margin-bottom: 10px; }
        .row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
        .row:last-child { border: none; }
        .row .label { color: #888; }
        .row .value { font-weight: 600; color: #1a1a1a; }
        .total-row { background: #f8f9fa; border-radius: 10px; padding: 14px 16px; margin: 0 20px 20px; display: flex; justify-content: space-between; align-items: center; }
        .total-row .tlabel { font-size: 13px; font-weight: 700; color: #888; text-transform: uppercase; }
        .total-row .tamount { font-size: 24px; font-weight: 800; color: #f5a623; }
        .footer { background: #0a0a0a; padding: 20px; text-align: center; }
        .footer p { color: rgba(255,255,255,0.35); font-size: 12px; margin: 0; }
        .footer a { color: #f5a623; text-decoration: none; }
    </style></head>
    <body>
    <div class="container">
        <div class="header">
            <div class="logo">🇮🇳</div>
            <h1>Explore India</h1>
            <p>Your journey starts here</p>
        </div>

        <div class="success-banner">
            <h3>✅ Booking Confirmed!</h3>
            <p>Dear ' . htmlspecialchars($cust_name) . ', your booking has been confirmed successfully.</p>
        </div>

        <div class="ref-box">
            <div class="ref-label">Booking Reference</div>
            <div class="ref-num">' . $booking_ref . '</div>
        </div>

        <div class="details">
            <h4>Booking Details</h4>
            <div class="row"><span class="label">Booking Type </span><span class="value">' . ucfirst($pay_type) . '</span></div>
            <div class="row"><span class="label">Name </span><span class="value">' . htmlspecialchars($cust_name) . '</span></div>
            <div class="row"><span class="label">Email </span><span class="value">' . htmlspecialchars($cust_email) . '</span></div>
            <div class="row"><span class="label">Travel Date </span><span class="value">' . date('D, d M Y', strtotime($date)) . '</span></div>
            <div class="row"><span class="label">Payment ID </span><span class="value">' . $pay_id . '</span></div>
            <div class="row"><span class="label">Status </span><span class="value" style="color:#2ecc9a;">✅ Confirmed</span></div>
        </div>

        <div class="total-row">
            <span class="tlabel">Amount Paid  </span>
            <span class="tamount">₹' . number_format($total) . '</span>
        </div>

        <div class="footer">
            <p>Thank you for choosing <a href="#">Explore India</a>! 🙏</p>
            <p style="margin-top:6px;">For support: <a href="mailto:exploreindiaplaner@gmail.com">exploreindiaplaner@gmail.com</a> | 1800-405025</p>
        </div>
    </div>
    </body></html>';

    $mail->send();
    $mail_sent = true;
} catch (Exception $e) {
    $mail_sent = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payment Successful – Explore India</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css'/>
    <link href="css/style.css" rel='stylesheet' type='text/css'/>
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Montserrat:400,600,700,800" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f5f6fa; font-family: 'Open Sans', sans-serif; color: #1a1a1a; }

        .page-hero { background: linear-gradient(160deg, #0a0a0a 0%, #0a1a0a 50%, #0a0a0a 100%); padding: 100px 20px 50px; text-align: center; }
        .hero-badge { display: inline-block; font-size: 10px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; color: rgba(255,255,255,0.4); border: 1px solid rgba(255,255,255,0.12); padding: 5px 14px; border-radius: 20px; margin-bottom: 14px; }
        .page-hero h1 { font-family: 'Montserrat', sans-serif; font-size: clamp(26px, 5vw, 46px); font-weight: 900; color: #fff; margin-bottom: 8px; text-transform: none !important; }
        .page-hero h1 span { color: #5ecfa8; }
        .page-hero p { font-size: 14px; color: rgba(255,255,255,0.4); margin-bottom: 20px; }
        .ref-badge { display: inline-flex; align-items: center; gap: 10px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15); border-radius: 30px; padding: 10px 22px; }
        .ref-label { font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: rgba(255,255,255,0.4); }
        .ref-num { font-family: 'Montserrat', sans-serif; font-size: 20px; font-weight: 800; color: #5ecfa8; letter-spacing: 2px; }
        .copy-btn { background: none; border: none; color: rgba(255,255,255,0.3); cursor: pointer; font-size: 14px; padding: 0; }
        .copy-btn:hover { color: #5ecfa8; }

        .content-section { padding: 40px 20px 80px; }
        .content-inner { max-width: 520px; margin: 0 auto; }

        .success-card { background: #fff; border: 1.5px solid #e9ecef; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 40px rgba(0,0,0,0.08); margin-bottom: 20px; }

        .success-banner { background: #f0fff8; border-bottom: 1px solid #c3f0df; padding: 18px 24px; display: flex; align-items: center; gap: 14px; }
        .s-icon { font-size: 28px; animation: popIn 0.5s cubic-bezier(0.175,0.885,0.32,1.275); }
        @keyframes popIn { 0% { transform: scale(0); } 100% { transform: scale(1); } }
        .s-text h4 { font-family: 'Montserrat', sans-serif; font-size: 15px; font-weight: 700; color: #1a1a1a; margin-bottom: 3px; text-transform: none !important; }
        .s-text p { font-size: 12px; color: #666; margin: 0; }

        .receipt-body { padding: 20px 24px; }
        .r-row { display: flex; justify-content: space-between; padding: 9px 0; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
        .r-row:last-child { border: none; }
        .r-label { color: #888; font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; }
        .r-value { font-weight: 600; color: #1a1a1a; text-align: right; }
        .r-value.green { color: #2ecc9a; }
        .r-value.orange { color: #f5a623; font-family: 'Montserrat', sans-serif; font-size: 18px; font-weight: 800; }

        .email-note { background: #f0f8ff; border: 1px solid rgba(94,160,255,0.25); border-radius: 10px; padding: 12px 16px; font-size: 12px; color: #5ea0ff; display: flex; align-items: center; gap: 8px; margin-bottom: 20px; }

        .action-btns { display: flex; gap: 12px; }
        .btn-action { flex: 1; padding: 13px; border-radius: 12px; font-size: 14px; font-weight: 700; font-family: 'Montserrat', sans-serif; cursor: pointer; border: none; transition: transform 0.2s; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 7px; text-transform: none !important; }
        .btn-action:hover { transform: translateY(-2px); text-decoration: none; }
        .btn-home { background: #f0f0f0; color: #1a1a1a; }
        .btn-history { background: linear-gradient(135deg, #5ecfa8, #3ab88a); color: #000; }

        .stats-section { background: #0a0a0a; padding: 50px 20px; }
        .stats-row { display: flex; justify-content: center; gap: 40px; flex-wrap: wrap; }
        .stat-item { text-align: center; }
        .stat-num { font-family: 'Montserrat', sans-serif; font-size: 28px; font-weight: 800; }
        .stat-num.blue { color: #5ea0ff; } .stat-num.orange { color: #f5a623; } .stat-num.green { color: #5ecfa8; }
        .stat-label { font-size: 12px; color: rgba(255,255,255,0.35); margin-top: 4px; }

        @media (max-width: 500px) { .action-btns { flex-direction: column; } }
    </style>
</head>
<body oncontextmenu="return false;">
<?php include('header.php'); ?>

<div class="page-hero">
    <div class="hero-badge">✅ Payment Successful</div>
    <h1>Payment <span>Successful!</span></h1>
    <p>Your booking has been confirmed and receipt sent to your email.</p>
    <div class="ref-badge">
        <span class="ref-label">Booking Ref</span>
        <span class="ref-num" id="refNum"><?= $booking_ref ?></span>
        <button class="copy-btn" onclick="copyRef()" title="Copy"><span class="fa fa-copy"></span></button>
    </div>
</div>

<div class="content-section">
    <div class="content-inner">

        <div class="success-card">
            <div class="success-banner">
                <div class="s-icon">🎉</div>
                <div class="s-text">
                    <h4>Booking Confirmed!</h4>
                    <p>Payment ID: <?= $pay_id ?></p>
                </div>
            </div>
            <div class="receipt-body">
                <div class="r-row"><span class="r-label">Booking Ref</span><span class="r-value"><?= $booking_ref ?></span></div>
                <div class="r-row"><span class="r-label">Customer</span><span class="r-value"><?= htmlspecialchars($cust_name) ?></span></div>
                <div class="r-row"><span class="r-label">Booking Type</span><span class="r-value"><?= ucfirst($pay_type) ?></span></div>
                <div class="r-row"><span class="r-label">Travel Date</span><span class="r-value"><?= date('D, d M Y', strtotime($date)) ?></span></div>
                <div class="r-row"><span class="r-label">Payment ID</span><span class="r-value"><?= $pay_id ?></span></div>
                <div class="r-row"><span class="r-label">Status</span><span class="r-value green">✅ Confirmed</span></div>
                <div class="r-row"><span class="r-label">Amount Paid</span><span class="r-value orange">₹<?= number_format($total) ?></span></div>
            </div>
        </div>

        <?php if ($mail_sent): ?>
        <div class="email-note">
            <span class="fa fa-envelope"></span>
            Receipt sent to <strong><?= htmlspecialchars($cust_email) ?></strong>
        </div>
        <?php endif; ?>

        <div class="action-btns">
            <a href="index.php" class="btn-action btn-home"><span class="fa fa-home"></span> Home</a>
            <a href="cust_history.php" class="btn-action btn-history"><span class="fa fa-history"></span> My Bookings</a>
        </div>
    </div>
</div>

<div class="stats-section">
    <div class="stats-row">
        <div class="stat-item"><div class="stat-num blue">500+</div><div class="stat-label">Flight Routes</div></div>
        <div class="stat-item"><div class="stat-num orange">1000+</div><div class="stat-label">Train Routes</div></div>
        <div class="stat-item"><div class="stat-num green">40</div><div class="stat-label">Cities Covered</div></div>
    </div>
</div>

<?php include('footer.php'); ?>

<script>
function copyRef() {
    var text = document.getElementById('refNum').textContent;
    navigator.clipboard.writeText(text).then(function() {
        var btn = document.querySelector('.copy-btn');
        btn.innerHTML = '<span class="fa fa-check" style="color:#5ecfa8;"></span>';
        setTimeout(function() { btn.innerHTML = '<span class="fa fa-copy"></span>'; }, 2000);
    });
}
</script>
</body>
</html>
<?php
include('db.php');
session_start();
if (!isset($_SESSION['uemail'])) { header('Location: login.php'); exit; }

$cust_id = intval($_SESSION['ucust_id']);
$sql = "SELECT * FROM customer_details WHERE cust_id=$cust_id";
$result = mysqli_query($conn, $sql);
$cust = mysqli_fetch_assoc($result);

$total   = floatval($_POST['total']  ?? 0);
$date    = htmlspecialchars($_POST['date']   ?? '');
$day     = htmlspecialchars($_POST['day']    ?? '');
$state   = htmlspecialchars($_POST['state']  ?? '');
$h_name  = htmlspecialchars($_POST['h_name'] ?? '');
$h_price = floatval($_POST['h_price'] ?? 0);
$hid     = htmlspecialchars($_POST['hid']    ?? '');
$lg      = intval($_POST['lg'] ?? 0);

$_SESSION['pay_type']    = 'customize';
$_SESSION['pay_total']   = $total;
$_SESSION['pay_date']    = $date;
$_SESSION['pay_day']     = $day;
$_SESSION['pay_state']   = $state;
$_SESSION['pay_h_name']  = $h_name;
$_SESSION['pay_h_price'] = $h_price;
$_SESSION['pay_hid']     = $hid;
$_SESSION['pay_lg']      = $lg;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payment – Custom Package – Explore India</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css'/>
    <link href="css/style.css" rel='stylesheet' type='text/css'/>
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Montserrat:400,600,700,800" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f5f6fa; font-family: 'Open Sans', sans-serif; color: #1a1a1a; }

        .page-hero { background: linear-gradient(160deg, #0a0a0a 0%, #0d1f1a 50%, #0a0a0a 100%); padding: 100px 20px 50px; text-align: center; }
        .hero-badge { display: inline-block; font-size: 10px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; color: rgba(255,255,255,0.4); border: 1px solid rgba(255,255,255,0.12); padding: 5px 14px; border-radius: 20px; margin-bottom: 14px; }
        .page-hero h1 { font-family: 'Montserrat', sans-serif; font-size: clamp(24px, 5vw, 42px); font-weight: 900; color: #fff; margin-bottom: 8px; text-transform: none !important; }
        .page-hero h1 span { color: #5ecfa8; }
        .page-hero p { font-size: 14px; color: rgba(255,255,255,0.4); }

        .content-section { padding: 40px 20px 80px; }
        .content-inner { max-width: 520px; margin: 0 auto; }

        .pay-card { background: #fff; border: 1.5px solid #e9ecef; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 40px rgba(0,0,0,0.08); }
        .pay-header { padding: 20px 24px 16px; border-bottom: 1px solid #e9ecef; display: flex; align-items: center; gap: 12px; }
        .pay-icon { width: 42px; height: 42px; border-radius: 12px; background: rgba(94,207,168,0.1); border: 1px solid rgba(94,207,168,0.25); display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .pay-header h3 { font-family: 'Montserrat', sans-serif; font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0; text-transform: none !important; }
        .pay-header p { font-size: 12px; color: #888; margin: 0; }
        .pay-body { padding: 22px 24px; }

        .order-summary { background: #f0fff8; border: 1px solid rgba(94,207,168,0.2); border-radius: 12px; padding: 16px 18px; margin-bottom: 20px; }
        .summary-row { display: flex; justify-content: space-between; padding: 7px 0; border-bottom: 1px solid rgba(0,0,0,0.05); font-size: 13px; }
        .summary-row:last-child { border: none; }
        .summary-label { color: #888; }
        .summary-value { font-weight: 600; color: #1a1a1a; }
        .summary-row.total .summary-label { font-weight: 700; color: #1a1a1a; font-size: 14px; }
        .summary-row.total .summary-value { font-family: 'Montserrat', sans-serif; font-size: 22px; font-weight: 800; color: #5ecfa8; }

        .cust-info { background: #f8f9fa; border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; }
        .cust-row { display: flex; gap: 10px; align-items: center; font-size: 13px; color: #555; padding: 5px 0; }
        .cust-row .fa { color: #5ecfa8; width: 16px; }

        .divider { border: none; border-top: 1px solid #e9ecef; margin: 20px 0; }

        .btn-pay { width: 100%; background: linear-gradient(135deg, #5ecfa8, #3ab88a); border: none; border-radius: 12px; color: #000; font-size: 15px; font-weight: 700; font-family: 'Montserrat', sans-serif; padding: 15px; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; text-transform: none !important; }
        .btn-pay:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(94,207,168,0.4); }
        .secure-note { text-align: center; font-size: 11px; color: #aaa; margin-top: 12px; }
        .secure-note .fa { color: #2ecc9a; margin-right: 4px; }
    </style>
</head>
<body oncontextmenu="return false;">
<?php include('header.php'); ?>

<div class="page-hero">
    <div class="hero-badge">💳 Secure Payment</div>
    <h1>Complete Your <span>Booking</span></h1>
    <p>Powered by Razorpay — 100% Secure</p>
</div>

<div class="content-section">
    <div class="content-inner">
        <div class="pay-card">
            <div class="pay-header">
                <div class="pay-icon">✨</div>
                <div>
                    <h3>Custom Package</h3>
                    <p><?= $state ?> · <?= $day ?> Days · <?= date('d M Y', strtotime($date)) ?></p>
                </div>
            </div>
            <div class="pay-body">
                <div class="order-summary">
                    <div class="summary-row"><span class="summary-label">State</span><span class="summary-value"><?= $state ?></span></div>
                    <div class="summary-row"><span class="summary-label">Duration</span><span class="summary-value"><?= $day ?> Days</span></div>
                    <div class="summary-row"><span class="summary-label">Travel Date</span><span class="summary-value"><?= date('d M Y', strtotime($date)) ?></span></div>
                    <div class="summary-row"><span class="summary-label">Hotel</span><span class="summary-value"><?= $h_name ?></span></div>
                    <div class="summary-row total"><span class="summary-label">Total Amount</span><span class="summary-value">₹<?= number_format($total) ?></span></div>
                </div>

                <div class="cust-info">
                    <div class="cust-row"><span class="fa fa-user"></span> <?= htmlspecialchars($cust['cust_fname'] . ' ' . $cust['cust_lname']) ?></div>
                    <div class="cust-row"><span class="fa fa-envelope"></span> <?= htmlspecialchars($cust['cust_email']) ?></div>
                    <div class="cust-row"><span class="fa fa-phone"></span> <?= htmlspecialchars($cust['cust_mobile']) ?></div>
                </div>

                <hr class="divider">
                <button class="btn-pay" id="rzp-button">
                    <span class="fa fa-lock"></span> &nbsp; Pay ₹<?= number_format($total) ?> Now
                </button>
                <p class="secure-note"><span class="fa fa-shield"></span> 256-bit SSL Encrypted &nbsp;|&nbsp; <span class="fa fa-check-circle"></span> Instant Confirmation</p>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
var options = {
    key: "rzp_test_SnYyCiVFFiZhv3",
    amount: <?= $total * 100 ?>,
    currency: "INR",
    name: "Explore India",
    description: "Custom Package - <?= addslashes($state) ?>",
    image: "images/logo.png",
    handler: function(response) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'payment_success.php';
        var fields = {
            'razorpay_payment_id': response.razorpay_payment_id,
            'pay_type': 'customize',
            'total': '<?= $total ?>',
            'date': '<?= $date ?>',
            'day': '<?= $day ?>',
            'state': '<?= addslashes($state) ?>',
            'h_name': '<?= addslashes($h_name) ?>',
            'hid': '<?= $hid ?>',
            'lg': '<?= $lg ?>'
        };
        for (var key in fields) {
            var input = document.createElement('input');
            input.type = 'hidden'; input.name = key; input.value = fields[key];
            form.appendChild(input);
        }
        document.body.appendChild(form);
        form.submit();
    },
    prefill: { name: "<?= addslashes($cust['cust_fname'].' '.$cust['cust_lname']) ?>", email: "<?= addslashes($cust['cust_email']) ?>", contact: "<?= addslashes($cust['cust_mobile']) ?>" },
    theme: { color: "#5ecfa8" },
    modal: { ondismiss: function() { alert('Payment cancelled.'); } }
};
var rzp = new Razorpay(options);
document.getElementById('rzp-button').onclick = function(e) { rzp.open(); e.preventDefault(); };
</script>
</body>
</html>